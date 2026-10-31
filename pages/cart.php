<?php
/** Shopping cart page. */
require_login();
$page_title = 'Cart';

$stmt = db()->prepare(
    'SELECT c.id AS cart_id, c.quantity, p.*
     FROM cart_items c JOIN products p ON p.id = c.product_id
     WHERE c.user_id = ? ORDER BY c.id'
);
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

$grand_total = 0;
foreach ($items as $it) {
    $grand_total += $it['price'] * $it['quantity'];
}

require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-5xl mx-auto py-6 px-2">
    <h1 class="text-3xl font-black mb-8 uppercase tracking-tighter">Shopping Cart</h1>

    <div class="grid grid-cols-12 gap-4 pb-4 border-b border-neutral-800 text-xs font-bold text-neutral-500 uppercase tracking-widest">
        <div class="col-span-6">Product</div>
        <div class="col-span-2 text-center">Price</div>
        <div class="col-span-2 text-center">Quantity</div>
        <div class="col-span-2 text-right">Total</div>
    </div>

    <div class="space-y-6 py-6">
        <?php if ($items): foreach ($items as $item): ?>
        <div class="grid grid-cols-12 gap-4 items-center border-b border-neutral-800 pb-6">
            <div class="col-span-6 flex items-center gap-4">
                <img src="<?= media('products/' . rawurlencode($item['image'])) ?>" alt="<?= e($item['name']) ?>" class="w-20 h-20 object-cover rounded-xl shadow-lg">
                <div>
                    <h3 class="font-bold text-white"><?= e($item['name']) ?></h3>
                    <p class="text-xs text-neutral-500">In Stock</p>
                </div>
            </div>
            <div class="col-span-2 text-center font-medium text-neutral-300"><?= rupees($item['price']) ?></div>
            <div class="col-span-2 text-center flex justify-center items-center gap-3">
                <a href="<?= url('update_cart', ['id' => $item['id'], 'action' => 'remove']) ?>"
                   class="text-neutral-500 hover:text-red-500 font-bold text-xl transition active:scale-90">-</a>
                <span class="font-black text-white w-8"><?= (int)$item['quantity'] ?></span>
                <a href="<?= url('update_cart', ['id' => $item['id'], 'action' => 'add']) ?>"
                   class="text-neutral-500 hover:text-green-500 font-bold text-xl transition active:scale-90">+</a>
            </div>
            <div class="col-span-2 text-right font-bold text-indigo-400"><?= rupees($item['price'] * $item['quantity']) ?></div>
        </div>
        <?php endforeach; else: ?>
        <p class="text-center py-20 text-neutral-500 italic">Your cart is currently empty.</p>
        <?php endif; ?>
    </div>

    <?php if ($items): ?>
    <div class="flex flex-col items-end pt-6 border-t border-neutral-800">
        <div class="text-xl mb-6">
            <span class="text-neutral-400 mr-2">Grand Total:</span>
            <span class="font-black text-3xl text-white"><?= rupees($grand_total) ?></span>
        </div>
        <a href="<?= url('checkout') ?>" class="w-full md:w-auto px-12 py-4 bg-indigo-600 rounded-xl font-black uppercase tracking-widest hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20 active:scale-95">
            Proceed to Checkout
        </a>
    </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
