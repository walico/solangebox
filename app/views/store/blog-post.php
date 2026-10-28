<section class="page-head">
    <div class="container narrow">
        <p class="eyebrow"><?= e($post['date']) ?></p>
        <h1><?= e($post['title']) ?></h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container narrow">
        <img class="post-hero" src="<?= e($post['image']) ?>" alt="">
        <?php foreach ($post['body'] as $paragraph): ?>
            <p class="lede"><?= e($paragraph) ?></p>
        <?php endforeach; ?>
        <p><a class="promo-link" href="<?= e(url('blog')) ?>">Back to the blog</a></p>
    </div>
</section>
