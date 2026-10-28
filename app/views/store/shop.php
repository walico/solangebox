<?php
$heading = $on_sale ? 'Sale' : 'All products';
$kicker = 'Collections';
$bannerLead = $on_sale ? 'Pieces on sale' : 'Explore the collection';
$bannerText = $on_sale
    ? 'A shorter edit of clothes, jewelry, and extras, marked down for a while.'
    : 'Clothing, jewelry, electronics, and hair — chosen to sit together.';
$action = url('shop');
$currentSlug = '';
$showSale = true;
require ROOT_PATH . '/app/views/partials/catalog.php';
