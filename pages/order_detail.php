<?php
/** Single order detail with its line items. */
require_login();
$u = current_user();
$order_id = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$order_id, $u['id']]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    $page_title = 'Not found';
    require __DIR__ . '/../includes/header.php';
    echo '<p class="text-neutral-400 py-20 text-center">Order not found.</p>';
    require __DIR__ . '/../includes/footer.php';
    return;
}

$stmt = db()->prepare(
    'SELECT oi.*, p.name, p.image, p.slug FROM order_items oi
     JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?'
);
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$page_title = 'Order #' . $order['id'];
require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-3xl mx-auto py-6 px-2">
    <a href="<?= url('profile') ?>" class="text-neutral-500 hover:text-white text-sm">← Back to profile</a>
    <div class="flex justify-between items-start mt-4 mb-8">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter">Order #<?= (int)$order['id'] ?></h1>
            <p class="text-neutral-500 text-sm mt-1"><?= date('M d, Y · g:i A', strtotime($order['created_at'])) ?></p>
        </div>
        <span class="px-4 py-2 rounded-full text-xs font-bold uppercase
            <?= $order['status'] === 'delivered' ? 'bg-emerald-900/40 text-emerald-400' : ($order['status'] === 'process' ? 'bg-rose-900/40 text-rose-400' : 'bg-indigo-900/40 text-indigo-400') ?>">
            <?= e($order['status']) ?>
        </span>
    </div>

    <div class="grid md:grid-cols-2 gap-4 mb-8">
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-5">
            <p class="text-[10px] uppercase font-bold text-neutral-500 mb-1">Delivery Address</p>
            <p class="text-neutral-200 text-sm whitespace-pre-line"><?= e($order['address']) ?></p>
        </div>
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-5">
            <p class="text-[10px] uppercase font-bold text-neutral-500 mb-1">Delivery / Payment</p>
            <p class="text-neutral-200 text-sm capitalize"><?= e($order['delivery_option']) ?> · <?= strtoupper(e($order['payment_method'])) ?></p>
        </div>
    </div>

    <div class="bg-neutral-900 border border-neutral-800 rounded-2xl divide-y divide-neutral-800">
        <?php foreach ($items as $it): ?>
        <div class="flex items-center gap-4 p-4">
            <img src="<?= media('products/' . rawurlencode($it['image'])) ?>" class="w-16 h-16 object-contain rounded-lg bg-neutral-800 p-2">
            <div class="flex-1">
                <a href="<?= url('product', ['slug' => $it['slug']]) ?>" class="font-bold text-white hover:text-indigo-400"><?= e($it['name']) ?></a>
                <p class="text-xs text-neutral-500"><?= rupees($it['price']) ?> × <?= (int)$it['quantity'] ?></p>
            </div>
            <div class="font-bold text-indigo-400"><?= rupees($it['price'] * $it['quantity']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="flex justify-end mt-6 text-xl">
        <span class="text-neutral-400 mr-3">Total:</span>
        <span class="font-black text-2xl text-white"><?= rupees($order['total_price']) ?></span>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
