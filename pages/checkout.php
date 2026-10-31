<?php
/** Checkout form. */
require_login();
$page_title = 'Checkout';

$stmt = db()->prepare(
    'SELECT c.quantity, p.price FROM cart_items c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?'
);
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

if (!$items) { redirect('cart'); }

$grand_total = 0;
foreach ($items as $it) $grand_total += $it['price'] * $it['quantity'];

require __DIR__ . '/../includes/header.php';
?>
<div class="px-2 my-8 flex justify-center">
    <div class="w-full max-w-2xl bg-neutral-900 border border-neutral-800 rounded-3xl p-8 shadow-2xl">
        <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-2">Checkout</h2>
        <p class="text-neutral-400 mb-8 text-sm">Confirming order for <span class="font-bold text-indigo-400"><?= rupees($grand_total) ?></span>.</p>

        <form method="POST" action="<?= url('process_checkout') ?>" class="space-y-6">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">Delivery Address</label>
                <textarea name="address" required placeholder="Enter your full delivery address..."
                          class="w-full p-4 bg-neutral-950 border border-neutral-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none transition"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">Delivery Option</label>
                <select name="delivery_option" class="w-full p-4 bg-neutral-950 border border-neutral-700 text-white rounded-xl focus:border-indigo-500 focus:outline-none">
                    <option value="standard">Standard Delivery (3-5 days)</option>
                    <option value="express">Express Delivery (1-2 days)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">Payment Method</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center p-4 bg-neutral-950 border border-neutral-700 rounded-xl cursor-pointer hover:border-indigo-500 transition text-white">
                        <input type="radio" name="payment_method" value="cod" checked class="mr-3 accent-indigo-500"> COD
                    </label>
                    <label class="flex items-center p-4 bg-neutral-950 border border-neutral-700 rounded-xl cursor-pointer hover:border-indigo-500 transition text-white">
                        <input type="radio" name="payment_method" value="upi" class="mr-3 accent-indigo-500"> UPI
                    </label>
                    <label class="flex items-center p-4 bg-neutral-950 border border-neutral-700 rounded-xl cursor-pointer hover:border-indigo-500 transition text-white">
                        <input type="radio" name="payment_method" value="card" class="mr-3 accent-indigo-500"> CARD
                    </label>
                </div>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-black uppercase text-sm tracking-widest py-4 rounded-xl hover:bg-indigo-500 transition duration-300">
                Complete Order
            </button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
