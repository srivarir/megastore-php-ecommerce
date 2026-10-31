<?php
/** Edit shipping address (creates the profile row if missing). */
require_login();
$page_title = 'Shipping Address';
$u = current_user();

$stmt = db()->prepare('SELECT * FROM profiles WHERE user_id = ?');
$stmt->execute([$u['id']]);
$profile = $stmt->fetch();

if (!$profile) {
    db()->prepare('INSERT INTO profiles (user_id, address, city) VALUES (?, NULL, NULL)')->execute([$u['id']]);
    $stmt->execute([$u['id']]);
    $profile = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $address = trim($_POST['address'] ?? '');
    $city    = trim($_POST['city'] ?? '');
    $upd = db()->prepare('UPDATE profiles SET address = ?, city = ? WHERE user_id = ?');
    $upd->execute([$address, $city, $u['id']]);
    redirect('profile');
}

require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto bg-neutral-900 p-8 rounded-3xl border border-neutral-800 mt-8">
    <h2 class="text-2xl font-black mb-6">Edit Shipping Address</h2>
    <form method="POST">
        <?= csrf_field() ?>
        <div class="space-y-4">
            <div>
                <label class="block text-sm text-neutral-400 mb-1">Street Address</label>
                <input type="text" name="address" value="<?= e($profile['address'] ?? '') ?>" class="w-full bg-neutral-800 p-4 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm text-neutral-400 mb-1">City</label>
                <input type="text" name="city" value="<?= e($profile['city'] ?? '') ?>" class="w-full bg-neutral-800 p-4 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
            </div>
            <button type="submit" class="w-full bg-indigo-600 py-4 rounded-xl font-bold hover:bg-indigo-500 transition">Save Address</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
