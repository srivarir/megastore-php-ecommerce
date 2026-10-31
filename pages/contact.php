<?php
$page_title = 'Contact';
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    // Demo only: in a real deployment, send mail / store the message here.
    $sent = true;
}
require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-5xl mx-auto px-2 py-12 text-white">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
        <div class="bg-[#101423] border border-[#2d3a66] rounded-2xl p-8 shadow-2xl">
            <span class="text-indigo-400 text-xs font-bold uppercase tracking-wider block mb-2">Direct Communication</span>
            <h2 class="text-3xl font-extrabold tracking-tight mb-6">Let's build something exceptional together.</h2>
            <p class="text-neutral-300 text-xs leading-relaxed mb-8">Have questions regarding custom workstation configurations, bulk enterprise orders, or open API pipelines? Get in touch with our team directly.</p>
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-[#1e2954] border border-[#3b4a80] rounded-lg flex items-center justify-center text-indigo-300 text-lg"><i class="ri-mail-send-line"></i></div>
                    <div><p class="text-neutral-400 text-[10px] uppercase font-bold tracking-widest">Operations Email</p><p class="text-sm font-bold text-white">contact@nexuslab.domain</p></div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-[#1e2954] border border-[#3b4a80] rounded-lg flex items-center justify-center text-indigo-300 text-lg"><i class="ri-phone-line"></i></div>
                    <div><p class="text-neutral-400 text-[10px] uppercase font-bold tracking-widest">Support Helpline</p><p class="text-sm font-bold text-white">+1 (555) 019-2834</p></div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-[#1e2954] border border-[#3b4a80] rounded-lg flex items-center justify-center text-indigo-300 text-lg"><i class="ri-map-pin-line"></i></div>
                    <div><p class="text-neutral-400 text-[10px] uppercase font-bold tracking-widest">Corporate Headquarters</p><p class="text-sm font-bold text-white">Grid Core Tower, Suite 101, Tech District</p></div>
                </div>
            </div>
        </div>

        <div class="bg-[#101423] border border-[#2d3a66] rounded-2xl p-8 shadow-2xl">
            <h3 class="text-lg font-bold text-white mb-2">Dispatch a Digital Ticket</h3>
            <p class="text-neutral-300 text-xs mb-6">Fill out the fields below, and an optimization engineer will reply within 4 operating hours.</p>
            <?php if ($sent): ?>
                <div class="bg-emerald-900/40 border border-emerald-700 text-emerald-300 text-sm rounded-xl px-4 py-3 mb-4">Message transmitted. We'll be in touch shortly.</div>
            <?php endif; ?>
            <form method="POST" class="space-y-5">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-neutral-400 mb-1.5">Your Full Name</label>
                    <input type="text" name="name" required placeholder="Your Name" class="w-full bg-[#171e36] border border-[#3b4a80] text-white font-medium text-xs px-4 py-3 rounded-xl outline-none focus:border-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-neutral-400 mb-1.5">Your Email Address</label>
                    <input type="email" name="email" required placeholder="Your Email" class="w-full bg-[#171e36] border border-[#3b4a80] text-white font-medium text-xs px-4 py-3 rounded-xl outline-none focus:border-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-neutral-400 mb-1.5">Message Payload</label>
                    <textarea name="message" rows="4" required placeholder="Describe your technical inquiry in detail..." class="w-full bg-[#171e36] border border-[#3b4a80] text-white font-medium text-xs px-4 py-3 rounded-xl outline-none focus:border-indigo-400 transition"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white text-xs font-bold tracking-wider uppercase py-3.5 rounded-xl hover:bg-indigo-500 transition duration-200">Transmit Secure Message</button>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
