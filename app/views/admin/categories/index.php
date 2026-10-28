<div class="admin-split">
    <form class="stack-form" method="post" action="<?= e(url('admin/categories')) ?>">
        <?= Csrf::field() ?>
        <h2>Add category</h2>
        <label>Name
            <input type="text" name="name" required>
        </label>
        <label>Slug
            <input type="text" name="slug" placeholder="auto from name">
        </label>
        <label>Parent
            <select name="parent_id">
                <option value="">Top level</option>
                <?php foreach ($parents as $parent): ?>
                    <option value="<?= (int) $parent['id'] ?>"><?= e($parent['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Sort
            <input type="number" name="sort_order" value="0">
        </label>
        <button class="btn btn-gold" type="submit">Add</button>
    </form>

    <div>
        <table class="data">
            <thead>
                <tr><th>Name</th><th>Slug</th><th>Parent</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <form method="post" action="<?= e(url('admin/categories/' . $category['id'])) ?>" class="inline-edit">
                                <?= Csrf::field() ?>
                                <input type="text" name="name" value="<?= e($category['name']) ?>">
                                <input type="text" name="slug" value="<?= e($category['slug']) ?>">
                                <input type="hidden" name="parent_id" value="<?= e((string) ($category['parent_id'] ?? '')) ?>">
                                <input type="hidden" name="sort_order" value="<?= (int) $category['sort_order'] ?>">
                                <button class="btn btn-ghost" type="submit">Save</button>
                            </form>
                        </td>
                        <td><?= e($category['slug']) ?></td>
                        <td><?= e($category['parent_name'] ?? '—') ?></td>
                        <td>
                            <form method="post" action="<?= e(url('admin/categories/' . $category['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this category?');">
                                <?= Csrf::field() ?>
                                <button class="linkish" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
