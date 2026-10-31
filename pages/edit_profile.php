<?php
/** Edit first/last name. */
require_login();
$page_title = 'Edit Profile';
$u = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $stmt = db()->prepare('UPDATE users SET first_name = ?, last_name = ? WHERE id = ?');
    $stmt->execute([$first, $last, $u['id']]);
    redirect('profile');
}

require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto bg-neutral-900 p-8 rounded-3xl border border-neutral-800 mt-8">
    <h2 class="text-2xl font-black mb-6">Edit Profile</h2>
    <form method="POST">
        <?= csrf_field() ?>
        <div class="space-y-4">
            <div>
                <label class="block text-sm text-neutral-400 mb-1">First Name</label>
                <input type="text" name="first_name" value="<?= e($u['first_name']) ?>" class="w-full bg-neutral-800 p-4 rounded-xl border border-neutral-700">
            </div>
            <div>
                <label class="block text-sm text-neutral-400 mb-1">Last Name</label>
                <input type="text" name="last_name" value="<?= e($u['last_name']) ?>" class="w-full bg-neutral-800 p-4 rounded-xl border border-neutral-700">
            </div>
            <button type="submit" class="w-full bg-indigo-600 py-4 rounded-xl font-bold hover:bg-indigo-500 transition">Save Profile</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
