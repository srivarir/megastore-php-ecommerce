<?php
/** Category products listing. */
$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT * FROM categories WHERE slug = ?');
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    http_response_code(404);
    $page_title = 'Not found';
    require __DIR__ . '/../includes/header.php';
    echo '<p class="text-neutral-400 py-20 text-center">Category not found.</p>';
    require __DIR__ . '/../includes/footer.php';
    return;
}

$stmt = db()->prepare('SELECT * FROM products WHERE category_id = ? ORDER BY id');
$stmt->execute([$category['id']]);
$products = $stmt->fetchAll();

$page_title = $category['name'];
require __DIR__ . '/../includes/header.php';
?>
<div class="px-2 py-4">
    <div class="text-xs text-neutral-500 font-semibold mb-4">
        <a href="<?= url('home') ?>" class="hover:text-white transition">Home</a> &nbsp;/&nbsp;
        <span class="text-white font-bold"><?= e($category['name']) ?></span>
    </div>

    <h1 class="text-2xl font-black text-white uppercase tracking-tight mb-8"><?= e($category['name']) ?> Collection</h1>

    <?php if ($products): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <?php include __DIR__ . '/_product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20 bg-neutral-900 border border-neutral-800 rounded-xl">
            <i class="ri-stack-line text-4xl text-neutral-600 block mb-3"></i>
            <p class="text-sm text-neutral-400 font-bold">No products currently available under this category.</p>
            <a href="<?= url('home') ?>" class="text-xs text-indigo-400 font-extrabold underline mt-2 inline-block">Return Home</a>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
