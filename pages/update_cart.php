<?php
/** Increment / decrement a cart line; delete when it hits zero. */
require_login();
$cart_id = (int)($_GET['id'] ?? 0);
$action  = $_GET['action'] ?? '';

$stmt = db()->prepare('SELECT id, quantity FROM cart_items WHERE id = ? AND user_id = ?');
$stmt->execute([$cart_id, $_SESSION['user_id']]);
$item = $stmt->fetch();

if ($item) {
    if ($action === 'add') {
        db()->prepare('UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?')->execute([$cart_id]);
    } elseif ($action === 'remove') {
        if ((int)$item['quantity'] > 1) {
            db()->prepare('UPDATE cart_items SET quantity = quantity - 1 WHERE id = ?')->execute([$cart_id]);
        } else {
            db()->prepare('DELETE FROM cart_items WHERE id = ?')->execute([$cart_id]);
        }
    }
}
redirect('cart');
