<?php
/**
 * Front controller. All requests route through index.php?page=...
 * This mirrors the Django urls.py mapping.
 */
require_once __DIR__ . '/includes/bootstrap.php';

$page = $_GET['page'] ?? 'home';

// Whitelist of routes -> page file in /pages
$routes = [
    'home'            => 'home.php',
    'category'        => 'category.php',
    'product'         => 'product.php',
    'compare'         => 'compare.php',
    'compare_toggle'  => 'compare_toggle.php',
    'about'           => 'about.php',
    'contact'         => 'contact.php',

    'login'           => 'login.php',
    'register'        => 'register.php',
    'logout'          => 'logout.php',

    'cart'            => 'cart.php',
    'add_to_cart'     => 'add_to_cart.php',
    'remove_from_cart'=> 'remove_from_cart.php',
    'update_cart'     => 'update_cart.php',

    'checkout'        => 'checkout.php',
    'process_checkout'=> 'process_checkout.php',
    'payment_cod'     => 'payment_cod.php',
    'payment_upi'     => 'payment_upi.php',
    'payment_card'    => 'payment_card_details.php',
    'payment_success' => 'payment_success.php',

    'profile'         => 'profile.php',
    'edit_profile'    => 'edit_profile.php',
    'edit_address'    => 'edit_address.php',
    'order_detail'    => 'order_detail.php',

    'review_add'      => 'review_add.php',
];

if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'home';
}

require __DIR__ . '/pages/' . $routes[$page];
