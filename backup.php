<?php
// backup.php – Xuất toàn bộ database ra file .sql và tải về
include "auth_admin.php";
include "config.php";

// Tên file backup kèm ngày giờ
$filename = "backup_chillwave_" . date('Y-m-d_H-i-s') . ".sql";

// Header để trình duyệt tải file về
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');

// ============================================================
// Tạo nội dung file SQL
// ============================================================
$output = "";
$output .= "-- ============================================================\n";
$output .= "-- Chill Wave Music – Database Backup\n";
$output .= "-- Ngày backup: " . date('d/m/Y H:i:s') . "\n";
$output .= "-- ============================================================\n\n";
$output .= "SET NAMES utf8mb4;\n";
$output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

// Lấy danh sách tất cả bảng
$tables = [];
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($result)) {
    $tables[] = $row[0];
}

foreach ($tables as $table) {
    // Lấy câu CREATE TABLE
    $create = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
    $createRow = mysqli_fetch_array($create);
    
    $output .= "-- --------------------------------------------------------\n";
    $output .= "-- Cấu trúc bảng: `$table`\n";
    $output .= "-- --------------------------------------------------------\n\n";
    $output .= "DROP TABLE IF EXISTS `$table`;\n";
    $output .= $createRow[1] . ";\n\n";

    // Lấy dữ liệu
    $rows = mysqli_query($conn, "SELECT * FROM `$table`");
    $numRows = mysqli_num_rows($rows);
    
    if ($numRows > 0) {
        $output .= "-- Dữ liệu bảng `$table`\n\n";
        $output .= "INSERT INTO `$table` VALUES\n";
        
        $rowsData = [];
        while ($row = mysqli_fetch_array($rows, MYSQLI_NUM)) {
            $values = array_map(function($val) use ($conn) {
                if ($val === null) return 'NULL';
                return "'" . mysqli_real_escape_string($conn, $val) . "'";
            }, $row);
            $rowsData[] = "(" . implode(", ", $values) . ")";
        }
        $output .= implode(",\n", $rowsData) . ";\n\n";
    }
}

$output .= "SET FOREIGN_KEY_CHECKS = 1;\n";
$output .= "\n-- Backup hoàn thành: " . date('d/m/Y H:i:s') . "\n";

echo $output;
mysqli_close($conn);
?>
