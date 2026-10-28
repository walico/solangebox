<section class="page-head">
    <div class="container">
        <p class="eyebrow">Journal</p>
        <h1>Our blog</h1>
    </div>
</section>

<section class="section section-tight">
    <div class="container">
        <div class="blog-row">
            <?php foreach ($posts as $post): ?>
                <article class="blog-card">
                    <a class="blog-media" href="<?= e(url('blog/' . $post['slug'])) ?>">
                        <img src="<?= e($post['image']) ?>" alt="">
                    </a>
                    <div class="blog-copy">
                        <p class="blog-date"><?= e($post['date']) ?></p>
                        <h3><a href="<?= e(url('blog/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h3>
                        <p><?= e($post['excerpt']) ?></p>
                        <a class="promo-link" href="<?= e(url('blog/' . $post['slug'])) ?>">Read more</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
