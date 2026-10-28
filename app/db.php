<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dir = ROOT_PATH . '/database';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $path = $dir . '/solange.sqlite';
    $needsInstall = !file_exists($path) || filesize($path) === 0;

    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');

    if ($needsInstall) {
        install_database($pdo);
    }

    return $pdo;
}

function install_database(PDO $pdo): void
{
    $schema = file_get_contents(ROOT_PATH . '/schema.sql');
    if ($schema === false) {
        throw new RuntimeException('Unable to read schema.sql');
    }
    $pdo->exec($schema);
    seed_database($pdo);
}

function seed_database(PDO $pdo): void
{
    $pdo->beginTransaction();

    $settings = [
        'shop_name' => 'Solange Box',
        'tagline' => 'class with elegance',
        'phone' => '+265 999 000 000',
        'email' => 'hello@solangebox.mw',
        'address' => 'Lilongwe, Malawi',
        'currency' => 'MWK',
        'currency_symbol' => 'MK',
        'about' => 'Solange Box is a curated shop for clothing, jewelry, electronics, and hair — chosen for people who want everyday pieces that still feel considered.',
        'payment_mode' => 'sandbox',
    ];

    $stmt = $pdo->prepare('INSERT INTO settings (key, value) VALUES (:key, :value)');
    foreach ($settings as $key => $value) {
        $stmt->execute(['key' => $key, 'value' => $value]);
    }

    $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:u, :p)')
        ->execute([
            'u' => 'admin',
            'p' => password_hash('solangebox', PASSWORD_DEFAULT),
        ]);

    $catStmt = $pdo->prepare(
        'INSERT INTO categories (name, slug, parent_id, sort_order) VALUES (:name, :slug, :parent_id, :sort_order)'
    );

    $categories = [
        ['name' => 'Clothing', 'slug' => 'clothing', 'parent_id' => null, 'sort_order' => 1],
        ['name' => 'Jewelry', 'slug' => 'jewelry', 'parent_id' => null, 'sort_order' => 2],
        ['name' => 'Electronics', 'slug' => 'electronics', 'parent_id' => null, 'sort_order' => 3],
        ['name' => 'Hair', 'slug' => 'hair', 'parent_id' => null, 'sort_order' => 4],
    ];
    $catIds = [];
    foreach ($categories as $category) {
        $catStmt->execute($category);
        $catIds[$category['slug']] = (int) $pdo->lastInsertId();
    }

    $catStmt->execute([
        'name' => 'Earrings',
        'slug' => 'earrings',
        'parent_id' => $catIds['jewelry'],
        'sort_order' => 1,
    ]);
    $catIds['earrings'] = (int) $pdo->lastInsertId();

    $productStmt = $pdo->prepare(
        'INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, status)
         VALUES (:category_id, :name, :slug, :description, :price, :sale_price, :stock, :status)'
    );
    $imageStmt = $pdo->prepare(
        'INSERT INTO product_images (product_id, path, sort_order) VALUES (:product_id, :path, :sort_order)'
    );
    $variantStmt = $pdo->prepare(
        'INSERT INTO product_variants (product_id, size, color, extra_price, stock)
         VALUES (:product_id, :size, :color, :extra_price, :stock)'
    );
    $specStmt = $pdo->prepare(
        'INSERT INTO product_specs (product_id, label, value, sort_order)
         VALUES (:product_id, :label, :value, :sort_order)'
    );

    $products = [
        [
            'category' => 'clothing',
            'name' => 'Silk Wrap Blouse',
            'slug' => 'silk-wrap-blouse',
            'description' => 'A fluid silk wrap blouse with a soft drape and covered buttons. Wear it tucked into trousers or loose over a skirt.',
            'price' => 45000,
            'sale_price' => 38000,
            'stock' => 12,
            'image' => 'silk-wrap-blouse.svg',
            'variants' => [
                ['size' => 'S', 'color' => 'Ivory', 'extra_price' => 0, 'stock' => 4],
                ['size' => 'M', 'color' => 'Ivory', 'extra_price' => 0, 'stock' => 5],
                ['size' => 'L', 'color' => 'Ivory', 'extra_price' => 0, 'stock' => 3],
            ],
            'specs' => [
                ['label' => 'Fabric', 'value' => 'Silk blend'],
                ['label' => 'Care', 'value' => 'Dry clean'],
            ],
        ],
        [
            'category' => 'clothing',
            'name' => 'Linen Midi Dress',
            'slug' => 'linen-midi-dress',
            'description' => 'A tailored midi dress in washed linen, with a square neckline and side pockets. Easy enough for the week, refined enough for dinner.',
            'price' => 55000,
            'sale_price' => null,
            'stock' => 8,
            'image' => 'linen-midi-dress.svg',
            'variants' => [
                ['size' => 'S', 'color' => 'Aubergine', 'extra_price' => 0, 'stock' => 2],
                ['size' => 'M', 'color' => 'Aubergine', 'extra_price' => 0, 'stock' => 4],
                ['size' => 'L', 'color' => 'Aubergine', 'extra_price' => 0, 'stock' => 2],
            ],
            'specs' => [
                ['label' => 'Fabric', 'value' => '100% linen'],
                ['label' => 'Length', 'value' => 'Midi'],
            ],
        ],
        [
            'category' => 'earrings',
            'name' => 'Gold Hoop Earrings',
            'slug' => 'gold-hoop-earrings',
            'description' => 'Lightweight bronze-gold hoops with a quiet shine. Everyday jewelry that still reads as considered.',
            'price' => 18500,
            'sale_price' => null,
            'stock' => 20,
            'image' => 'gold-hoop-earrings.svg',
            'variants' => [],
            'specs' => [
                ['label' => 'Finish', 'value' => 'Gold plated'],
                ['label' => 'Diameter', 'value' => '35 mm'],
            ],
        ],
        [
            'category' => 'jewelry',
            'name' => 'Statement Necklace',
            'slug' => 'statement-necklace',
            'description' => 'A sculptural collar necklace in warm bronze. Designed to sit cleanly against a simple neckline.',
            'price' => 22000,
            'sale_price' => 17500,
            'stock' => 6,
            'image' => 'statement-necklace.svg',
            'variants' => [],
            'specs' => [
                ['label' => 'Finish', 'value' => 'Bronze gold'],
                ['label' => 'Closure', 'value' => 'Lobster clasp'],
            ],
        ],
        [
            'category' => 'electronics',
            'name' => 'Wireless Earbuds',
            'slug' => 'wireless-earbuds',
            'description' => 'Compact wireless earbuds with a charging case, clear calls, and a matte finish that matches the rest of the box.',
            'price' => 65000,
            'sale_price' => null,
            'stock' => 15,
            'image' => 'wireless-earbuds.svg',
            'variants' => [
                ['size' => null, 'color' => 'Black', 'extra_price' => 0, 'stock' => 8],
                ['size' => null, 'color' => 'Ivory', 'extra_price' => 2000, 'stock' => 7],
            ],
            'specs' => [
                ['label' => 'Battery', 'value' => '6 hours + 24 hours case'],
                ['label' => 'Connectivity', 'value' => 'Bluetooth 5.3'],
                ['label' => 'Warranty', 'value' => '12 months'],
            ],
        ],
        [
            'category' => 'electronics',
            'name' => 'Power Bank 20000mAh',
            'slug' => 'power-bank-20000mah',
            'description' => 'A slim 20000mAh power bank with dual USB-C ports. Enough for a weekend away without the bulk.',
            'price' => 28000,
            'sale_price' => null,
            'stock' => 18,
            'image' => 'power-bank.svg',
            'variants' => [],
            'specs' => [
                ['label' => 'Capacity', 'value' => '20000mAh'],
                ['label' => 'Ports', 'value' => '2x USB-C, 1x USB-A'],
                ['label' => 'Warranty', 'value' => '12 months'],
            ],
        ],
        [
            'category' => 'hair',
            'name' => 'Human Hair Wig',
            'slug' => 'human-hair-wig',
            'description' => 'A natural-texture human hair wig with a lace front and adjustable cap. Styled at the salon or worn as-is.',
            'price' => 120000,
            'sale_price' => null,
            'stock' => 4,
            'image' => 'human-hair-wig.svg',
            'variants' => [
                ['size' => '14 inch', 'color' => 'Natural black', 'extra_price' => 0, 'stock' => 2],
                ['size' => '18 inch', 'color' => 'Natural black', 'extra_price' => 15000, 'stock' => 2],
            ],
            'specs' => [
                ['label' => 'Hair', 'value' => 'Human hair'],
                ['label' => 'Cap', 'value' => 'Lace front, adjustable'],
            ],
        ],
        [
            'category' => 'clothing',
            'name' => 'Leather Crossbody',
            'slug' => 'leather-crossbody',
            'description' => 'A compact leather crossbody with a structured silhouette and a discreet gold clasp. Holds a phone, cards, and keys.',
            'price' => 42000,
            'sale_price' => null,
            'stock' => 9,
            'image' => 'leather-crossbody.svg',
            'variants' => [
                ['size' => null, 'color' => 'Aubergine', 'extra_price' => 0, 'stock' => 5],
                ['size' => null, 'color' => 'Bronze', 'extra_price' => 0, 'stock' => 4],
            ],
            'specs' => [
                ['label' => 'Material', 'value' => 'Genuine leather'],
                ['label' => 'Strap', 'value' => 'Adjustable'],
            ],
        ],
    ];

    foreach ($products as $index => $product) {
        $productStmt->execute([
            'category_id' => $catIds[$product['category']],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'description' => $product['description'],
            'price' => $product['price'],
            'sale_price' => $product['sale_price'],
            'stock' => $product['stock'],
            'status' => 'active',
        ]);
        $productId = (int) $pdo->lastInsertId();

        $imageStmt->execute([
            'product_id' => $productId,
            'path' => 'uploads/products/' . $product['image'],
            'sort_order' => 0,
        ]);

        foreach ($product['variants'] as $variant) {
            $variantStmt->execute([
                'product_id' => $productId,
                'size' => $variant['size'],
                'color' => $variant['color'],
                'extra_price' => $variant['extra_price'],
                'stock' => $variant['stock'],
            ]);
        }

        foreach ($product['specs'] as $specIndex => $spec) {
            $specStmt->execute([
                'product_id' => $productId,
                'label' => $spec['label'],
                'value' => $spec['value'],
                'sort_order' => $specIndex,
            ]);
        }
    }

    $pdo->commit();
}
