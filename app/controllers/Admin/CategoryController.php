<?php

declare(strict_types=1);

final class AdminCategoryController
{
    public function index(): void
    {
        Auth::requireLogin();
        layout('admin', 'admin/categories/index', [
            'title' => 'Categories',
            'categories' => Category::all(),
            'parents' => Category::roots(),
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            flash('error', 'Category name is required.');
            redirect('admin/categories');
        }
        Category::create([
            'name' => $name,
            'slug' => Category::uniqueSlug((string) ($_POST['slug'] ?: $name)),
            'parent_id' => ($_POST['parent_id'] ?? '') !== '' ? (int) $_POST['parent_id'] : null,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ]);
        flash('success', 'Category added.');
        redirect('admin/categories');
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $category = Category::find((int) $id);
        if (!$category) {
            redirect('admin/categories');
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        $parentId = $_POST['parent_id'] !== '' ? (int) $_POST['parent_id'] : null;
        if ($parentId === (int) $id) {
            $parentId = null;
        }
        Category::update((int) $id, [
            'name' => $name,
            'slug' => Category::uniqueSlug((string) ($_POST['slug'] ?: $name), (int) $id),
            'parent_id' => $parentId,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ]);
        flash('success', 'Category saved.');
        redirect('admin/categories');
    }

    public function destroy(string $id): void
    {
        Auth::requireLogin();
        Csrf::verify();
        try {
            Category::delete((int) $id);
            flash('success', 'Category deleted.');
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('admin/categories');
    }
}
