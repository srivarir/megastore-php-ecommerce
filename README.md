# MEGASTORE — PHP / MySQL / Bootstrap E-Commerce

A full conversion of the original **Django `premium_shop`** project into a plain
**PHP + MySQL + HTML + CSS + Bootstrap + JS** stack. No framework required — it runs
on any standard PHP + MySQL setup (XAMPP, WAMP, MAMP, or LAMP).

---

## 1. What's inside

```
megastore_php/
├── index.php              ← front controller (routes ?page=... like Django urls.py)
├── config/
│   └── db.php             ← database credentials + PDO connection
├── includes/
│   ├── bootstrap.php      ← session, helpers, CSRF, auth, cart helpers
│   ├── header.php         ← shared top nav / <head> (the old base.html)
│   └── footer.php         ← shared footer + scripts
├── pages/                 ← one file per screen
│   ├── home.php           category.php   product.php
│   ├── cart.php           add_to_cart.php  remove_from_cart.php  update_cart.php
│   ├── checkout.php       process_checkout.php
│   ├── payment_cod.php    payment_upi.php  payment_card_details.php  payment_success.php
│   ├── profile.php        edit_profile.php  edit_address.php  order_detail.php
│   ├── login.php          register.php  logout.php  review_add.php
│   ├── about.php          contact.php
│   └── _product_card.php  _payment_layout.php   (shared partials)
├── assets/
│   ├── css/style.css      js/main.js
│   └── images/            ← carousel & banner images
├── uploads/
│   ├── categories/        ← 15 category images (from Django /media)
│   └── products/          ← 39 product images (from Django /media)
└── sql/
    └── megastore.sql      ← schema + all seed data
```

---

## 2. Setup (XAMPP example)

1. **Copy the project** into your web root:
   - XAMPP → `C:\xampp\htdocs\megastore_php`
   - LAMP  → `/var/www/html/megastore_php`

2. **Start Apache + MySQL** (XAMPP control panel).

3. **Create the database & import data.** Open phpMyAdmin (`http://localhost/phpmyadmin`):
   - Create a database named **`megastore`** (collation `utf8mb4_general_ci`).
   - Select it → **Import** tab → choose `sql/megastore.sql` → **Go**.

   Or from a terminal:
   ```bash
   mysql -u root -p -e "CREATE DATABASE megastore CHARACTER SET utf8mb4;"
   mysql -u root -p megastore < sql/megastore.sql
   ```

4. **Check credentials** in `config/db.php` (XAMPP defaults already set:
   host `127.0.0.1`, user `root`, empty password). Edit if yours differ.

5. **Open the app:**
   `http://localhost/megastore_php/index.php`

---

## 3. Logging in

Django password hashes (PBKDF2) can't be verified by PHP, so all imported users
were re-hashed. **Every seeded account uses the password `password123`.**

| Username | Role          |
|----------|---------------|
| Shikha   | Admin (super) |
| Sana     | Customer      |
| Priya    | Customer      |
| Raghu    | Customer      |

You can also register a brand-new account from the **Register** page.

---

## 4. Feature mapping (Django → PHP)

| Django                                   | PHP equivalent                          |
|------------------------------------------|-----------------------------------------|
| `urls.py`                                | `index.php` route table                 |
| `views.py` functions                     | files in `pages/`                        |
| `models.py` + ORM                        | MySQL tables + PDO prepared statements   |
| `base.html` / templates                  | `includes/header.php` + `footer.php`     |
| `@login_required`                        | `require_login()`                        |
| `{% csrf_token %}`                        | `csrf_field()` / `check_csrf()`          |
| Django auth (`User`)                     | `users` table + `password_hash/verify`   |
| `/media/` uploads                        | `uploads/`                               |
| `{% static %}`                            | `assets/`                                |

Behaviour preserved: hero carousel (Swiper), category grid, product search,
product detail with review submission + rating-summary bars, quantity-aware cart,
checkout with delivery/payment options, COD/UPI/Card payment screens, order
history with status filters, order detail, profile and address editing.

---

## 5. Security notes

- All database access uses **PDO prepared statements** (no SQL injection).
- All output is escaped with `htmlspecialchars` via the `e()` helper (no XSS).
- **CSRF tokens** protect every POST form.
- Passwords are stored as **bcrypt** hashes.
- The card-payment screen is a **UI demo only** — it does not process or store
  real card data. Integrate a real gateway (Stripe/Razorpay) before production.

---

## 6. Tech stack

- PHP 7.4+ / 8.x (PDO, pdo_mysql)
- MySQL 5.7+ / MariaDB
- Tailwind CSS (CDN) — preserves the original dark theme
- Bootstrap 5 (CDN) — added per the requested stack
- Swiper 11 (CDN) — homepage carousel
- Remix Icon (CDN)
```
```
