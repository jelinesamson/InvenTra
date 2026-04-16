<?php
header('Content-Type: application/json');
include('config.php');

if (!$conn) die(json_encode(['success'=>false,'error'=>'DB connection failed.']));

$month = $_GET['month'] ?? date('Y-m');
$typeFilter = $_GET['type'] ?? '';

$monthStart = $month . '-01';
$monthEnd   = date('Y-m-t', strtotime($monthStart));

// --- Transaction Items History ---
$sqlHistory = "
SELECT ti.item_id, ti.transaction_id, ti.product_name AS product, ti.category,
       ti.qty, ti.unit_price, ti.total_price,
       t.paid, t.change_amount AS `change`, t.vat AS vat_amount,
       t.status, t.date_time AS sold_at
FROM transaction_items ti
JOIN transactions t ON ti.transaction_id = t.transaction_id
WHERE t.status='completed'
  AND t.date_time BETWEEN '$monthStart 00:00:00' AND '$monthEnd 23:59:59'
  " . ($typeFilter ? " AND ti.category='$typeFilter'" : "") . "
ORDER BY t.date_time DESC
LIMIT 100";
$hRes = mysqli_query($conn, $sqlHistory);
$history = [];
$overallTotal = $overallVat = $overallPaid = 0;
$processedTxns = []; 

while ($r = mysqli_fetch_assoc($hRes)) {
    $history[] = $r;
    $overallTotal += (float)$r['total_price'];  
    
    if (!in_array($r['transaction_id'], $processedTxns)) {
        $overallVat   += (float)$r['vat_amount'];
        $overallPaid  += (float)$r['paid'];
        $processedTxns[] = $r['transaction_id'];
    }
}

// --- Product Summary (aggregated) ---
$sqlSummary = "
SELECT ti.product_id, ti.product_name AS product, ti.category,
       ti.unit_price,
       SUM(ti.qty) AS total_qty,
       SUM(ti.total_price) AS total_amount,
       SUM(t.vat) AS total_vat
FROM transaction_items ti
JOIN transactions t ON ti.transaction_id = t.transaction_id
WHERE t.status='completed'
  AND t.date_time BETWEEN '$monthStart 00:00:00' AND '$monthEnd 23:59:59'
  " . ($typeFilter ? " AND ti.category='$typeFilter'" : "") . "
GROUP BY ti.product_id, ti.product_name, ti.category, ti.unit_price
ORDER BY total_amount DESC";
$sRes = mysqli_query($conn, $sqlSummary);
$items = [];
$catTotals = [];
while ($r = mysqli_fetch_assoc($sRes)) {
    $items[] = $r;
    $cat = $r['category'];
    if (!isset($catTotals[$cat])) $catTotals[$cat] = ['qty'=>0,'amount'=>0];
    $catTotals[$cat]['qty'] += (int)$r['total_qty'];
    $catTotals[$cat]['amount'] += (float)$r['total_amount'];
}
arsort($catTotals);

// --- KPI & Response ---
$totalQty = array_sum(array_column($items,'total_qty'));
$topCat   = $catTotals ? array_key_first($catTotals) : '—';

// --- Previous Month ---
$prevMonthStart = date('Y-m-01', strtotime($monthStart . ' -1 month'));
$prevMonthEnd   = date('Y-m-t', strtotime($prevMonthStart));

// Previous totals
$sqlPrev = "
SELECT 
  SUM(ti.total_price) AS prev_total,
  SUM(ti.qty) AS prev_qty
FROM transaction_items ti
JOIN transactions t ON ti.transaction_id = t.transaction_id
WHERE t.status='completed'
  AND t.date_time BETWEEN '$prevMonthStart 00:00:00' AND '$prevMonthEnd 23:59:59'
" . ($typeFilter ? " AND ti.category='$typeFilter'" : "");

$pRes = mysqli_query($conn, $sqlPrev);
$pRow = mysqli_fetch_assoc($pRes);

$prevTotal = (float)($pRow['prev_total'] ?? 0);
$prevQty   = (float)($pRow['prev_qty'] ?? 0);

// Compute % change
$revChange = $prevTotal > 0
  ? (($overallTotal - $prevTotal) / $prevTotal) * 100
  : 0;

$qtyChange = $prevQty > 0
  ? (($totalQty - $prevQty) / $prevQty) * 100
  : 0;

echo json_encode([
    'success' => true,
    'month'   => $month,
    'month_label' => date('F Y', strtotime($monthStart)),
    'generated'   => date('F j, Y \a\t g:i A'),
    'available_months' => [$month], // you can expand this
    'product_types'    => array_unique(array_column($items,'category')),
    'kpi' => [
        'total_revenue' => $overallTotal,
        'total_qty'     => $totalQty,
        'total_vat'     => $overallVat,
        'total_paid'    => $overallPaid,
        'top_category'  => $topCat,
        'rev_change_pct' => round($revChange, 2),
        'qty_change_pct' => round($qtyChange, 2),
    ],
    'items'   => $items,
    'history' => $history,
    'overall_total' => $overallTotal,
    'overall_vat'   => $overallVat,
]);