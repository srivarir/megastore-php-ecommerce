<?php
/**
 * Product comparison page.
 *
 * Works entirely off the existing schema (no new columns):
 *   - price            -> products.price
 *   - category         -> categories.name (joined)
 *   - availability     -> products.stock
 *   - rating / reviews -> aggregated from the reviews table
 *   - "type"           -> derived from the category name
 * Products are chosen via a session-based compare list (see bootstrap.php).
 */
$page_title = 'Compare Products';

$ids = compare_ids();
$items = [];

if ($ids) {
    // Build a safe IN(...) list of integer placeholders.
    $place = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare(
        "SELECT p.*, c.name AS category_name,
                COALESCE(AVG(r.rating), 0) AS avg_rating,
                COUNT(r.id)               AS review_count
           FROM products p
           LEFT JOIN categories c ON c.id = p.category_id
           LEFT JOIN reviews   r ON r.product_id = p.id
          WHERE p.id IN ($place)
          GROUP BY p.id"
    );
    $stmt->execute(array_map('intval', $ids));
    $rows = $stmt->fetchAll();

    // Preserve the order the user added them in.
    $byId = [];
    foreach ($rows as $r) { $byId[(int)$r['id']] = $r; }
    foreach ($ids as $id) {
        if (isset($byId[(int)$id])) $items[] = $byId[(int)$id];
    }
}

// Highlight helpers: which product wins each metric.
$cheapest_id = null; $top_rated_id = null;
if ($items) {
    $min = PHP_INT_MAX; $best = -1;
    foreach ($items as $it) {
        if ((float)$it['price'] < $min) { $min = (float)$it['price']; $cheapest_id = (int)$it['id']; }
        if ((float)$it['avg_rating'] > $best) { $best = (float)$it['avg_rating']; $top_rated_id = (int)$it['id']; }
    }
}

function stars_html($rating) {
    $rating = (float)$rating;
    $full = (int)round($rating);
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $cls = $i <= $full ? 'text-amber-400' : 'text-neutral-700';
        $out .= '<svg class="inline h-4 w-4 ' . $cls . '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 6.5 7 .9-5 4.8 1.3 7L12 17.8 5.4 21.2 6.7 14.2 1.7 9.4l7-.9z"/></svg>';
    }
    return $out;
}

require __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-white">Compare Products</h1>
        <p class="text-neutral-500 text-sm mt-1">Add up to <?= COMPARE_MAX ?> products side by side. <?= compare_count() ?> selected.</p>
    </div>
    <a href="<?= url('home') ?>" class="text-sm text-indigo-400 hover:text-indigo-300">← Keep shopping</a>
</div>

<?php if (!$items): ?>
    <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-16 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-neutral-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <p class="text-neutral-400 mb-2 font-bold">Your compare list is empty.</p>
        <p class="text-neutral-600 text-sm mb-6">Open any product or browse the homepage and tap <span class="text-neutral-300">“Compare”</span> to add it here.</p>
        <a href="<?= url('home') ?>" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-500 transition">Browse Products</a>
    </div>
<?php else:
    // Each comparison row: [label, callback returning cell HTML, optional "winner id" key]
    $cols = count($items);
