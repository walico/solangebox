<?php
$slug = $category['slug'] ?? '';
?>
<svg class="cat-svg" viewBox="0 0 48 48" aria-hidden="true">
    <?php if ($slug === 'clothing'): ?>
        <path d="M16 14l8-4 8 4 4 6-6 3v15H18V23l-6-3 4-6z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
    <?php elseif ($slug === 'jewelry' || $slug === 'earrings'): ?>
        <circle cx="18" cy="22" r="7" fill="none" stroke="currentColor" stroke-width="1.8"/>
        <circle cx="30" cy="22" r="7" fill="none" stroke="currentColor" stroke-width="1.8"/>
        <circle cx="24" cy="14" r="2" fill="currentColor"/>
    <?php elseif ($slug === 'electronics'): ?>
        <rect x="16" y="8" width="16" height="28" rx="3" fill="none" stroke="currentColor" stroke-width="1.8"/>
        <path d="M22 32h4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    <?php elseif ($slug === 'hair'): ?>
        <path d="M24 8c6 8 10 14 10 22a10 10 0 0 1-20 0c0-8 4-14 10-22z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        <path d="M24 18v18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    <?php else: ?>
        <text x="24" y="30" text-anchor="middle" font-size="18"><?= e(substr($category['name'] ?? '?', 0, 1)) ?></text>
    <?php endif; ?>
</svg>
