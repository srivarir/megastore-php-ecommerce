<?php
/** Registration page. */
$page_title = 'Register';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $first    = trim($_POST['first_name'] ?? '');
    $last     = trim($_POST['last_name'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'That username is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins = db()->prepare(
                'INSERT INTO users (username, password, email, first_name, last_name, is_superuser)
                 VALUES (?, ?, ?, ?, ?, 0)'
            );
            $ins->execute([$username, $hash, $email, $first, $last]);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            redirect('home');
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-md mx-auto bg-neutral-900 p-8 rounded-3xl border border-neutral-800 mt-8">
    <h2 class="text-2xl font-black mb-6">Create Account</h2>
    <?php if ($error): ?>
        <div class="bg-red-900/40 border border-red-700 text-red-300 text-sm rounded-xl px-4 py-3 mb-4"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-neutral-400 mb-1">First Name</label>
                <input type="text" name="first_name" class="w-full bg-neutral-800 p-3 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm text-neutral-400 mb-1">Last Name</label>
                <input type="text" name="last_name" class="w-full bg-neutral-800 p-3 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
            </div>
        </div>
        <div>
            <label class="block text-sm text-neutral-400 mb-1">Username</label>
            <input type="text" name="username" required class="w-full bg-neutral-800 p-3 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm text-neutral-400 mb-1">Email</label>
            <input type="email" name="email" class="w-full bg-neutral-800 p-3 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm text-neutral-400 mb-1">Password</label>
            <input type="password" name="password" required class="w-full bg-neutral-800 p-3 rounded-xl border border-neutral-700 focus:border-indigo-500 outline-none">
        </div>
        <button type="submit" class="w-full bg-indigo-600 py-4 rounded-xl font-bold hover:bg-indigo-500 transition">Register</button>
    </form>
    <p class="text-neutral-500 text-sm mt-6 text-center">
        Already have an account? <a href="<?= url('login') ?>" class="text-indigo-400 hover:underline">Login</a>
    </p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
