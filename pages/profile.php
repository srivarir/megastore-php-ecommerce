<?php
/** Profile page: avatar, order-status filters, settings links, orders list. */
require_login();
$page_title = 'Profile';
$u = current_user();

$status = $_GET['status'] ?? null;
$allowed = ['pending', 'process', 'delivered'];

if ($status && in_array($status, $allowed, true)) {
    $stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? AND status = ? ORDER BY created_at DESC');
    $stmt->execute([$u['id'], $status]);
} else {
    $stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$u['id']]);
}
$orders = $stmt->fetchAll();

$avatar = 'https://ui-avatars.com/api/?name=' . urlencode(trim($u['first_name'] . ' ' . $u['last_name']) ?: $u['username']) . '&background=6366f1&color=fff&size=128';

require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto py-4 px-2 text-neutral-100">
    <div class="bg-neutral-900 border border-neutral-800 rounded-3xl p-8 text-center shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-rose-600 to-indigo-600 opacity-20"></div>
        <div class="relative z-10">
            <img src="<?= e($avatar) ?>" alt="Profile" class="w-24 h-24 rounded-full border-4 border-neutral-900 mx-auto mb-4">
            <h2 class="text-2xl font-bold"><?= e(trim($u['first_name'] . ' ' . $u['last_name']) ?: $u['username']) ?></h2>
            <p class="text-neutral-500 font-medium">Customer</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 my-8">
        <a href="<?= url('profile', ['status' => 'pending']) ?>" class="bg-neutral-900 p-4 rounded-2xl text-center border border-neutral-800 hover:border-indigo-500 transition">
            <div class="text-indigo-400 text-xl mb-1">📦</div>
            <p class="text-[10px] uppercase font-bold text-neutral-400">Pending</p>
        </a>
        <a href="<?= url('profile', ['status' => 'delivered']) ?>" class="bg-neutral-900 p-4 rounded-2xl text-center border border-neutral-800 hover:border-emerald-500 transition">
            <div class="text-emerald-400 text-xl mb-1">✅</div>
            <p class="text-[10px] uppercase font-bold text-neutral-400">Delivered</p>
        </a>
        <a href="<?= url('profile', ['status' => 'process']) ?>" class="bg-neutral-900 p-4 rounded-2xl text-center border border-neutral-800 hover:border-rose-500 transition">
            <div class="text-rose-400 text-xl mb-1">⚙️</div>
            <p class="text-[10px] uppercase font-bold text-neutral-400">Process</p>
        </a>
    </div>

    <div class="bg-neutral-900 rounded-2xl border border-neutral-800 divide-y divide-neutral-800 mb-8">
        <a href="<?= url('edit_profile') ?>" class="flex justify-between items-center p-5 hover:bg-neutral-800 transition">
            <span class="font-bold">Edit Profile</span><span class="text-neutral-500">→</span>
        </a>
        <a href="<?= url('edit_address') ?>" class="flex justify-between items-center p-5 hover:bg-neutral-800 transition">
            <span class="font-bold">Shipping Address</span><span class="text-neutral-500">→</span>
        </a>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold">My Orders</h3>
        <a href="<?= url('profile') ?>" class="text-xs text-indigo-400 hover:underline">Clear Filter</a>
    </div>

    <div class="space-y-4">
        <?php if ($orders): foreach ($orders as $order): ?>
        <div class="bg-neutral-900 p-5 rounded-2xl border border-neutral-800 flex justify-between items-center">
            <div>
                <p class="font-bold">Order #<?= (int)$order['id'] ?></p>
                <p class="text-xs text-neutral-500 capitalize"><?= e($order['status']) ?> • <?= date('M d, Y', strtotime($order['created_at'])) ?></p>
            </div>
            <a href="<?= url('order_detail', ['id' => $order['id']]) ?>" class="bg-neutral-800 px-4 py-2 rounded-xl text-xs font-bold hover:bg-neutral-700">Details</a>
        </div>
        <?php endforeach; else: ?>
        <p class="text-center text-neutral-500 py-4">No orders found.</p>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
