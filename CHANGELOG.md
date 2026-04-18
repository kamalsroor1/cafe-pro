# ☕ Cafe-Pro

> نظام نقطة بيع (POS) للمقاهي • مبني بـ Laravel 13 + NativePHP + Livewire 3

![Laravel](https://img.shields.io/badge/Laravel-13-red?style=flat-square)
![NativePHP](https://img.shields.io/badge/NativePHP-Desktop-blue?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Status](https://img.shields.io/badge/Status-Alpha_v1.0-orange?style=flat-square)

---

## 📖 نبذة عن المشروع

Cafe-Pro هو تطبيق ديسكتوب متكامل لإدارة المقاهي الصغيرة والمتوسطة. يعمل بدون إنترنت بالكامل باستخدام SQLite، ويدعم اللغة العربية بشكل أصلي. صُمِّم ليكون سريعاً وخفيفاً وسهل التركيب عند العميل.

---

## ⚙️ المتطلبات

| الأداة | الإصدار المطلوب |
| :--- | :--- |
| PHP | 8.4+ |
| Node.js | 20+ |
| Composer | 2.x |
| OS | Windows / Linux |

---

## 🚀 خطوات التشغيل

**1. استنساخ المشروع وتثبيت التبعيات:**

```bash
git clone https://github.com/your-username/cafe-pro.git
cd cafe-pro
composer install
npm install
```

**2. إعداد ملف البيئة وقاعدة البيانات:**

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed  # لإضافة بيانات المنيو التجريبية
```

**3. تشغيل الـ Assets (في تيرمينال منفصل):**

```bash
npm run dev
```

**4. تشغيل التطبيق:**

```bash
php artisan native:serve
```

---

## 📦 بناء النسخة النهائية

لإنشاء ملف تنفيذي جاهز للتركيب عند العميل (`.exe` / `.AppImage`):

```bash
php artisan native:build
```

---

## 📂 هيكلة المشروع

```
cafe-pro/
 ├── app/
 │   ├── Livewire/                          # منطق شاشة POS
 │   └── Providers/
 │       └── NativeAppServiceProvider.php   # إعدادات نافذة الديسكتوب
 ├── resources/views/livewire/              # ملفات Blade للواجهة
 ├── database/
 │   └── database.sqlite                    # مستبعدة من Git
 ├── .env.example
 └── README.md
```

---

## 🗺️ خارطة الطريق (Roadmap)

### الربع الحالي (Q2 2026)
- [ ] **ربط الطابعات:** دعم بروتوكول ESC/POS للطباعة الحرارية
- [ ] **إدارة المخزن:** تنبيه عند نقص المواد الخام
- [ ] **نظام الورديات:** فتح وإغلاق الكاشير لكل موظف

### الربع القادم (Q3 2026)
- [ ] **التقارير المتقدمة:** رسوم بيانية للمبيعات الأسبوعية والشهرية
- [ ] **دعم الـ QR Code:** للدفع الإلكتروني السريع
- [ ] **تعدد اللغات:** واجهة كاملة باللغة الإنجليزية

---

## 🤝 المساهمة

- اتبع نمط **Conventional Commits** مثل: `feat: add printing`, `fix: total bug`
- شغّل الاختبارات قبل أي Pull Request: `php artisan test`
- يُمنع رفع `.env` أو `database.sqlite` للمستودع

---

## 🛡️ الأمان

للإبلاغ عن ثغرة أمنية، يرجى مراجعة ملف [SECURITY.md](SECURITY.md) بدلاً من فتح Issue عام.

---

## 📝 سجل التغييرات

### [1.0.0] - 2026-04-18
- إعداد هيكل المشروع باستخدام Laravel 13 و NativePHP
- إنشاء واجهة POS تدعم اللمس باستخدام Livewire
- إعداد نظام SQLite للعمل بدون إنترنت
- تخصيص `NativeAppServiceProvider` لفتح النافذة بوضع Maximized

---

## 📄 الرخصة

مرخّص بموجب [MIT License](LICENSE) — © 2026 Cafe-Pro Team
