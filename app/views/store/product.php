<?php
$onSale = !empty($product['sale_price']) && (int) $product['sale_price'] < (int) $product['price'];
$main = $images[0]['path'] ?? null;
?>
<section class="section product-page">
    <div class="container product-layout">
        <div class="gallery">
            <div class="gallery-main">
                <?php if ($main): ?>
                    <img id="gallery-main" src="<?= e(uploaded($main)) ?>" alt="<?= e($product['name']) ?>">
                <?php else: ?>
                    <div class="media-fallback large"><?= e(substr($product['name'], 0, 1)) ?></div>
                <?php endif; ?>
            </div>
            <?php if (count($images) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($images as $image): ?>
                        <button type="button" class="thumb" data-src="<?= e(uploaded($image['path'])) ?>">
                            <img src="<?= e(uploaded($image['path'])) ?>" alt="">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <p class="eyebrow"><a href="<?= e(url('category/' . $product['category_slug'])) ?>"><?= e($product['category_name']) ?></a></p>
            <h1><?= e($product['name']) ?></h1>
            <p class="price price-lg">
                <?php if ($onSale): ?>
                    <span class="sale-badge">Sale</span>
                    <span class="price-now"><?= e(money($product['sale_price'])) ?></span>
                    <span class="price-was"><?= e(money($product['price'])) ?></span>
                <?php else: ?>
                    <span class="price-now"><?= e(money($product['price'])) ?></span>
                <?php endif; ?>
            </p>
            <p class="lede"><?= nl2br(e($product['description'])) ?></p>

            <form method="post" action="<?= e(url('cart/add')) ?>" class="buy-form">
                <?= Csrf::field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

                <?php if ($variants): ?>
                    <label>
                        Option
                        <select name="variant_id" required>
                            <option value="">Choose size / colour</option>
                            <?php foreach ($variants as $variant): ?>
                                <?php
                                $label = variant_label($variant);
                                $extra = (int) $variant['extra_price'];
                                $priceNote = $extra > 0 ? ' (+' . money($extra) . ')' : '';
                                ?>
                                <option value="<?= (int) $variant['id'] ?>">
                                    <?= e($label) ?><?= e($priceNote) ?>
                                    <?= (int) $variant['stock'] < 1 ? ' — out of stock' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endif; ?>

                <label>
                    Quantity
                    <input type="number" name="qty" min="1" value="1">
                </label>

                <button class="btn btn-gold" type="submit" <?= (int) $product['stock'] < 1 ? 'disabled' : '' ?>>
                    <?= (int) $product['stock'] < 1 ? 'Out of stock' : 'Add to cart' ?>
                </button>
            </form>

            <?php if ($specs): ?>
                <table class="spec-table">
                    <?php foreach ($specs as $spec): ?>
                        <tr>
                            <th><?= e($spec['label']) ?></th>
                            <td><?= e($spec['value']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>
