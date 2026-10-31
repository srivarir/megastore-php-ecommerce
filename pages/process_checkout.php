<?php
/**
 * Process the checkout form: create the order + order items, clear cart,
 * then route to the chosen payment page (mirrors Django process_checkout).
 */
require_login();
check_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('checkout');
}

$address  = trim($_POST['address'] ?? '');
$delivery = $_POST['delivery_option'] ?? 'standard';
$payment  = $_POST['payment_method'] ?? 'cod';

$allowed_delivery = ['standard', 'express'];
$allowed_payment  = ['cod', 'upi', 'card'];
if (!in_array($delivery, $allowed_delivery, true)) $delivery = 'standard';
if (!in_array($payment, $allowed_payment, true))  $payment  = 'cod';

// Load cart
$stmt = db()->prepare(
    'SELECT c.quantity, p.id AS pid, p.price
     FROM cart_items c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?'
);
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

if (!$items || $address === '') {
    redirect('checkout');
}

$grand_total = 0;
foreach ($items as $it) $grand_total += $it['price'] * $it['quantity'];

$pdo = db();
$pdo->beginTransaction();
try {
    $ins = $pdo->prepare(
        'INSERT INTO orders (user_id, address, delivery_option, payment_method, status, total_price)
         VALUES (?, ?, ?, ?, "pending", ?)'
    );
    $ins->execute([$_SESSION['user_id'], $address, $delivery, $payment, $grand_total]);
    $order_id = (int)$pdo->lastInsertId();

    $oi = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)'
    );
    foreach ($items as $it) {
        $oi->execute([$order_id, $it['pid'], $it['price'], $it['quantity']]);
    }

    // Clear cart
    $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?')->execute([$_SESSION['user_id']]);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('Could not place order: ' . e($e->getMessage()));
}

// Route to the matching payment screen
if ($payment === 'card')      redirect('payment_card');
elseif ($payment === 'upi')   redirect('payment_upi');
else                          redirect('payment_cod');
