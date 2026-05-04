<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSettings extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'site_name',
        'site_description',
        'site_logo',
        'site_favicon',
        'hero_title',
        'hero_subtitle',
        'hero_button_text',
        'hero_button_link',
        'hero_background_color',
        'hero_background_image',
        'show_top_banner',
        'banner_text',
        'banner_icon',
        'banner_bg_color',
        'primary_color',
        'secondary_color',
        'accent_color',
        'contact_phone',
        'contact_email',
        'contact_address',
        'whatsapp_number',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'footer_about',
        'footer_copyright',
        'features',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'show_top_banner' => 'boolean',
        'features' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public static function getSettings($userId, $storeId = null)
    {
        $where = ['user_id' => $userId];
        if ($storeId) {
            $where['store_id'] = $storeId;
        }
        
        return static::firstOrCreate(
            $where,
            [
                'site_name' => config('app.name'),
                'hero_title' => 'مرحباً بكم في متجرنا',
                'hero_subtitle' => 'اكتشف منتجات فريدة تم اختيارها بعناية من أجلك',
                'banner_text' => 'المتجر الإلكتروني رقم 1 في المغرب! مباشرة من عندنا إلى عندكم',
                'contact_phone' => '(212) 661-360879',
                'footer_about' => 'متجركم الموثوق للمنتجات عالية الجودة.',
                'footer_copyright' => '© 2026 ' . config('app.name') . '. جميع الحقوق محفوظة.',
                'features' => [
                    ['icon' => 'local_shipping', 'title' => 'توصيل مجاني', 'color' => '#10b981'],
                    ['icon' => 'support_agent', 'title' => 'خدمة العملاء', 'color' => '#3b82f6'],
                    ['icon' => 'public', 'title' => 'متوفر في المغرب', 'color' => '#a855f7'],
                    ['icon' => 'payment', 'title' => 'الدفع عند الاستلام', 'color' => '#f97316'],
                ],
            ]
        );
    }
}
