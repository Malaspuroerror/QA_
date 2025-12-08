<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import advisory Excel file and create table named by grade_section (e.g. 10_A -> "10_A").
 * Inserts student_name and gender plus grade_level, section and adviser_name into the table.
 *
 * @param string $relativePath Path relative to project root
 * @param PDO $pdo
 * @param string $gradeSection e.g. "10 - A" or "10-A" or "Grade10-A"
 * @param string $adviserName
 * @return array
 */
function import_advisory_excel(string $relativePath, PDO $pdo, string $gradeSection, string $adviserName = ''): array
{
    $projectRoot = realpath(__DIR__ . '/..');
    $fullPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

    if (!file_exists($fullPath)) {
        return ['success' => false, 'rows' => 0, 'message' => "File not found: $fullPath"];
    }

    // Basic mapping - using same layout as teacher files
    $NAME_COL = 1; // Column B
    $DATA_START_ROW = 13; // first data row index (1-based)

    try {
        $spreadsheet = IOFactory::load($fullPath);
        $worksheet = $spreadsheet->getSheetByName('SUMMARY');
        if (!$worksheet) {
            $worksheet = $spreadsheet->getActiveSheet();
        }

        $highestRow = $worksheet->getHighestRow();
        $highestColumnLetter = $worksheet->getHighestColumn();
        $range = 'A1:' . max($highestColumnLetter, 'V') . $highestRow;
        $dataArray = $worksheet->rangeToArray($range, null, true, true, false);

        // Build table name from gradeSection, expected result like GRADELVL_SECTION (uppercase)
        $raw = trim($gradeSection);
        $tableName = preg_replace('/[^A-Za-z0-9]+/', '_', strtoupper($raw));
        $tableName = trim($tableName, '_');
        // Allow numeric-leading table names as-is (no automatic prefix)
        if (empty($tableName)) {
            $tableName = 'ADVISORY_IMPORT';
        }

        // Create table with required columns
        $createSQL = "CREATE TABLE IF NOT EXISTS `" . $tableName . "` (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            student_name VARCHAR(255) NOT NULL,
            gender VARCHAR(20) DEFAULT NULL,
            grade_level VARCHAR(50) DEFAULT NULL,
            section VARCHAR(50) DEFAULT NULL,
            adviser_name VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $pdo->exec($createSQL);
        // Truncate to ensure clean import
        $pdo->exec("TRUNCATE TABLE `" . $tableName . "`");

        $insertSQL = "INSERT INTO `" . $tableName . "` (student_name, gender, grade_level, section, adviser_name) VALUES (:name, :gender, :grade, :section, :adviser)";
        $stmt = $pdo->prepare($insertSQL);

        // parse provided grade/section into two parts
        $gradeLevel = '';
        $section = '';
        // common separators: '-', ' - ', '_'
        if (strpos($gradeSection, '-') !== false) {
            $parts = explode('-', $gradeSection, 2);
            $gradeLevel = trim($parts[0]);
            $section = trim($parts[1]);
        } else {
            // fallback if user provided single value
            $gradeLevel = trim($gradeSection);
            $section = '';
        }

        $rowsInserted = 0;
        $currentGender = 'Male';

        for ($i = $DATA_START_ROW - 1; $i < count($dataArray); $i++) {
            $row = $dataArray[$i];
            $cellContent = trim($row[$NAME_COL] ?? '');

            if (stripos($cellContent, 'Prepared by:') !== false) {
                break;
            }

            // Some sheets include gender marker rows like "FEMALE"
            if (strtoupper(substr($cellContent, 0, 6)) === 'FEMALE') {
                $currentGender = 'Female';
                continue;
            }

            // remove leading numbering "1. "
            $studentName = preg_replace('/^\d+\.\s*/', '', $cellContent);
            if (empty($studentName)) {
                continue;
            }

            $params = [
                ':name' => $studentName,
                ':gender' => $currentGender,
                ':grade' => $gradeLevel,
                ':section' => $section,
                ':adviser' => !empty($adviserName) ? $adviserName : null
            ];

            $stmt->execute($params);
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