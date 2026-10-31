<?php
/** Login page. */
$page_title = 'Login';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        redirect('home');
    } else {
        $error = 'Invalid username or password.';
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto bg-neutral-900 p-8 rounded-3xl border border-neutral-800 mt-8">
    <h2 class="text-2xl font-black mb-6">Login</h2>
    <?php if ($error): ?>
        <div class="bg-red-900/40 border border-red-700 text-red-300 text-sm rounded-xl px-4 py-3 mb-4"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm text-neutral-400 mb-1">Username</label>
            <input type="text" name="username" required class="w-full bg-neutral-800 p-4 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm text-neutral-400 mb-1">Password</label>
            <input type="password" name="password" required class="w-full bg-neutral-800 p-4 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
        </div>
        <button type="submit" class="w-full bg-indigo-600 py-4 rounded-xl font-bold hover:bg-indigo-500 transition">Login</button>
    </form>
    <p class="text-neutral-500 text-sm mt-6 text-center">
        No account? <a href="<?= url('register') ?>" class="text-indigo-400 hover:underline">Register</a>
    </p>
    <p class="text-neutral-600 text-xs mt-4 text-center">Demo login: <b>Shikha</b> / <b>password123</b></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
