<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * يتم تنفيذه بمجرد إقلاع التطبيق كبرنامج Desktop.
     */
    public function boot(): void
    {
        // 1. تشغيل الميجريشن تلقائياً (مهم جداً عند العميل)
        // هذا السطر يضمن أن الجداول ستنشأ في ملف SQLite فور فتح البرنامج
        Artisan::call('migrate', ['--force' => true]);

        // 2. إدخال بيانات تجريبية إذا كانت الداتابيز فارغة (اختياري)
        $this->seedInitialData();

        // 3. فتح نافذة البرنامج بإعدادات تناسب شاشات التاتش
        Window::open()
            ->title('Cafe Pro - نظام إدارة الكافيه')
            ->width(1200)
            ->height(800)
            ->maximize() // يفتح الشاشة كاملة فوراً
            ->rememberState() // يحفظ حجم الشاشة لو المستخدم غيره
            ->showDevTools(false); // إخفاء أدوات المطورين في النسخة النهائية
    }

    /**
     * دالة للتأكد من وجود أصناف في المنيو عند أول تشغيل
     */
    protected function seedInitialData(): void
    {
        if (Product::count() === 0) {
            Product::create(['name' => 'قهوة تركي', 'price' => 35.00]);
            Product::create(['name' => 'كابتشينو', 'price' => 55.00]);
            Product::create(['name' => 'شاي أحمر', 'price' => 15.00]);
        }
    }

    /**
     * إعدادات php.ini الخاصة بالبرنامج (مثلاً لزيادة حجم الرفع)
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'display_errors' => '1',
            'error_reporting' => E_ALL,
        ];
    }
}