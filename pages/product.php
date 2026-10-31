<?php
/** Product detail page: image, add-to-cart, reviews, rating summary. */
$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT * FROM products WHERE slug = ?');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $page_title = 'Not found';
    require __DIR__ . '/../includes/header.php';
    echo '<p class="text-neutral-400 py-20 text-center">Product not found.</p>';
    require __DIR__ . '/../includes/footer.php';
    return;
}

// Reviews
$stmt = db()->prepare(
    'SELECT r.*, u.username FROM reviews r JOIN users u ON u.id = r.user_id
     WHERE r.product_id = ? ORDER BY r.created_at DESC'
);
$stmt->execute([$product['id']]);
$reviews = $stmt->fetchAll();

$total_reviews = count($reviews);
$rating_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
foreach ($reviews as $rv) {
    $rating_counts[(int)$rv['rating']]++;
}

$page_title = $product['name'];
require __DIR__ . '/../includes/header.php';
?>
<div class="max-w-6xl mx-auto px-2 py-6">
    <div class="grid md:grid-cols-2 gap-12 items-start">
        <div class="bg-neutral-900 border border-neutral-800 rounded-3xl p-8 flex items-center justify-center">
            <img src="<?= media('products/' . rawurlencode($product['image'])) ?>" alt="<?= e($product['name']) ?>" class="max-h-96 object-contain">
        </div>

        <div>
            <h1 class="text-4xl font-black text-white mb-4"><?= e($product['name']) ?></h1>
            <p class="text-2xl font-bold text-indigo-400 mb-6"><?= rupees($product['price']) ?></p>
            <p class="text-neutral-400 mb-8 leading-relaxed"><?= e($product['description']) ?></p>

            <form action="<?= url('add_to_cart', ['id' => $product['id']]) ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="w-full bg-indigo-600 py-4 rounded-xl text-white font-black uppercase tracking-widest hover:bg-indigo-500 transition shadow-lg shadow-indigo-900/20">
                    Add to Cart
                </button>
            </form>

            <a href="<?= url('compare_toggle', ['id' => $product['id']]) ?>"
               class="mt-3 w-full flex items-center justify-center gap-2 py-3.5 rounded-xl border font-bold uppercase tracking-widest text-sm transition <?= in_compare($product['id']) ? 'bg-indigo-600/15 border-indigo-500 text-indigo-300' : 'border-neutral-700 text-neutral-300 hover:border-indigo-500 hover:text-indigo-400' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 7l3-3M8 7l3 3M16 17H4M16 17l-3-3M16 17l-3 3"/></svg>
                <?= in_compare($product['id']) ? 'Added to Compare' : 'Add to Compare' ?>
            </a>

            <a href="<?= url('home') ?>" class="block text-center mt-6 text-neutral-500 hover:text-white transition">← Back to Home</a>
        </div>
    </div>

    <div class="mt-16 border-t border-neutral-800 pt-12">
        <h2 class="text-2xl font-black text-white mb-8">Customer Reviews</h2>

        <div class="mb-12 bg-neutral-900 p-6 rounded-2xl border border-neutral-800">
            <h3 class="text-lg font-bold text-white mb-4">Rating Summary</h3>
            <div class="space-y-2">
                <?php for ($i = 5; $i >= 1; $i--):
                    $pct = $total_reviews ? round($rating_counts[$i] / $total_reviews * 100) : 0; ?>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-neutral-400 w-12"><?= $i ?> Star</span>
                    <div class="flex-1 h-4 bg-neutral-950 rounded-full overflow-hidden">
                        <div class="bg-indigo-600 h-full" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php if (is_logged_in()): ?>
        <div class="bg-neutral-900 p-6 rounded-2xl border border-neutral-800 mb-8">
            <h3 class="font-bold text-white mb-4">Leave a Review</h3>
            <form method="POST" action="<?= url('review_add', ['id' => $product['id']]) ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div class="w-full">
                    <label class="block text-neutral-400 text-sm mb-2">Your Review</label>
                    <textarea name="comment" class="w-full bg-neutral-800 border border-neutral-700 rounded-lg p-3 text-white" rows="3" required></textarea>
                </div>
                <div class="flex items-center gap-4">
                    <label class="text-neutral-400 font-bold">Rating (1-5):</label>
                    <input type="number" name="rating" min="1" max="5" class="bg-neutral-800 border border-neutral-700 rounded-lg p-2 text-white w-20" required>
                    <button type="submit" class="bg-indigo-600 px-6 py-2 rounded-lg text-white font-bold hover:bg-indigo-500 transition">Submit</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if ($reviews): foreach ($reviews as $review): ?>
            <div class="bg-neutral-900 p-6 rounded-2xl border border-neutral-800">
                <div class="flex justify-between mb-2">
                    <span class="font-bold text-white"><?= e($review['username']) ?></span>
                    <span class="text-indigo-400">
                        <?php for ($i = 1; $i <= 5; $i++) echo $i <= (int)$review['rating'] ? '★' : '☆'; ?>
                    </span>
                </div>
                <p class="text-neutral-300"><?= e($review['comment']) ?></p>
            </div>
            <?php endforeach; else: ?>
            <p class="text-neutral-500 italic">No reviews yet. Be the first to share your thoughts!</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
