<?php
$heading = $category['name'];
$kicker = 'Collections';
$bannerLead = $category['name'];
$bannerText = 'A considered edit from this collection, ready for everyday wear.';
$action = url('category/' . $category['slug']);
$currentSlug = $category['slug'];
$showSale = false;
$on_sale = false;
require ROOT_PATH . '/app/views/partials/catalog.php';
