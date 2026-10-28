<section class="page-banner">
    <img src="<?= e(asset('images/hero-rack.png')) ?>" alt="">
    <div class="page-banner-shade"></div>
    <div class="page-banner-copy">
        <p class="eyebrow">Get in touch</p>
        <h1>Contact Us</h1>
        <p>Orders, sizing, or a piece you would like us to find — write and we will come back to you.</p>
    </div>
</section>

<section class="section contact-section">
    <div class="contact-layout">
        <div class="contact-form-card">
            <h2>Send a message</h2>
            <p class="muted">We usually reply within one working day.</p>
            <form class="stack-form" action="<?= e(url('contact')) ?>" method="post">
                <?= Csrf::field() ?>
                <label>
                    Name
                    <input type="text" name="name" value="<?= e((string) old('name')) ?>" required>
                </label>
                <div class="form-grid">
                    <label>
                        Email
                        <input type="email" name="email" value="<?= e((string) old('email')) ?>" required>
                    </label>
                    <label>
                        Phone
                        <input type="tel" name="phone" value="<?= e((string) old('phone')) ?>">
                    </label>
                </div>
                <label>
                    Topic
                    <select name="topic">
                        <?php
                        $topic = (string) old('topic', 'general');
                        $topics = [
                            'general' => 'General question',
                            'order' => 'An order',
                            'product' => 'A product',
                            'delivery' => 'Delivery & returns',
                        ];
                        foreach ($topics as $value => $label):
                        ?>
                            <option value="<?= e($value) ?>" <?= $topic === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Message
                    <textarea name="message" rows="6" required><?= e((string) old('message')) ?></textarea>
                </label>
                <button class="btn btn-gold" type="submit">Send message</button>
            </form>
        </div>
        <aside class="contact-aside">
            <article class="contact-card">
                <h3>Visit</h3>
                <p><?= e(setting('address')) ?></p>
                <p class="muted">Mon–Sat · 9:00–17:30<br>Sunday by appointment</p>
            </article>
            <article class="contact-card">
                <h3>Call or write</h3>
                <ul class="contact-list">
                    <li><a href="tel:<?= e(preg_replace('/\s+/', '', (string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li>
                    <li><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li>
                </ul>
            </article>
            <article class="contact-card">
                <h3>Help</h3>
                <p><a class="promo-link" href="<?= e(url('delivery')) ?>">Delivery &amp; returns</a></p>
                <p><a class="promo-link" href="<?= e(url('shop')) ?>">Browse the shop</a></p>
            </article>
        </aside>
    </div>
</section>

<section class="mid-banner">
    <img src="<?= e(asset('images/hero-rack.png')) ?>" alt="">
    <div class="mid-banner-shade"></div>
    <div class="mid-banner-copy">
        <p class="eyebrow">The box</p>
        <h2>Come in, or start with the shop.</h2>
        <p>Clothing, jewelry, electronics, and hair — chosen to sit together.</p>
        <a class="pill-btn" href="<?= e(url('shop')) ?>">
            Shop now
            <span class="arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M7 17L17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </a>
    </div>
</section>
