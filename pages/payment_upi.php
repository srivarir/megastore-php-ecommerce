<?php
require_login();
$page_title = 'Pay via UPI';
ob_start(); ?>
    <h2 class="text-3xl font-black text-white mb-4">Pay via UPI</h2>
    <div class="p-8 bg-white rounded-xl mb-6 flex justify-center">
        <img src="<?= asset('images/qr-code.png') ?>" alt="UPI QR Code" class="w-48 h-48 border-4 border-indigo-600">
    </div>
    <p class="text-neutral-400">Scan this QR code with any UPI app (GPay, PhonePe, Paytm).</p>
    <form method="POST" action="<?= url('payment_success') ?>" class="mt-6">
        <?= csrf_field() ?>
        <button type="submit" class="w-full bg-indigo-600 py-3 rounded-xl text-white font-black uppercase tracking-widest hover:bg-indigo-500 transition">I've Paid</button>
    </form>
<?php $payment_content = ob_get_clean();
require __DIR__ . '/_payment_layout.php';
