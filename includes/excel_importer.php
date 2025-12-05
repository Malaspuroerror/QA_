<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import an uploaded teacher Excel file into a sanitized table name.
 *
 * @param string $relativePath Path relative to project root (e.g. 'uploads/teacher_files/123_file.xlsx')
 * @param PDO $pdo Database connection object
 * @return array ['success' => bool, 'rows' => int, 'message' => string]
 */
function import_excel_file(string $relativePath, PDO $pdo): array
{
    $projectRoot = realpath(__DIR__ . '/..');
    $fullPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

    if (!file_exists($fullPath)) {
        return ['success' => false, 'rows' => 0, 'message' => "File not found: $fullPath"];
    }

    // Column indices and start row (same mapping used in original script)
    $NAME_COL = 1; // Column B
    $Q1_COL = 5;   // Column F
    $Q2_COL = 9;   // Column J
    $Q3_COL = 13;  // Column N
    $Q4_COL = 17;  // Column R
    $FINAL_COL = 21; // Column V
    $DATA_START_ROW = 13; // 1-based row index for spreadsheet, we'll convert when reading array

    try {
        $spreadsheet = IOFactory::load($fullPath);
        // Prefer SUMMARY sheet if available
        $worksheet = $spreadsheet->getSheetByName('SUMMARY');
        if (!$worksheet) {
            $worksheet = $spreadsheet->getActiveSheet();
        }

        $highestRow = $worksheet->getHighestRow();
        $highestColumnLetter = $worksheet->getHighestColumn();
        $range = 'A1:' . max($highestColumnLetter, 'V') . $highestRow;
        $dataArray = $worksheet->rangeToArray($range, null, true, true, false);

        // Build a sanitized table name from file base name
        $base = pathinfo($relativePath, PATHINFO_FILENAME);
        $tableName = preg_replace('/[^A-Za-z0-9_]/', '_', $base);
        if (empty($tableName)) {
            $tableName = 'imported_teacher_file';
        }

        // Create table if not exists
        $createSQL = "CREATE TABLE IF NOT EXISTS `" . $tableName . "` (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            student_name VARCHAR(255) NOT NULL,
            gender VARCHAR(10) NOT NULL,
            q1_grade DECIMAL(5,2),
            q2_grade DECIMAL(5,2),
            q3_grade DECIMAL(5,2),
            q4_grade DECIMAL(5,2),
            final_grade DECIMAL(5,2)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $pdo->exec($createSQL);
        // Truncate the table to ensure clean import
        $pdo->exec("TRUNCATE TABLE `" . $tableName . "`");

        $insertSQL = "INSERT INTO `" . $tableName . "` (student_name, gender, q1_grade, q2_grade, q3_grade, q4_grade, final_grade) VALUES (:name, :gender, :q1, :q2, :q3, :q4, :final)";
        $stmt = $pdo->prepare($insertSQL);

        $rowsInserted = 0;
        $currentGender = 'Male';

        for ($i = $DATA_START_ROW - 1; $i < count($dataArray); $i++) {
            $row = $dataArray[$i];
            $cellContent = trim($row[$NAME_COL] ?? '');

            if (stripos($cellContent, 'Prepared by:') !== false) {
                break;
            }

            if (strtoupper(substr($cellContent, 0, 6)) === 'FEMALE') {
                $currentGender = 'Female';
                continue;
            }

            $studentName = preg_replace('/^\d+\.\s*/', '', $cellContent);
            if (empty($studentName)) {
                continue;
            }

            $dataToInsert = [
                ':name' => $studentName,
                ':gender' => $currentGender,
                ':q1' => floatval($row[$Q1_COL] ?? 0),
                ':q2' => floatval($row[$Q2_COL] ?? 0),
                ':q3' => floatval($row[$Q3_COL] ?? 0),
                ':q4' => floatval($row[$Q4_COL] ?? 0),
                ':final' => floatval($row[$FINAL_COL] ?? 0)
            ];

            $stmt->execute($dataToInsert);
            $rowsInserted++;
        }

        return ['success' => true, 'rows' => $rowsInserted, 'message' => "Imported $rowsInserted rows into $tableName", 'table' => $tableName];

    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        return ['success' => false, 'rows' => 0, 'message' => 'Excel error: ' . $e->getMessage()];
    } catch (PDOException $e) {
        return ['success' => false, 'rows' => 0, 'message' => 'DB error: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['success' => false, 'rows' => 0, 'message' => $e->getMessage()];
    }
}

?>
