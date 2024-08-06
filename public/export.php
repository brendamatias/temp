<?php
require_once('../vendor/autoload.php'); // Autoload composer dependencies
require_once('../config/db.php'); // Database connection

// Get the export type (pdf or excel) from the request
$exportType = isset($_GET['type']) ? $_GET['type'] : null;

if (!$exportType) {
    die('Invalid export type.');
}

// Fetch notifications from the database
$stmt = $pdo->query('SELECT * FROM notifications');
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($notifications)) {
    die('No notifications available to export.');
}

// Export as PDF or Excel
if ($exportType === 'pdf') {
    exportAsPDF($notifications);
} elseif ($exportType === 'excel') {
    exportAsExcel($notifications);
} else {
    die('Invalid export type.');
}

// Export to PDF using TCPDF
function exportAsPDF($notifications) {
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Table header
    $html = '<h1>Notification List</h1>';
    $html .= '<table border="1" cellpadding="5"><thead><tr>';
    $html .= '<th>ID</th><th>User ID</th><th>Channel</th><th>Message</th><th>Status</th><th>Created At</th>';
    $html .= '</tr></thead><tbody>';

    // Table content
    foreach ($notifications as $notification) {
        $html .= '<tr>';
        $html .= '<td>' . $notification['id'] . '</td>';
        $html .= '<td>' . $notification['user_id'] . '</td>';
        $html .= '<td>' . $notification['channel'] . '</td>';
        $html .= '<td>' . $notification['message'] . '</td>';
        $html .= '<td>' . $notification['status'] . '</td>';
        $html .= '<td>' . $notification['created_at'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $pdf->writeHTML($html);
    $pdf->Output('notifications.pdf', 'D');
}

// Export to Excel using PhpSpreadsheet
function exportAsExcel($notifications) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Notifications');

    // Header
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'User ID');
    $sheet->setCellValue('C1', 'Channel');
    $sheet->setCellValue('D1', 'Message');
    $sheet->setCellValue('E1', 'Status');
    $sheet->setCellValue('F1', 'Created At');

    // Data
    $row = 2;
    foreach ($notifications as $notification) {
        $sheet->setCellValue('A' . $row, $notification['id']);
        $sheet->setCellValue('B' . $row, $notification['user_id']);
        $sheet->setCellValue('C' . $row, $notification['channel']);
        $sheet->setCellValue('D' . $row, $notification['message']);
        $sheet->setCellValue('E' . $row, $notification['status']);
        $sheet->setCellValue('F' . $row, $notification['created_at']);
        $row++;
    }

    // Save Excel file
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $fileName = 'notifications.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    $writer->save('php://output');
    exit;
}

