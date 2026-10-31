<?php
/** Add a product to the cart (get_or_create + increment). */
require_login();
$pid = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT id FROM products WHERE id = ?');
$stmt->execute([$pid]);
if (!$stmt->fetch()) { redirect('cart'); }

$stmt = db()->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
$stmt->execute([$_SESSION['user_id'], $pid]);
$row = $stmt->fetch();

if ($row) {
    $upd = db()->prepare('UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?');
    $upd->execute([$row['id']]);
} else {
    $ins = db()->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, 1)');
    $ins->execute([$_SESSION['user_id'], $pid]);
}
redirect('cart');
