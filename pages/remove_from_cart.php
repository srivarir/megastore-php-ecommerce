<?php
/** Remove a product entirely from the cart. */
require_login();
$pid = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ?');
$stmt->execute([$_SESSION['user_id'], $pid]);
redirect('cart');
