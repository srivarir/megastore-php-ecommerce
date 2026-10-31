<?php
/** Homepage: hero carousel + categories grid + (optional) search results. */
$page_title = 'Home';
$search = trim($_GET['search'] ?? '');

$categories = db()->query('SELECT * FROM categories ORDER BY id')->fetchAll();

$products = [];
if ($search !== '') {
    // NOTE: positional placeholders (one bind per ?) because the PDO
    // connection runs with EMULATE_PREPARES = false, under which MySQL
    // does NOT allow the same named placeholder (:q) to be reused twice.
    // Reusing :q was the reason search silently errored out before.
    $like = '%' . $search . '%';
    $stmt = db()->prepare(
        'SELECT * FROM products
         WHERE name LIKE ? OR description LIKE ?
         ORDER BY name'
    );
    $stmt->execute([$like, $like]);
    $products = $stmt->fetchAll();
}

/* ----- Homepage feature sections (only needed when not searching) ----- */
$deal = null; $more_deals = []; $best_sellers = []; $new_launches = [];

// Helper: attach a believable "original" price + discount % to a product row.
// Discount is derived deterministically from the product id so it stays stable
// across page loads (no schema change needed).
function with_discount(array $p): array {
    $pcts = [15, 20, 25, 28, 30, 35, 40];
    $pct  = $pcts[$p['id'] % count($pcts)];
    $p['discount_pct'] = $pct;
    $p['orig_price']   = round(((float)$p['price']) / (1 - $pct / 100));
    return $p;
}

if ($search === '') {
    // Sale of the Day hero: the highest-priced product gets the spotlight.
    $hero = db()->query('SELECT * FROM products ORDER BY price DESC LIMIT 1')->fetch();
    $deal = $hero ? with_discount($hero) : null;

    // More deals: a spread of other products (skip the hero) shown as a deal grid.
    $hero_id = is_array($hero) ? (int)$hero['id'] : 0;
    $rows = db()->query(
        'SELECT * FROM products
          WHERE id <> ' . $hero_id . '
          ORDER BY price DESC
          LIMIT 4'
    )->fetchAll();
    $more_deals = array_map('with_discount', $rows);

    // Best Sellers: most-ordered products (falls back to newest if no orders yet).
    $best_sellers = db()->query(
        'SELECT p.*, COALESCE(SUM(oi.quantity),0) AS sold
           FROM products p
           LEFT JOIN order_items oi ON oi.product_id = p.id
          GROUP BY p.id
          ORDER BY sold DESC, p.id ASC
          LIMIT 4'
    )->fetchAll();

    // New Launches: most recently created products.
    $new_launches = db()->query(
        'SELECT * FROM products ORDER BY created_at DESC, id DESC LIMIT 4'
    )->fetchAll();
}

$extra_js = <<<JS
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper(".mySwiper", {
        slidesPerView: 1, spaceBetween: 0, loop: true, effect: "slide",
        speed: 700,
        autoplay: { delay: 4500, disableOnInteraction: false },
        pagination: { el: ".swiper-pagination", clickable: true },
        navigation: { nextEl: ".hero-next", prevEl: ".hero-prev" }
    });

    // Live countdown for the "Sale of the Day" block (resets each midnight).
    const cd = document.getElementById('deal-countdown');
    if (cd) {
        function tick() {
            const now = new Date();
            const end = new Date(); end.setHours(23,59,59,999);
            let s = Math.max(0, Math.floor((end - now) / 1000));
            const h = String(Math.floor(s/3600)).padStart(2,'0');
            const m = String(Math.floor((s%3600)/60)).padStart(2,'0');
            const ss = String(s%60).padStart(2,'0');
            cd.textContent = h + ':' + m + ':' + ss;
        }
        tick(); setInterval(tick, 1000);
    }
});
</script>
JS;

require __DIR__ . '/../includes/header.php';
?>

<?php if ($search !== ''): ?>
    <!-- Search results view -->
    <h1 class="text-2xl font-black text-white uppercase tracking-tight mb-8">
        Results for "<?= e($search) ?>"
    </h1>
    <?php if ($products): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <?php include __DIR__ . '/_product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-neutral-500 italic py-16 text-center">No products matched your search.</p>
    <?php endif; ?>

<?php else: ?>

