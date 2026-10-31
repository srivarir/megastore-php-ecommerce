<?php
require_login();
$page_title = 'Card Payment';
ob_start(); ?>
<div class="text-left">
    <h2 class="text-3xl font-black text-white mb-6 text-center uppercase tracking-tighter">Enter Card Details</h2>
    <form method="POST" action="<?= url('payment_success') ?>" class="space-y-5">
        <?= csrf_field() ?>
        <div>
            <label class="block text-neutral-400 text-xs font-bold uppercase mb-2">Card Number</label>
            <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required
                   class="w-full p-4 bg-neutral-950 border border-neutral-800 text-white rounded-xl focus:border-indigo-500 outline-none transition placeholder:text-neutral-700">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-neutral-400 text-xs font-bold uppercase mb-2">Expiry</label>
                <input type="text" name="expiry" placeholder="MM/YY" maxlength="5" required
                       class="w-full p-4 bg-neutral-950 border border-neutral-800 text-white rounded-xl focus:border-indigo-500 outline-none transition placeholder:text-neutral-700">
            </div>
            <div>
                <label class="block text-neutral-400 text-xs font-bold uppercase mb-2">CVV</label>
                <input type="password" name="cvv" placeholder="***" maxlength="4" required
                       class="w-full p-4 bg-neutral-950 border border-neutral-800 text-white rounded-xl focus:border-indigo-500 outline-none transition placeholder:text-neutral-700">
            </div>
        </div>
        <div>
            <label class="block text-neutral-400 text-xs font-bold uppercase mb-2">Cardholder Name</label>
            <input type="text" name="name" placeholder="Your Name" required
                   class="w-full p-4 bg-neutral-950 border border-neutral-800 text-white rounded-xl focus:border-indigo-500 outline-none transition placeholder:text-neutral-700">
        </div>
        <button type="submit" class="w-full bg-indigo-600 py-4 mt-4 rounded-xl text-white font-black uppercase tracking-widest hover:bg-indigo-500 transition duration-300 shadow-lg shadow-indigo-900/20 active:scale-[0.98]">
            Pay Now
        </button>
    </form>
</div>
<?php $payment_content = ob_get_clean();
require __DIR__ . '/_payment_layout.php';
