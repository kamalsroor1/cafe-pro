<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Category;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        // 1. تشغيل الميجريشن والسييد بأمان
        // Artisan::call('migrate', ['--force' => true]);
        // Artisan::call('db:seed', ['--force' => true]);

        // 2. إدخال بيانات تجريبية
        $this->seedInitialData();

        // 3. التحكم في مسار البداية (Login vs Dashboard)
        // إذا كنت تستخدم Laravel Auth، يفضل توجيه المستخدم للـ Login أولاً
        Window::open()
            ->title('Cafe Pro - نظام إدارة الكافيه')
            ->width(1200)
            ->height(800)
            ->maximize()
            ->rememberState()
            ->route('login') // تأكد أن لديك Route باسم login
            ->showDevTools(false);
    }

    protected function seedInitialData(): void
    {
        // ملاحظة: الـ Logs أظهرت أن جدول المنتجات يتطلب category_id
        // لذا يجب التأكد من وجود قسم أولاً
        if (Schema::hasTable('products') && Product::count() === 0) {
            $category = Category::firstOrCreate(['name' => 'General'],['slug' => 'general']);
            
            Product::create([
                'category_id' => $category->id,
                'name' => 'قهوة تركي', 
                'price' => 35.00,
                'slug' => 'turkish-coffee'
            ]);
        }
    }

    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'display_errors' => '1',
            'error_reporting' => E_ALL,
        ];
    }
}