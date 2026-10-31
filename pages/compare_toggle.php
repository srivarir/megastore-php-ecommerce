<?php
/** Toggle a product in/out of the compare list, then return where we came from. */
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $now_in = compare_toggle($id);
    $_SESSION['flash'] = $now_in
        ? 'Added to compare.'
        : 'Removed from compare.';
}

// Return to the referring page if it is local, otherwise the compare page.
$ref = $_SERVER['HTTP_REFERER'] ?? '';
if ($ref && strpos($ref, $_SERVER['HTTP_HOST'] ?? '') !== false) {
    header('Location: ' . $ref);
    exit;
}
redirect('compare');
