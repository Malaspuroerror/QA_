<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// ====================================================================
//                             1. DATABASE CONFIGURATION
// ====================================================================

const DB_HOST = 'localhost';
const DB_NAME = 'school';
const DB_USER = 'root'; // DEFAULT XAMPP USER
const DB_PASS = '';     // DEFAULT XAMPP PASSWORD (often blank)
const TABLE_NAME = 'ESP_VI_Venus'; 

// ====================================================================
//                             2. EXCEL CONFIGURATION
// ====================================================================

const INPUT_FILE = 'ESP-VI-Venus.xlsx'; 
const TARGET_SHEET_NAME = 'SUMMARY'; 

// Column Indices (0-based array index, based on F13 start)
const NAME_COL = 1;      // Column B (Student Name, and location of "MALE"/"FEMALE" labels)
const Q1_COL = 5;      // Column F
const Q2_COL = 9;      // Column J
const Q3_COL = 13;     // Column N
const Q4_COL = 17;     // Column R
const FINAL_COL = 21;  // Column V

// Data starts on Row 13 (fixed start for student list)
const DATA_START_ROW = 13; 

// ====================================================================
//                              3. EXECUTION
// ====================================================================

if (!file_exists(INPUT_FILE)) {
    die("Error: Original Excel file not found at path: " . INPUT_FILE);
}

try {
    // --- PART A: EXCEL DATA EXTRACTION ---
    $spreadsheet = IOFactory::load(INPUT_FILE);
    $worksheet = $spreadsheet->getSheetByName(TARGET_SHEET_NAME);

    if (!$worksheet) {
        die("Error: Sheet named '" . TARGET_SHEET_NAME . "' not found in the Excel file.");
    }
    
    // Get full data array
    $highestRow = $worksheet->getHighestRow();
    $highestColumnLetter = $worksheet->getHighestColumn();
    $range = 'A1:' . max($highestColumnLetter, 'V') . $highestRow; 
    $dataArray = $worksheet->rangeToArray(
        $range, null, $calculateFormulas = true, $dateColumns = true, $returnCellDetails = false
    );

    // --- PART B: DATABASE CONNECTION AND SETUP ---
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Successfully connected to the database **" . DB_NAME . "**.<br>";

    // Create table (same structure as before)
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS " . TABLE_NAME . " (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            student_name VARCHAR(255) NOT NULL,
            gender VARCHAR(10) NOT NULL,
            q1_grade DECIMAL(5, 2),
            q2_grade DECIMAL(5, 2),
            q3_grade DECIMAL(5, 2),
            q4_grade DECIMAL(5, 2),
            final_grade DECIMAL(5, 2)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($createTableSQL);
    echo "Table **" . TABLE_NAME . "** is ready.<br>";
    
    // Truncate the table to ensure a clean import
    $pdo->exec("TRUNCATE TABLE " . TABLE_NAME);
    echo "Existing data cleared from the table.<br>";

    $insertSQL = "
        INSERT INTO " . TABLE_NAME . " 
        (student_name, gender, q1_grade, q2_grade, q3_grade, q4_grade, final_grade) 
        VALUES (:name, :gender, :q1, :q2, :q3, :q4, :final)
    ";
    $stmt = $pdo->prepare($insertSQL);

    // --- PART C: DYNAMIC DATA INSERTION ---
    $rowsInserted = 0;
    $currentGender = 'Male'; // Start assuming Male until proven otherwise
    
    // Loop starting from the first student data row (index 12 in the 0-indexed array)
    for ($i = DATA_START_ROW - 1; $i < count($dataArray); $i++) {
        $row = $dataArray[$i];
        
        // Check content of the column where student names and labels appear (NAME_COL, index 1)
        $cellContent = trim($row[NAME_COL] ?? '');
        
        // 1. STOP Condition: Check for the footer start ("Prepared by:")
        if (strpos($cellContent, 'Prepared by:') !== false) {
            echo "Stopping import: Detected 'Prepared by:' row.<br>";
            break; 
        }

        // 2. GENDER SWITCH/SKIP Condition: Check for the Female label
        // We trim the content and check if it starts with "FEMALE"
        if (strtoupper(substr($cellContent, 0, 6)) === 'FEMALE') {
            $currentGender = 'Female'; // Switch gender for subsequent rows
            continue; // Skip inserting the label row itself
        }
        
        // 3. ACTUAL DATA INSERTION
        
        // Skip rows that are empty (only contain the row index number or are truly blank)
        $studentName = preg_replace('/^\d+\.\s*/', '', $cellContent);
        if (empty($studentName)) {
             continue; 
        }
        
        // Prepare data for insertion
        $dataToInsert = [
            'name'   => $studentName,
            'gender' => $currentGender, // Use the dynamically set gender
            'q1'     => floatval($row[Q1_COL] ?? 0),
            'q2'     => floatval($row[Q2_COL] ?? 0),
            'q3'     => floatval($row[Q3_COL] ?? 0),
            'q4'     => floatval($row[Q4_COL] ?? 0),
            'final'  => floatval($row[FINAL_COL] ?? 0)
        ];

        // Execute the prepared statement
        $stmt->execute($dataToInsert);
        $rowsInserted++;
    }

    echo "<h2>Import Complete!</h2>";
    echo "Successfully imported **{$rowsInserted}** student records (dynamic read) into the **" . TABLE_NAME . "** table.";

} catch (PDOException $e) {
    die('<h2 style="color: red;">Database Error</h2>' . $e->getMessage());
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    die('<h2 style="color: red;">Excel Reading Error</h2>' . $e->getMessage());
}
?>