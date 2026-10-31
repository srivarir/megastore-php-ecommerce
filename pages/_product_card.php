<?php /** Reusable product card. Expects $product in scope. */ ?>
<div id="product-<?= (int)$product['id'] ?>" class="bg-neutral-900 border border-neutral-800 rounded-xl p-4 group flex flex-col justify-between transition hover:shadow-md hover:border-indigo-500">
    <a href="<?= url('product', ['slug' => $product['slug']]) ?>">
        <div class="bg-neutral-800 rounded-lg w-full h-44 flex items-center justify-center p-4 overflow-hidden mb-3">
            <img src="<?= media('products/' . rawurlencode($product['image'])) ?>" alt="<?= e($product['name']) ?>" class="object-contain h-36 group-hover:scale-105 transition duration-300">
        </div>
        <h3 class="font-extrabold text-sm text-white truncate"><?= e($product['name']) ?></h3>
        <p class="text-neutral-400 text-xs line-clamp-2 mt-1 mb-3"><?= e($product['description']) ?></p>
    </a>
    <div class="flex items-center justify-between pt-3 mt-auto border-t border-neutral-800">
        <span class="font-black text-sm text-white"><?= rupees($product['price']) ?></span>
        <div class="flex items-center gap-1.5">
            <a href="<?= url('compare_toggle', ['id' => $product['id']]) ?>"
               title="<?= in_compare($product['id']) ? 'Remove from compare' : 'Add to compare' ?>"
               class="h-8 w-8 flex items-center justify-center rounded-md border transition <?= in_compare($product['id']) ? 'bg-indigo-600 border-indigo-500 text-white' : 'border-neutral-700 text-neutral-400 hover:border-indigo-500 hover:text-indigo-400' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 7l3-3M8 7l3 3M16 17H4M16 17l-3-3M16 17l-3 3"/></svg>
            </a>
            <a href="<?= url('add_to_cart', ['id' => $product['id']]) ?>"
               class="bg-indigo-600 text-white text-[10px] uppercase font-bold tracking-wider px-3 py-2 rounded-md hover:bg-indigo-500 transition">
                Add To Cart
            </a>
        </div>
    </div>
</div>
