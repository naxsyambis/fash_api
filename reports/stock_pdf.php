
<?php
require "../auth/verify_token.php";
require "../config/database.php";
require "../libs/fpdf/fpdf.php";

/* hanya admin */
if ($payload['role'] !== 'ADMIN') {
    die("Akses ditolak");
}

/* filter */
$date_from = $_GET['date_from'] ?? null;
$date_to   = $_GET['date_to'] ?? null;

/* query */
$query = "
SELECT
    sm.created_at,
    p.product_name,
    ip.size,
    ip.color,
    sm.movement_type,
    sm.quantity,
    sm.reason
FROM stock_movements sm
JOIN item_produk ip ON sm.item_id = ip.item_id
JOIN products p ON ip.product_id = p.product_id
WHERE 1=1
";

$params = [];
$types  = "";

if ($date_from && $date_to) {
    $query .= " AND DATE(sm.created_at) BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $date_from;
    $params[] = $date_to;
}

$query .= " ORDER BY sm.created_at ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

/* PDF */
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

$pdf->Cell(0,10,'LAPORAN STOCK BARANG',0,1,'C');
$pdf->Ln(3);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,7,
    "Periode: " . ($date_from ?? '-') . " s/d " . ($date_to ?? '-'),
0,1);

$pdf->Ln(2);

/* table header */
$pdf->SetFont('Arial','B',9);
$pdf->Cell(35,8,'Tanggal',1);
$pdf->Cell(60,8,'Produk',1);
$pdf->Cell(20,8,'Size',1);
$pdf->Cell(25,8,'Warna',1);
$pdf->Cell(20,8,'Tipe',1);
$pdf->Cell(20,8,'Qty',1);
$pdf->Cell(35,8,'Keterangan',1);
$pdf->Ln();

/* data */
$pdf->SetFont('Arial','',9);

$total_in = 0;
$total_out = 0;

while ($row = $result->fetch_assoc()) {

    if ($row['movement_type'] == 'IN') {
        $total_in += $row['quantity'];
    } else {
        $total_out += $row['quantity'];
    }

    $pdf->Cell(35,7,$row['created_at'],1);
    $pdf->Cell(60,7,$row['product_name'],1);
    $pdf->Cell(20,7,$row['size'],1);
    $pdf->Cell(25,7,$row['color'],1);
    $pdf->Cell(20,7,$row['movement_type'],1);
    $pdf->Cell(20,7,$row['quantity'],1);
    $pdf->Cell(35,7,$row['reason'],1);
    $pdf->Ln();
}

/* total */
$pdf->Ln(4);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,8,"Total IN : $total_in",0);
$pdf->Cell(40,8,"Total OUT : $total_out",0);

$pdf->Output("D","laporan_stock.pdf");

?>