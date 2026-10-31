<?php
require_login();
$page_title = 'Payment Successful';
ob_start(); ?>
    <div class="py-10">
        <h2 class="text-4xl font-black text-emerald-400 mb-4">Payment Successful!</h2>
        <p class="text-neutral-400">Thank you! Your order has been placed successfully and is being processed.</p>
        <a href="<?= url('profile') ?>" class="inline-block mt-6 bg-indigo-600 px-6 py-3 rounded-xl text-white font-bold hover:bg-indigo-500 transition">View My Orders</a>
    </div>
<?php $payment_content = ob_get_clean();
require __DIR__ . '/_payment_layout.php';
