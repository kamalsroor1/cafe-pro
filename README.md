# ☕ Cafe-Pro POS System

نظام إدارة كافيهات متكامل مبني باستخدام **Laravel** و **NativePHP**، مصمم كبرنامج سطح مكتب (Desktop App) بواجهة تدعم اللمس.

## 🚀 التقنيات المستخدمة
- **Framework:** Laravel 13
- **Desktop Engine:** NativePHP (Electron)
- **Frontend:** Livewire 3 & Tailwind CSS
- **Database:** SQLite (للعمل Offline)

## 🛠 التشغيل السريع (Hybrid Setup)
بما أن البرنامج يحتاج الوصول لواجهة الرسوميات، نستخدم أسلوب **Hybrid**:

1. **تثبيت المتطلبات محلياً (على Fedora):**
   `sudo dnf install php-cli php-common composer nodejs npm php-sqlite3 php-intl php-pecl-zip -y`

2. **تجهيز المشروع:**
   ```bash
   composer install --ignore-platform-reqs
   npm install
   ./vendor/bin/sail up -d