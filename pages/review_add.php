<?php
/** Save a product review (logged-in users only). */
require_login();
check_csrf();

$pid = (int)($_GET['id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

$stmt = db()->prepare('SELECT slug FROM products WHERE id = ?');
$stmt->execute([$pid]);
$prod = $stmt->fetch();
if (!$prod) { redirect('home'); }

if ($rating >= 1 && $rating <= 5) {
    $ins = db()->prepare(
        'INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)'
    );
    $ins->execute([$pid, $_SESSION['user_id'], $rating, $comment]);
}
redirect('product', ['slug' => $prod['slug']]);
