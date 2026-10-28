<?php

declare(strict_types=1);

final class StoreController
{
    public function home(): void
    {
        $products = Product::paginate(['status' => 'active', 'sort' => 'newest']);
        $roots = [];
        foreach (Category::all() as $category) {
            $roots[(int) $category['id']] = $category;
        }
        foreach ($products as &$product) {
            $current = $roots[(int) $product['category_id']] ?? null;
            while ($current && !empty($current['parent_id']) && isset($roots[(int) $current['parent_id']])) {
                $current = $roots[(int) $current['parent_id']];
            }
            $product['filter'] = $current['slug'] ?? $product['category_slug'];
        }
        unset($product);

        layout('store', 'store/home', [
            'title' => setting('shop_name', 'Solange Box'),
            'hero' => true,
            'categories' => Category::roots(),
            'products' => $products,
            'posts' => array_slice($this->blogPosts(), 0, 2),
        ]);
    }

    public function shop(): void
    {
        $sort = (string) ($_GET['sort'] ?? 'newest');
        $inStock = !empty($_GET['in_stock']);
        $onSale = !empty($_GET['sale']);
        $products = Product::paginate([
            'status' => 'active',
            'sort' => $sort,
            'in_stock' => $inStock,
            'on_sale' => $onSale,
        ]);

        layout('store', 'store/shop', [
            'title' => $onSale ? 'Sale' : 'Shop',
            'products' => $products,
            'sort' => $sort,
            'in_stock' => $inStock,
            'on_sale' => $onSale,
        ]);
    }

    public function collections(): void
    {
        layout('store', 'store/collections', [
            'title' => 'Collections',
            'categories' => Category::roots(),
        ]);
    }

    public function blog(): void
    {
        layout('store', 'store/blog', [
            'title' => 'Blog',
            'posts' => $this->blogPosts(),
        ]);
    }

    public function blogPost(string $slug): void
    {
        $post = null;
        foreach ($this->blogPosts() as $item) {
            if ($item['slug'] === $slug) {
                $post = $item;
                break;
            }
        }
        if (!$post) {
            http_response_code(404);
            layout('store', 'store/404', ['title' => 'Post not found']);
            return;
        }

        layout('store', 'store/blog-post', [
            'title' => $post['title'],
            'post' => $post,
        ]);
    }

    public function newsletter(): void
    {
        Csrf::verify();
        $email = trim((string) ($_POST['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = trim((string) ($_POST['return'] ?? ''), '/');
            flash('error', 'Please enter a valid email address.');
            redirect($return === 'newsletter' ? '' : $return);
        }

        $return = trim((string) ($_POST['return'] ?? ''), '/');
        if ($return === 'newsletter' || str_starts_with($return, 'admin')) {
            $return = '';
        }

        flash('success', 'You are on the list. Look out for 10% off your first order.');
        redirect($return);
    }

    public function delivery(): void
    {
        layout('store', 'store/delivery', [
            'title' => 'Delivery & returns',
        ]);
    }

    public function about(): void
    {
        layout('store', 'store/about', [
            'title' => 'About Us',
        ]);
    }

    /** @return list<array<string, mixed>> */
    private function blogPosts(): array
    {
        return [
            [
                'slug' => 'quiet-pieces-for-everyday',
                'title' => 'Quiet pieces for everyday wear',
                'excerpt' => 'How we choose clothes, jewelry, and accessories that sit well together without trying too hard.',
                'date' => '12 Oct 2026',
                'image' => asset('images/hero-rack.png'),
                'body' => [
                    'Solange Box is built around pieces you can actually live in: clothes that hold their shape, jewelry with a quiet shine, and extras that do not shout.',
                    'Each week we look for a smaller edit — not more product, just better ones. If it does not sit well with what is already in the box, it does not come in.',
                    'Start with one considered piece and let the rest of the shop fill in around it.',
                ],
            ],
            [
                'slug' => 'new-arrivals-in-the-box',
                'title' => 'New arrivals in the box',
                'excerpt' => 'A smaller edit of clothes and jewelry, just in. Shop the newest pieces before they leave the box.',
                'date' => '4 Oct 2026',
                'image' => asset('images/hero-rack.png'),
                'body' => [
                    'New arrivals land in small groups so the shop stays easy to read. This drop leans into warm metal, washed linen, and compact electronics you will actually carry.',
                    'If you are building a first order, start with a hoop, a blouse, or a bag — then add from there.',
                ],
            ],
        ];
    }

    public function contact(): void
    {
        layout('store', 'store/contact', [
            'title' => 'Contact Us',
        ]);
    }

    public function sendContact(): void
    {
        Csrf::verify();
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $topic = trim((string) ($_POST['topic'] ?? 'general'));
        $message = trim((string) ($_POST['message'] ?? ''));

        remember_old([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'topic' => $topic,
            'message' => $message,
        ]);

        if ($name === '' || $message === '') {
            flash('error', 'Please add your name and a message.');
            redirect('contact');
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            redirect('contact');
        }

        clear_old();
        flash('success', 'Thank you, ' . $name . '. We have your message and will reply shortly.');
        redirect('contact');
    }

    public function search(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $products = $q !== ''
            ? Product::paginate(['status' => 'active', 'q' => $q, 'sort' => $_GET['sort'] ?? 'newest'])
            : [];

        layout('store', 'store/search', [
            'title' => $q !== '' ? 'Search: ' . $q : 'Search',
            'q' => $q,
            'products' => $products,
            'sort' => $_GET['sort'] ?? 'newest',
        ]);
    }

    public function category(string $slug): void
    {
        $category = Category::findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            layout('store', 'store/404', ['title' => 'Category not found']);
            return;
        }

        $sort = (string) ($_GET['sort'] ?? 'newest');
        $inStock = !empty($_GET['in_stock']);
        $products = Product::paginate([
            'status' => 'active',
            'category_ids' => Category::descendantIds((int) $category['id']),
            'sort' => $sort,
            'in_stock' => $inStock,
        ]);

        layout('store', 'store/category', [
            'title' => $category['name'],
            'category' => $category,
            'products' => $products,
            'sort' => $sort,
            'in_stock' => $inStock,
        ]);
    }

    public function product(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product || $product['status'] !== 'active') {
            http_response_code(404);
            layout('store', 'store/404', ['title' => 'Product not found']);
            return;
        }

        layout('store', 'store/product', [
            'title' => $product['name'],
            'product' => $product,
            'images' => Product::images((int) $product['id']),
            'variants' => Product::variants((int) $product['id']),
            'specs' => Product::specs((int) $product['id']),
        ]);
    }
}
