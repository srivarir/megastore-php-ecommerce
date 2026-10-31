<?php
/**
 * Renders the payment card wrapper (equivalent of payment_base.html).
 * Usage: set $payment_content (HTML string) then include this file.
 */
require __DIR__ . '/../includes/header.php';
?>
<div class="px-2 my-12 flex justify-center">
    <div class="w-full max-w-lg bg-neutral-900 border border-neutral-800 rounded-3xl p-8 shadow-2xl">
        <div class="mb-8 flex items-center justify-center gap-2">
            <div class="h-2 w-12 bg-indigo-600 rounded-full"></div>
            <div class="h-2 w-12 bg-indigo-600 rounded-full"></div>
            <div class="h-2 w-12 bg-neutral-800 rounded-full"></div>
        </div>
        <div class="text-left">
            <?= $payment_content ?? '' ?>
        </div>
        <div class="mt-8 pt-6 border-t border-neutral-800 text-center">
            <a href="<?= url('home') ?>" class="text-neutral-500 hover:text-white transition uppercase font-bold text-xs tracking-widest">
                &larr; Return to Homepage
            </a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
