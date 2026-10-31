<?php
/** Shared page header. Expects $page_title optionally set. */
$u = current_user();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) . ' · ' : '' ?>E-Commerce Megastore</title>

    <!-- Tailwind (CDN) keeps the original look -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap bundle (requested in the stack) for components/JS utilities -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Swiper carousel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- Remix icons (used by contact/category pages) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style> .swiper-pagination-bullet { background:#6366f1 !important; } </style>
</head>
<body class="bg-neutral-950 text-neutral-100 min-h-screen flex flex-col antialiased">

<header class="sticky top-0 z-50 bg-neutral-900/80 backdrop-blur-lg border-b border-neutral-800 px-6 py-4">
    <div class="max-w-[1600px] mx-auto flex flex-wrap items-center justify-between gap-4">

        <a href="<?= url('home') ?>" class="text-white font-black text-2xl tracking-tighter hover:text-indigo-400 transition-colors">
            MEGA<span class="text-indigo-500">STORE</span>
        </a>

        <form action="<?= url('home') ?>" method="GET" class="flex-1 max-w-md flex items-center bg-neutral-900 border border-neutral-800 rounded-xl overflow-hidden focus-within:border-indigo-500 transition-all">
            <input type="hidden" name="page" value="home">
            <input type="text" name="search" placeholder="Search products..."
                   value="<?= e($_GET['search'] ?? '') ?>"
                   class="bg-transparent px-4 py-2.5 text-sm w-full focus:outline-none placeholder-neutral-500">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>

        <nav class="flex items-center gap-6 text-sm font-bold uppercase tracking-widest">
            <a href="<?= url('home') ?>" class="text-neutral-400 hover:text-white transition">Home</a>
            <a href="<?= url('compare') ?>" class="text-neutral-400 hover:text-white transition relative">
                Compare
                <?php $cmp = compare_count(); if ($cmp > 0): ?>
                    <span class="absolute -top-2 -right-3 bg-indigo-600 text-white text-[10px] rounded-full px-1.5 py-0.5"><?= $cmp ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= url('about') ?>" class="text-neutral-400 hover:text-white transition">About</a>
            <a href="<?= url('contact') ?>" class="text-neutral-400 hover:text-white transition">Contact</a>
            <a href="<?= url('cart') ?>" class="text-indigo-400 hover:text-indigo-300 transition relative">
                Cart
                <?php $cc = cart_count(); if ($cc > 0): ?>
                    <span class="absolute -top-2 -right-3 bg-indigo-600 text-white text-[10px] rounded-full px-1.5 py-0.5"><?= $cc ?></span>
                <?php endif; ?>
            </a>
            <?php if ($u): ?>
                <a href="<?= url('profile') ?>" class="text-white hover:text-indigo-400 transition">Profile</a>
                <a href="<?= url('logout') ?>" class="text-neutral-500 hover:text-white transition text-xs">(Logout)</a>
            <?php else: ?>
                <a href="<?= url('login') ?>" class="bg-indigo-600 px-4 py-2 rounded-lg text-white hover:bg-indigo-500 transition">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if ($flash): ?>
<div class="max-w-[1600px] mx-auto w-full px-6 mt-4">
    <div class="bg-indigo-900/40 border border-indigo-600/50 text-indigo-200 text-sm rounded-xl px-4 py-3">
        <?= e($flash) ?>
    </div>
</div>
<?php endif; ?>

<main class="flex-grow w-full max-w-[1600px] mx-auto px-6 py-8">
