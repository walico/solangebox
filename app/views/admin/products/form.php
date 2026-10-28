<?php
$p = $product ?? [];
$isEdit = !empty($p);
?>
<form class="stack-form admin-form" method="post" action="<?= e($isEdit ? url('admin/products/' . $p['id']) : url('admin/products')) ?>" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <div class="form-grid">
        <label>Name
            <input type="text" name="name" required value="<?= e(old('name', $p['name'] ?? '')) ?>">
        </label>
        <label>Slug
            <input type="text" name="slug" value="<?= e(old('slug', $p['slug'] ?? '')) ?>" placeholder="auto from name">
        </label>
        <label>Category
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) old('category_id', $p['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= e(($category['parent_id'] ? '— ' : '') . $category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Status
            <select name="status">
                <option value="active" <?= old('status', $p['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="hidden" <?= old('status', $p['status'] ?? '') === 'hidden' ? 'selected' : '' ?>>Hidden</option>
            </select>
        </label>
        <label>Price (MWK)
            <input type="number" name="price" min="0" required value="<?= e((string) old('price', $p['price'] ?? '')) ?>">
        </label>
        <label>Sale price
            <input type="number" name="sale_price" min="0" value="<?= e((string) old('sale_price', $p['sale_price'] ?? '')) ?>">
        </label>
        <label>Stock
            <input type="number" name="stock" min="0" value="<?= e((string) old('stock', $p['stock'] ?? 0)) ?>">
        </label>
    </div>
    <label>Description
        <textarea name="description" rows="5"><?= e(old('description', $p['description'] ?? '')) ?></textarea>
    </label>

    <h2>Images</h2>
    <?php if ($images): ?>
        <div class="image-row">
            <?php foreach ($images as $image): ?>
                <figure>
                    <img src="<?= e(uploaded($image['path'])) ?>" alt="">
                    <?php if ($isEdit): ?>
                        <button class="linkish" type="submit" formaction="<?= e(url('admin/products/' . $p['id'] . '/images/delete')) ?>" name="image_id" value="<?= (int) $image['id'] ?>">Remove</button>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <label>Add images
        <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
    </label>

    <h2>Variants</h2>
    <p class="muted">Size, colour, extra price, and stock. Leave blank if the product has no options.</p>
    <div id="variant-rows" class="repeat-rows">
        <?php
        $variantRows = $variants ?: [['size' => '', 'color' => '', 'extra_price' => 0, 'stock' => 0]];
        foreach ($variantRows as $i => $variant):
        ?>
            <div class="repeat-row">
                <input type="text" name="variants[<?= $i ?>][size]" placeholder="Size" value="<?= e((string) ($variant['size'] ?? '')) ?>">
                <input type="text" name="variants[<?= $i ?>][color]" placeholder="Colour" value="<?= e((string) ($variant['color'] ?? '')) ?>">
                <input type="number" name="variants[<?= $i ?>][extra_price]" placeholder="Extra" value="<?= e((string) ($variant['extra_price'] ?? 0)) ?>">
                <input type="number" name="variants[<?= $i ?>][stock]" placeholder="Stock" value="<?= e((string) ($variant['stock'] ?? 0)) ?>">
            </div>
        <?php endforeach; ?>
    </div>
    <button class="btn btn-ghost" type="button" data-repeat="variant-rows">Add variant</button>

    <h2>Specifications</h2>
    <div id="spec-rows" class="repeat-rows">
        <?php
        $specRows = $specs ?: [['label' => '', 'value' => '']];
        foreach ($specRows as $i => $spec):
        ?>
            <div class="repeat-row">
                <input type="text" name="specs[<?= $i ?>][label]" placeholder="Label" value="<?= e($spec['label'] ?? '') ?>">
                <input type="text" name="specs[<?= $i ?>][value]" placeholder="Value" value="<?= e($spec['value'] ?? '') ?>">
            </div>
        <?php endforeach; ?>
    </div>
    <button class="btn btn-ghost" type="button" data-repeat="spec-rows">Add spec</button>

    <button class="btn btn-gold" type="submit"><?= $isEdit ? 'Save product' : 'Create product' ?></button>
</form>