<!-- Swiper Carousel -->
<div class="relative my-8 group/hero">
<div class="swiper mySwiper w-full rounded-2xl overflow-hidden shadow-2xl h-96">
    <div class="swiper-wrapper">
        <div class="swiper-slide bg-neutral-900 relative">
            <img src="<?= asset('images/phone.jfif') ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-black/40">
                <h2 class="text-5xl font-black text-white uppercase tracking-tighter">Latest Smartphones</h2>
                <a href="<?= url('category', ['slug' => 'mobile']) ?>" class="mt-6 bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-500">Shop Phones →</a>
            </div>
        </div>
        <div class="swiper-slide bg-neutral-900 relative">
            <img src="<?= asset('images/fashion.png') ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-black/40">
                <h2 class="text-5xl font-black text-white uppercase">Trendy Fashion</h2>
                <a href="<?= url('category', ['slug' => 'fashion']) ?>" class="mt-6 border-2 border-white text-white px-8 py-3 rounded-xl font-bold hover:bg-white hover:text-black">Browse Fashion →</a>
            </div>
        </div>
        <div class="swiper-slide bg-neutral-900 relative">
            <img src="<?= asset('images/house.png') ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-black/40">
                <h2 class="text-5xl font-black text-white uppercase">Home Comforts</h2>
                <a href="<?= url('category', ['slug' => 'home-living']) ?>" class="mt-6 bg-amber-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-amber-500">Shop Home →</a>
            </div>
        </div>
        <div class="swiper-slide bg-neutral-900 relative">
            <img src="<?= asset('images/electronics.jfif') ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-black/40">
                <h2 class="text-5xl font-black text-white uppercase">Gadget Flash Sale</h2>
                <a href="<?= url('category', ['slug' => 'headphones']) ?>" class="mt-6 bg-red-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-red-500">Claim Deals →</a>
            </div>
        </div>
        <div class="swiper-slide bg-neutral-900 relative">
            <img src="<?= asset('images/festival.jfif') ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-black/40">
                <h2 class="text-5xl font-black text-white uppercase">Festival Special</h2>
                <a href="<?= url('category', ['slug' => 'speakers']) ?>" class="mt-6 bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-500">See Offers →</a>
            </div>
        </div>
    </div>
    <div class="swiper-pagination"></div>
</div>

    <!-- Custom arrow controls (inline SVG so they render without the icon font) -->
    <button type="button" aria-label="Previous slide"
        class="hero-prev absolute left-4 top-1/2 -translate-y-1/2 z-20 h-12 w-12 flex items-center justify-center rounded-full bg-black/50 backdrop-blur border border-white/30 text-white shadow-lg hover:bg-indigo-600 hover:border-indigo-400 hover:scale-110 transition-all duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button type="button" aria-label="Next slide"
        class="hero-next absolute right-4 top-1/2 -translate-y-1/2 z-20 h-12 w-12 flex items-center justify-center rounded-full bg-black/50 backdrop-blur border border-white/30 text-white shadow-lg hover:bg-indigo-600 hover:border-indigo-400 hover:scale-110 transition-all duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>

<!-- ===================== VALUE / TRUST STRIP ===================== -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-4 my-10">
    <?php
    $trust = [
        ['Free Shipping', 'On orders over ₹4,999', 'M3 3h13v10H3zM16 8h4l1 5h-5z M6 17a2 2 0 100-4 2 2 0 000 4z M18 17a2 2 0 100-4 2 2 0 000 4z'],
        ['Easy Returns', '7-day no-questions policy', 'M4 4v6h6 M20 20v-6h-6 M5 14a7 7 0 0013 2 M19 10A7 7 0 006 8'],
        ['Secure Payments', '100% protected checkout', 'M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z M9 12l2 2 4-4'],
        ['24/7 Support', 'We are here to help', 'M3 12a9 9 0 1118 0v4a2 2 0 01-2 2h-1v-6h3 M3 12v4a2 2 0 002 2h1v-6H3'],
    ];
    foreach ($trust as $t): ?>
        <div class="flex items-center gap-3 bg-neutral-900 border border-neutral-800 rounded-2xl p-4">
            <span class="shrink-0 h-11 w-11 rounded-xl bg-indigo-600/15 text-indigo-400 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="<?= $t[2] ?>"/>
                </svg>
            </span>
            <div>
                <p class="text-white font-bold text-sm leading-tight"><?= $t[0] ?></p>
                <p class="text-neutral-500 text-xs"><?= $t[1] ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<!-- ===================== SALE OF THE DAY ===================== -->
<?php if ($deal):
    $sale = (float)$deal['price'];
    $orig = (float)$deal['orig_price'];
    $pct  = (int)$deal['discount_pct'];