?>
    <div class="overflow-x-auto rounded-2xl border border-neutral-800">
        <table class="w-full text-left border-collapse min-w-[640px]">
            <thead>
                <tr class="bg-neutral-900">
                    <th class="p-4 w-40 text-neutral-500 text-xs font-bold uppercase tracking-widest align-bottom">Product</th>
                    <?php foreach ($items as $it): ?>
                        <th class="p-4 align-top border-l border-neutral-800">
                            <div class="bg-neutral-950 rounded-xl h-32 flex items-center justify-center p-3 mb-3">
                                <img src="<?= media('products/' . rawurlencode($it['image'])) ?>" alt="<?= e($it['name']) ?>" class="object-contain h-28">
                            </div>
                            <a href="<?= url('product', ['slug' => $it['slug']]) ?>" class="block text-white font-bold text-sm leading-tight hover:text-indigo-400 transition"><?= e($it['name']) ?></a>
                            <a href="<?= url('compare_toggle', ['id' => $it['id']]) ?>" class="inline-block mt-2 text-[11px] text-red-400 hover:text-red-300">Remove ✕</a>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="text-sm">
                <!-- Price -->
                <tr class="border-t border-neutral-800">
                    <td class="p-4 text-neutral-500 font-bold">Price</td>
                    <?php foreach ($items as $it):
                        $win = (int)$it['id'] === $cheapest_id; ?>
                        <td class="p-4 border-l border-neutral-800 <?= $win ? 'bg-emerald-900/15' : '' ?>">
                            <span class="font-black text-white text-base"><?= rupees($it['price']) ?></span>
                            <?php if ($win): ?><span class="ml-2 text-[10px] bg-emerald-600 text-white px-2 py-0.5 rounded-full font-bold uppercase">Best Price</span><?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <!-- Rating -->
                <tr class="border-t border-neutral-800">
                    <td class="p-4 text-neutral-500 font-bold">Rating</td>
                    <?php foreach ($items as $it):
                        $win = (int)$it['id'] === $top_rated_id && (float)$it['avg_rating'] > 0; ?>
                        <td class="p-4 border-l border-neutral-800 <?= $win ? 'bg-amber-900/15' : '' ?>">
                            <?php if ((float)$it['avg_rating'] > 0): ?>
                                <?= stars_html($it['avg_rating']) ?>
                                <div class="text-neutral-400 text-xs mt-1"><?= number_format((float)$it['avg_rating'], 1) ?> / 5
                                    <?php if ($win): ?><span class="ml-1 text-[10px] bg-amber-500 text-black px-2 py-0.5 rounded-full font-bold uppercase">Top Rated</span><?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-neutral-600 text-xs">No ratings yet</span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <!-- Reviews -->
                <tr class="border-t border-neutral-800">
                    <td class="p-4 text-neutral-500 font-bold">Reviews</td>
                    <?php foreach ($items as $it): ?>
                        <td class="p-4 border-l border-neutral-800 text-neutral-300"><?= (int)$it['review_count'] ?> review<?= (int)$it['review_count'] === 1 ? '' : 's' ?></td>
                    <?php endforeach; ?>
                </tr>
                <!-- Category -->
                <tr class="border-t border-neutral-800">
                    <td class="p-4 text-neutral-500 font-bold">Category</td>
                    <?php foreach ($items as $it): ?>
                        <td class="p-4 border-l border-neutral-800 text-neutral-300"><?= e($it['category_name'] ?? '—') ?></td>
                    <?php endforeach; ?>
                </tr>
                <!-- Availability -->
                <tr class="border-t border-neutral-800">
                    <td class="p-4 text-neutral-500 font-bold">Availability</td>
                    <?php foreach ($items as $it):
                        $stock = (int)$it['stock']; ?>
                        <td class="p-4 border-l border-neutral-800">
                            <?php if ($stock > 10): ?>
                                <span class="text-emerald-400 font-bold">In stock</span>
                            <?php elseif ($stock > 0): ?>
                                <span class="text-amber-400 font-bold">Only <?= $stock ?> left</span>
                            <?php else: ?>
                                <span class="text-red-400 font-bold">Out of stock</span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <!-- Action -->
                <tr class="border-t border-neutral-800">
                    <td class="p-4"></td>
                    <?php foreach ($items as $it): ?>
                        <td class="p-4 border-l border-neutral-800">
                            <a href="<?= url('add_to_cart', ['id' => $it['id']]) ?>"
                               class="block text-center bg-indigo-600 text-white text-xs uppercase font-bold tracking-wider px-3 py-2.5 rounded-lg hover:bg-indigo-500 transition">Add To Cart</a>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
