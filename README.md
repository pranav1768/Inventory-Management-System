# OFV Inventory Management System
## Ordnance Factory Varangaon – Ministry of Defence

---

## 📁 Project Structure

```
ordnance_ims/
├── login.php           → Login page (Indian flag theme + OFV branding)
├── home.php            → Dashboard with inventory table
├── add_product.php     → Add new product form
├── edit_product.php    → Edit existing product
├── delete_product.php  → Delete product (soft delete)
├── history.php         → Inventory update history log
├── low_stock.php       → Low stock alert page (bonus feature)
├── logout.php          → Session destroy + redirect to login
├── database.sql        → Full MySQL setup script
└── includes/
    ├── db.php          → Database connection + helper functions
    ├── header.php      → Shared header, navbar, sidebar
    └── footer.php      → Shared footer
```

---

## ⚙️ Setup Instructions

### Step 1 – Database
1. Open **phpMyAdmin** or MySQL CLI
2. Run the SQL script:
   ```sql
   SOURCE /path/to/ordnance_ims/database.sql;
   ```
   Or import `database.sql` via phpMyAdmin → Import

### Step 2 – Configure DB Connection
Open `includes/db.php` and update:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
define('DB_NAME', 'ordnance_ims');
```

### Step 3 – Deploy
- Copy the `ordnance_ims/` folder to your web server's root:
  - **XAMPP:** `C:/xampp/htdocs/ordnance_ims/`
  - **WAMP:**  `C:/wamp/www/ordnance_ims/`
  - **Linux:** `/var/www/html/ordnance_ims/`

### Step 4 – Access
Open browser: `http://localhost/ordnance_ims/login.php`

---

## 🔑 Demo Login
| Field    | Value     |
|----------|-----------|
| Username | `admin`   |
| Password | `admin123`|

---

## ✨ Features

| Feature | Page |
|---------|------|
| Secure Login with demo credentials | `login.php` |
| Dashboard with stats (total products, qty, low stock, categories) | `home.php` |
| Inventory table (Name, Code, Quantity, Date, Category, Status) | `home.php` |
| Add Product | `add_product.php` |
| Edit Product | `edit_product.php` |
| Delete Product (with confirmation popup) | `home.php` |
| Update Inventory (with "Are you sure?" popup) | `home.php` |
| Hamburger sidebar (Home, Add, Update, Low Stock, History, Logout) | All pages |
| Inventory Update History with filters | `history.php` |
| Low Stock Alert page (bonus feature) | `low_stock.php` |
| Logout with "Are you sure?" popup → redirects to login | All pages |
| Indian flag color theme (Saffron, White, Green, Navy) | All pages |
| OFV branding with government emblem + Satyamev Jayate | All pages |
| Live date/time clock in header | All pages |

---

## 🎨 UI Theme
- **Saffron** `#FF9933` – Primary actions, headers, borders
- **Green**   `#138808` – Success badges, add actions
- **Navy**    `#000080` – Table headers, navigation
- **White**   `#FFFFFF` – Cards, backgrounds
- Indian flag stripe on top of every page
- Government of India emblem (SVG) on login & all pages

---

## 🛠️ Tech Stack
- **Backend:** PHP 7.4+
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Fonts:** Google Fonts (Rajdhani, Open Sans)
- **Server:** Apache (XAMPP / WAMP / LAMP)

---

*© 2025 Ordnance Factory Varangaon · Ministry of Defence, Government of India*