?>
<section class="my-12">
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <span class="inline-flex items-center gap-2 bg-red-600 text-white text-xs font-black uppercase tracking-widest px-3 py-1.5 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h7l-1 8 10-12h-7z"/></svg>
            Sale of the Day
        </span>
        <span class="text-neutral-400 text-sm">Ends in
            <span id="deal-countdown" class="font-mono font-bold text-red-400">00:00:00</span>
        </span>
    </div>

    <!-- Hero deal -->
    <div class="relative overflow-hidden rounded-3xl border border-neutral-800 bg-gradient-to-br from-red-950/40 via-neutral-900 to-neutral-900">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-red-600/20 blur-3xl"></div>
        <div class="relative grid md:grid-cols-2 gap-6 p-6 md:p-10 items-center">
            <div class="bg-neutral-950/60 rounded-2xl h-80 flex items-center justify-center p-6">
                <img src="<?= media('products/' . rawurlencode($deal['image'])) ?>"
                     alt="<?= e($deal['name']) ?>" class="object-contain h-64">
            </div>
            <div>
                <span class="text-red-400 font-black text-6xl tracking-tighter"><?= $pct ?>%<span class="text-2xl align-top"> OFF</span></span>
                <h3 class="mt-4 text-2xl md:text-3xl font-black text-white leading-tight"><?= e($deal['name']) ?></h3>
                <p class="mt-3 text-neutral-400 text-sm line-clamp-2 max-w-md"><?= e($deal['description']) ?></p>
                <div class="mt-5 flex items-baseline gap-3">
                    <span class="text-3xl font-black text-white"><?= rupees($sale) ?></span>
                    <span class="text-lg text-neutral-500 line-through"><?= rupees($orig) ?></span>
                    <span class="text-emerald-400 text-sm font-bold">Save <?= rupees($orig - $sale) ?></span>
                </div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="<?= url('product', ['slug' => $deal['slug']]) ?>"
                       class="bg-white text-black px-7 py-3 rounded-xl font-bold hover:bg-neutral-200 transition">View Deal</a>
                    <a href="<?= url('add_to_cart', ['id' => $deal['id']]) ?>"
                       class="bg-red-600 text-white px-7 py-3 rounded-xl font-bold hover:bg-red-500 transition">Grab It Now →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- More deals grid -->
    <?php if ($more_deals): ?>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mt-6">
        <?php foreach ($more_deals as $d): ?>
        <div class="relative bg-neutral-900 border border-neutral-800 rounded-2xl p-4 flex flex-col group hover:border-red-500/60 transition">
            <span class="absolute top-3 left-3 z-10 bg-red-600 text-white text-[11px] font-black px-2 py-1 rounded-lg"><?= (int)$d['discount_pct'] ?>% OFF</span>
            <a href="<?= url('product', ['slug' => $d['slug']]) ?>" class="block">
                <div class="bg-neutral-950/60 rounded-xl h-40 flex items-center justify-center p-3 mb-3 overflow-hidden">
                    <img src="<?= media('products/' . rawurlencode($d['image'])) ?>" alt="<?= e($d['name']) ?>" class="object-contain h-32 group-hover:scale-105 transition duration-300">
                </div>
                <h4 class="text-white font-bold text-sm truncate"><?= e($d['name']) ?></h4>
            </a>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-white font-black text-base"><?= rupees($d['price']) ?></span>
                <span class="text-neutral-500 text-xs line-through"><?= rupees($d['orig_price']) ?></span>
            </div>
            <a href="<?= url('add_to_cart', ['id' => $d['id']]) ?>"
               class="mt-3 text-center bg-red-600/90 text-white text-xs uppercase font-bold tracking-wider px-3 py-2 rounded-lg hover:bg-red-500 transition">Add To Cart</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<!-- ===================== BEST SELLERS ===================== -->
<?php if ($best_sellers): ?>
<section class="my-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="flex items-center gap-2 text-xl font-black text-white uppercase tracking-tight">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c1 3-1 4-2 6-1 1.7-.5 4 1 5 .6-1 .8-2 .8-3 1.5 1 2.4 2.6 2.4 4.4A4.6 4.6 0 017.6 19 5 5 0 016 12c0-3 3-4 3-7 1 1 2 2 3-3z"/></svg>
            Best Sellers
        </h2>
        <span class="text-xs text-neutral-500 uppercase tracking-widest">Most loved by shoppers</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach ($best_sellers as $i => $product): ?>
            <div class="relative">
                <span class="absolute top-2 left-2 z-10 bg-amber-500 text-black text-[10px] font-black uppercase px-2 py-1 rounded-md tracking-wide">#<?= $i + 1 ?> Top</span>
                <?php include __DIR__ . '/_product_card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ===================== NEW LAUNCHES ===================== -->
<?php if ($new_launches): ?>
<section class="my-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="flex items-center gap-2 text-xl font-black text-white uppercase tracking-tight">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2 6 6 2-6 2-2 6-2-6-6-2 6-2z"/></svg>
            New Launches
        </h2>
        <span class="text-xs text-neutral-500 uppercase tracking-widest">Fresh on the shelves</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach ($new_launches as $product): ?>
            <div class="relative">
                <span class="absolute top-2 left-2 z-10 bg-indigo-600 text-white text-[10px] font-black uppercase px-2 py-1 rounded-md tracking-wide">New</span>
                <?php include __DIR__ . '/_product_card.php'; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ===================== CATEGORIES (redesigned) ===================== -->
<section id="categories" class="my-14">
    <div class="flex items-center justify-between mb-6">
        <h2 class="flex items-center gap-2 text-xl font-black text-white uppercase tracking-tight">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-400" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>
            Shop by Category
        </h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($categories as $cat): ?>
        <a href="<?= url('category', ['slug' => $cat['slug']]) ?>"
           class="group relative overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900 aspect-square block hover:border-indigo-500 transition-all duration-300">
            <?php if (!empty($cat['image'])): ?>
                <img src="<?= media('categories/' . rawurlencode($cat['image'])) ?>"
                     alt="<?= e($cat['name']) ?>"
                     class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-80 group-hover:scale-110 transition-all duration-500">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-4">
                <h3 class="font-black text-white text-base leading-tight"><?= e($cat['name']) ?></h3>
                <span class="mt-1 inline-flex items-center gap-1 text-xs font-bold text-indigo-300 opacity-0 -translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                    Explore <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
