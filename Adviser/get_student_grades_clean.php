<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../Login/db.php'; // provides $pdo and optionally $dbname

// JSON helper
function send(array $payload) {
    echo json_encode($payload);
    exit;
}

$grade = trim($_POST['grade_level'] ?? '');
$section = trim($_POST['section'] ?? '');
$student = trim($_POST['student_name'] ?? '');
$tableOverride = trim($_POST['table'] ?? '');
$debug = !empty($_POST['debug']);
$debugLogs = [];

$debugLogs[] = "Received: grade={$grade}, section={$section}, student={$student}, table_override={$tableOverride}";

if ($student === '' || $grade === '') send(['success' => false, 'message' => 'Missing parameters', 'debug' => $debugLogs]);

// determine DB name
$dbName = null;
try {
    if (isset($dbname) && $dbname) $dbName = $dbname;
    else $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
} catch (Exception $e) {
    $debugLogs[] = 'Could not determine DB name: ' . $e->getMessage();
}
if (!$dbName) $dbName = 'school';
$debugLogs[] = 'Using DB: ' . $dbName;

function normalize_token($s) { return trim(preg_replace('/[^A-Z0-9]+/', '_', strtoupper($s)), '_'); }

$gToken = normalize_token($grade);
$sToken = normalize_token($section);

$candidates = [];
// table override
if ($tableOverride !== '') {
    try {
        $chk = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :db AND table_name = :t');
        $chk->execute([':db' => $dbName, ':t' => $tableOverride]);
        if ($chk->fetchColumn() > 0) { $candidates[] = $tableOverride; $debugLogs[] = 'Override found: ' . $tableOverride; }
        else $debugLogs[] = 'Override not found: ' . $tableOverride;
    } catch (Exception $e) { $debugLogs[] = 'Override check failed: ' . $e->getMessage(); }
}

if (empty($candidates)) {
    try {
        $like1 = '%' . $gToken . '%';
        $like2 = $sToken ? ('%' . $sToken . '%') : '%';
        $stmt = $pdo->prepare('SELECT table_name FROM information_schema.tables WHERE table_schema = :db AND table_name LIKE :l1 AND table_name LIKE :l2');
        $stmt->execute([':db' => $dbName, ':l1' => $like1, ':l2' => $like2]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $candidates = array_values(array_unique($rows));
        $debugLogs[] = 'Candidates: ' . implode(',', $candidates ?: ['(none)']);
    } catch (Exception $e) { $debugLogs[] = 'Table search failed: ' . $e->getMessage(); }
}

if (empty($candidates)) send(['success' => false, 'message' => 'No candidate tables', 'debug' => $debugLogs]);

$results = [];
foreach ($candidates as $table) {
    try {
        $cstmt = $pdo->prepare('SELECT column_name FROM information_schema.columns WHERE table_schema = :db AND table_name = :t');
        $cstmt->execute([':db' => $dbName, ':t' => $table]);
        $cols = $cstmt->fetchAll(PDO::FETCH_COLUMN);
        if (empty($cols)) { $debugLogs[] = "$table: no columns"; continue; }
        $lc = array_map('strtolower', $cols);

        // student column
        $studentCol = null;
        foreach (['student_name','student','name'] as $cand) { if (($p = array_search($cand, $lc, true)) !== false) { $studentCol = $cols[$p]; break; } }
        if (!$studentCol) { $debugLogs[] = "$table: no student col"; continue; }

        // q1..q4
        $qCols = [];
        for ($i=1;$i<=4;$i++) {
            $found = null;
            foreach ($lc as $idx => $cname) { if (strpos($cname, 'q' . $i) !== false) { $found = $cols[$idx]; break; } }
            $qCols[$i] = $found;
        }
        if (empty(array_filter($qCols))) { $debugLogs[] = "$table: no q cols"; continue; }

        $finalCol = null;
        foreach ($lc as $idx => $cname) { if ($cname === 'final_grade' || $cname === 'final' || $cname === 'finalgrade') { $finalCol = $cols[$idx]; break; } }

        // build select
        $select = ['`' . str_replace('`','``',$studentCol) . '`'];
        for ($i=1;$i<=4;$i++) $select[] = $qCols[$i] ? ('`' . str_replace('`','``',$qCols[$i]) . '`') : "NULL AS q$i";
        $select[] = $finalCol ? ('`' . str_replace('`','``',$finalCol) . '`') : 'NULL AS final_grade';

        $sql = 'SELECT ' . implode(', ', $select) . ' FROM `' . str_replace('`','``',$table) . '` WHERE `' . str_replace('`','``',$studentCol) . '` LIKE :stu LIMIT 1';

        // direct match
        $row = false;
        try { $st = $pdo->prepare($sql); $st->execute([':stu' => '%' . $student . '%']); $row = $st->fetch(PDO::FETCH_NUM); }
        catch (Exception $e) { $debugLogs[] = "$table query failed: " . $e->getMessage(); }

        // scan fallback
        if (!$row) {
            try {
                $scan = $pdo->query('SELECT ' . implode(', ', $select) . ' FROM `' . str_replace('`','``',$table) . '`');
                $all = $scan->fetchAll(PDO::FETCH_NUM);
                $normSearch = preg_replace('/[^a-z0-9]/','', strtolower($student));
                foreach ($all as $r) {
                    $dbVal = strtolower((string)$r[0]);
                    $normDb = preg_replace('/[^a-z0-9]/','', $dbVal);
                    if ($normDb !== '' && (strpos($normDb, $normSearch) !== false || strpos($normSearch, $normDb) !== false)) { $row = $r; $debugLogs[] = "$table normalized match: {$r[0]}"; break; }
                }
            } catch (Exception $e) { $debugLogs[] = "$table scan failed: " . $e->getMessage(); }
        }

        if ($row) {
            $q1 = is_numeric($row[1]) ? floatval($row[1]) : null;
            $q2 = is_numeric($row[2]) ? floatval($row[2]) : null;
            $q3 = is_numeric($row[3]) ? floatval($row[3]) : null;
            $q4 = is_numeric($row[4]) ? floatval($row[4]) : null;
            $finalStored = is_numeric($row[5]) ? floatval($row[5]) : null;
            $subject = trim(preg_replace('/[_]+/',' ', preg_replace('/' . preg_quote($gToken, '/') . '/i','',$table)));
            if ($sToken) $subject = preg_replace('/' . preg_quote($sToken, '/') . '/i','',$subject);
            if ($subject === '') $subject = $table;
            $results[] = ['table'=>$table,'subject'=>$subject,'q1'=>$q1,'q2'=>$q2,'q3'=>$q3,'q4'=>$q4,'final_stored'=>$finalStored];
        }

    } catch (Exception $e) { $debugLogs[] = "$table inspect error: " . $e->getMessage(); continue; }
}

// compute GWA as (sum of all subjects' final_stored) / (number of subjects)
$gwa = null;
$subjectCount = count($results);
if ($subjectCount > 0) {
    $sumFinals = 0.0;
    foreach ($results as $r) {
        $sumFinals += (isset($r['final_stored']) && is_numeric($r['final_stored'])) ? floatval($r['final_stored']) : 0.0;
    }
    $gwa = round($sumFinals / $subjectCount, 2);
}

if (empty($results)) send(['success'=>false,'message'=>'No grades found','debug'=>$debug ? $debugLogs : []]);
send(['success'=>true,'grades'=>$results,'gwa'=>$gwa,'debug'=>$debug ? $debugLogs : []]);

?>