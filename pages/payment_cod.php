<?php
require_login();
$page_title = 'Order Confirmed';
ob_start(); ?>
    <h2 class="text-3xl font-black text-white mb-4">Order Confirmed!</h2>
    <div class="p-8 bg-neutral-950 border border-neutral-700 rounded-xl mb-6">
        <p class="text-emerald-400 font-bold text-xl">Cash on Delivery</p>
        <p class="text-neutral-400 mt-2">Your order has been placed successfully. Please keep the exact amount ready upon delivery.</p>
    </div>
<?php $payment_content = ob_get_clean();
require __DIR__ . '/_payment_layout.php';
