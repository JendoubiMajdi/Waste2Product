# 🌍 Waste2Product

[![Laravel CI/CD](https://github.com/JendoubiMajdi/Waste2Product/actions/workflows/laravel.yml/badge.svg)](https://github.com/JendoubiMajdi/Waste2Product/actions/workflows/laravel.yml)
[![Code Quality](https://github.com/JendoubiMajdi/Waste2Product/actions/workflows/code-quality.yml/badge.svg)](https://github.com/JendoubiMajdi/Waste2Product/actions/workflows/code-quality.yml)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## 📋 About Waste2Product

Waste2Product is a comprehensive waste management and recycling platform that connects users, collectors, and transporters in an eco-friendly ecosystem. The platform enables:

- ♻️ **Waste Management** - Track and manage waste deposits at collection points
- 🛒 **Product Marketplace** - Order recycled and reusable products
- 🚚 **Delivery System** - Complete order tracking and delivery management
- 🏆 **Challenges & Gamification** - Environmental challenges with AI-powered image verification
- 🎯 **Collection Points** - Locate nearest collection points with interactive maps
- 💬 **Community Forum** - Share ideas and best practices
- 📊 **Analytics Dashboard** - Comprehensive reporting for admins and collectors

## ✨ Key Features

### For Customers
- Browse and order recycled/reusable products
- Track orders in real-time with delivery timeline
- Deposit waste at collection points
- Participate in environmental challenges
- Earn points and badges for eco-friendly actions

### For Transporters
- Accept and manage product deliveries
- Update delivery status and estimated times
- View assigned orders and routes

### For Collectors
- Manage collection points
- Track waste deposits and processing
- View statistics and analytics

### For Admins
- Comprehensive dashboard with all metrics
- User management with role-based access
- Product and order management
- Challenge creation and submission approval
- Reports and analytics

## 🛠️ Tech Stack

- **Framework:** Laravel 12.x
- **PHP:** 8.2+
- **Database:** MySQL 8.0
- **Frontend:** Bootstrap 5, Blade Templates
- **AI Integration:** Google Gemini Vision API
- **Authentication:** Laravel Fortify
- **Maps:** Interactive collection point maps
- **Icons:** Bootstrap Icons, Iconify

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM (for asset compilation)
- Git

### Setup Steps

1. **Clone the repository**
```bash
git clone https://github.com/JendoubiMajdi/Waste2Product.git
cd Waste2Product
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=waste2product
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Configure Google Gemini API**
Add your Gemini API key to `.env`:
```env
GEMINI_API_KEY=your_gemini_api_key_here
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Seed database (optional)**
```bash
php artisan db:seed
```

8. **Build assets**
```bash
npm run build
```

9. **Start development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 👥 User Roles

- **Admin** - Full system access and management
- **Customer** - Order products, deposit waste, participate in challenges
- **Collector** - Manage collection points and waste processing
- **Transporter** - Handle product deliveries

## 🚀 Deployment

The project includes GitHub Actions workflows for:
- Automated testing on push/PR
- Code quality checks with Laravel Pint
- MySQL database integration tests

## 📱 API Integration

### Google Gemini Vision API
Used for AI-powered challenge verification:
- Image classification for waste types
- Challenge submission validation
- Confidence scoring

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Passion template migration notes

This repository was created as a fresh Laravel application to host the Passion template you provided in the parent workspace. The following actions were taken:

- Copied the original `assets/` folder into `public/assets` so static files are served by Laravel.
- Converted several existing views into Blade templates and placed them into `resources/views` (including `auth` views and `layouts/app.blade.php`).
- Added simple routes for `/`, `/login`, `/register`, and `/forgot-password` in `routes/web.php`.

Quick start (Windows PowerShell):

```powershell
cd laravel-app
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

Open http://127.0.0.1:8000 in your browser.

Notes:
- Authentication logic is not implemented (you previously asked not to install Breeze). The auth routes currently return the static views and POST handlers are placeholders in `routes/web.php`.
- If you'd like real Laravel authentication (controllers, database-backed users and password reset emails), I can install Laravel Breeze and wire it up.
