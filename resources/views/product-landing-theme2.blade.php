<!DOCTYPE html>
<html lang="ar" class="scroll-smooth" x-data="{ currentLang: 'ar' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;600;700;800;900&family=Bebas+Neue&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if($store->facebook_pixel_enabled && $store->facebook_pixel_id)
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $store->facebook_pixel_id }}');
        fbq('track', 'PageView');
        fbq('track', 'ViewContent', {
            content_name: '{{ addslashes($product->name) }}',
            content_ids: ['{{ $product->id }}'],
            content_type: 'product',
            value: {{ $product->price }},
            currency: 'DHS'
        });
    </script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f0; }
        .rtl { direction: rtl; font-family: 'Cairo', sans-serif; }
        .font-display { font-family: 'Bebas Neue', 'Cairo', sans-serif; letter-spacing: 0.02em; }
        .stripe-bg {
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255,255,255,0.08) 0,
                rgba(255,255,255,0.08) 12px,
                transparent 12px,
                transparent 24px
            );
        }
        @keyframes pulse-scale { 0%,100% { transform: scale(1); } 50% { transform: scale(1.04); } }
        .animate-pulse-scale { animation: pulse-scale 1.6s ease-in-out infinite; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 20s linear infinite; }
        
        /* Quill editor text alignment classes */
        .ql-align-center,
        .prose .ql-align-center {
            text-align: center !important;
        }
        .ql-align-right,
        .prose .ql-align-right {
            text-align: right !important;
        }
        .ql-align-left,
        .prose .ql-align-left {
            text-align: left !important;
        }
        .ql-align-justify,
        .prose .ql-align-justify {
            text-align: justify !important;
        }
        
        /* Also support inline styles */
        [style*="text-align: center"],
        [style*="text-align:center"] {
            text-align: center !important;
        }
        [style*="text-align: right"],
        [style*="text-align:right"] {
            text-align: right !important;
        }
        [style*="text-align: left"],
        [style*="text-align:left"] {
            text-align: left !important;
        }
    </style>
</head>
@php
    $td = $product->theme_data ?? [];
    $shortDescription = $td['short_description'] ?? '';
    $promoBadge = $td['promo_badge'] ?? '-50% OFF TODAY';
    $promoBadgeColor = $td['promo_badge_color'] ?? 'red';
    $ctaText = $td['cta_text'] ?? 'ORDER NOW';
    $ctaColor = $td['cta_color'] ?? 'orange';
    $statsCustomers = $td['stats_customers'] ?? '325';
    $statsRating = $td['stats_rating'] ?? '4.8';
    $statsReviews = $td['stats_reviews'] ?? '127';
    $features = $td['features'] ?? [];
    $badges = $td['badges'] ?? [];
    $images = $product->all_images ?? [];

    $heroBgMap = [
        'red' => 'from-red-500 via-red-600 to-red-700',
        'orange' => 'from-orange-500 via-orange-600 to-amber-600',
        'green' => 'from-emerald-500 via-green-600 to-green-700',
        'blue' => 'from-blue-500 via-blue-600 to-indigo-700',
        'purple' => 'from-purple-500 via-purple-600 to-fuchsia-700',
    ];
    $ctaGradientMap = [
        'orange' => 'from-orange-500 to-red-600',
        'green' => 'from-green-500 to-emerald-600',
        'red' => 'from-red-500 to-rose-600',
        'blue' => 'from-blue-500 to-indigo-600',
    ];
    $heroBg = $heroBgMap[$promoBadgeColor] ?? $heroBgMap['orange'];
    $ctaBg = $ctaGradientMap[$ctaColor] ?? $ctaGradientMap['orange'];

    $featureIconMap = [
        'steam' => '🔥', 'clean' => '✨', 'fast' => '⚡', 'eco' => '🌿',
        'power' => '💪', 'safe' => '🛡️', 'timer' => '⏱️', 'warranty' => '📋',
    ];

    $badgeLabels = [
        'fr' => [
            'free_shipping' => ['🚚', 'Livraison gratuite'],
            'money_back' => ['💰', 'Satisfait ou remboursé'],
            'secure_payment' => ['🔒', 'Paiement sécurisé'],
            'warranty' => ['✅', 'Garantie 1 an'],
            'cod' => ['💵', 'Paiement à la livraison'],
            'fast_delivery' => ['⚡', 'Livraison 24-48h'],
        ],
        'en' => [
            'free_shipping' => ['🚚', 'Free Shipping'],
            'money_back' => ['💰', 'Money Back Guarantee'],
            'secure_payment' => ['🔒', 'Secure Payment'],
            'warranty' => ['✅', '1 Year Warranty'],
            'cod' => ['💵', 'Cash On Delivery'],
            'fast_delivery' => ['⚡', 'Fast Delivery 24-48h'],
        ],
        'ar' => [
            'free_shipping' => ['🚚', 'شحن مجاني'],
            'money_back' => ['💰', 'ضمان استرداد الأموال'],
            'secure_payment' => ['🔒', 'دفع آمن'],
            'warranty' => ['✅', 'ضمان سنة'],
            'cod' => ['💵', 'الدفع عند الاستلام'],
            'fast_delivery' => ['⚡', 'توصيل 24-48 ساعة'],
        ],
    ];

    $i18n = [
        'fr' => [
            'order_now' => 'COMMANDEZ MAINTENANT', 'name' => 'Nom complet', 'phone' => 'Téléphone',
            'city' => 'Ville', 'address' => 'Adresse', 'note' => 'Note',
            'send_order' => 'Envoyer la commande', 'cod' => 'PAIEMENT À LA LIVRAISON',
            'limited_stock' => 'STOCK LIMITÉ', 'only_today' => 'Offre valable aujourd\'hui seulement !',
            'customers' => 'Clients', 'rating' => 'Note', 'reviews' => 'Avis',
            'why_choose' => 'Pourquoi choisir ce produit ?',
            'how_to_order' => 'COMMENT COMMANDER',
            'step1_t' => 'Remplissez le formulaire', 'step1_d' => 'Vos informations en toute sécurité',
            'step2_t' => 'Nous vous appelons', 'step2_d' => 'Pour confirmer votre commande',
            'step3_t' => 'Livraison à domicile', 'step3_d' => 'Payez à la réception',
            'testimonials' => 'Témoignages de nos clients',
            'share' => 'Partagez :', 'guarantee' => 'Garantie 100% Satisfait ou Remboursé',
        ],
        'en' => [
            'order_now' => 'ORDER NOW', 'name' => 'Full Name', 'phone' => 'Phone',
            'city' => 'City', 'address' => 'Address', 'note' => 'Note',
            'send_order' => 'Send Order', 'cod' => 'CASH ON DELIVERY',
            'limited_stock' => 'LIMITED STOCK', 'only_today' => 'Offer only valid today!',
            'customers' => 'Customers', 'rating' => 'Rating', 'reviews' => 'Reviews',
            'why_choose' => 'Why choose this product?',
            'how_to_order' => 'HOW TO ORDER',
            'step1_t' => 'Fill the form', 'step1_d' => 'Your info kept 100% safe',
            'step2_t' => 'We call you', 'step2_d' => 'To confirm your order',
            'step3_t' => 'Home delivery', 'step3_d' => 'Pay on arrival',
            'testimonials' => 'What our customers say',
            'share' => 'Share:', 'guarantee' => '100% Satisfied or Refunded',
        ],
        'ar' => [
            'order_now' => 'اطلب الآن', 'name' => 'الاسم الكامل', 'phone' => 'رقم الهاتف',
            'city' => 'المدينة', 'address' => 'العنوان', 'note' => 'ملاحظة',
            'send_order' => 'أرسل طلبك', 'cod' => 'الدفع عند الاستلام',
            'limited_stock' => 'الكمية محدودة', 'only_today' => 'العرض ساري اليوم فقط !',
            'customers' => 'عميل', 'rating' => 'التقييم', 'reviews' => 'مراجعة',
            'why_choose' => 'لماذا تختار هذا المنتج ؟',
            'how_to_order' => 'كيف تطلب المنتج',
            'step1_t' => 'املأ النموذج', 'step1_d' => 'بياناتك آمنة 100%',
            'step2_t' => 'نتصل بك', 'step2_d' => 'لتأكيد طلبك',
            'step3_t' => 'التوصيل إلى البيت', 'step3_d' => 'ادفع عند الاستلام',
            'testimonials' => 'آراء عملائنا',
            'share' => 'شارك :', 'guarantee' => '100% راض أو تسترد أموالك',
        ],
    ];

    $testimonials = [
        ['name' => 'Karim', 'city' => 'Casablanca', 'fr' => "Produit de qualité, livraison rapide. Je recommande vivement !", 'en' => 'Great product, fast delivery. Highly recommend!', 'ar' => 'منتج ممتاز والتوصيل سريع جدا. أنصح به بشدة !'],
        ['name' => 'Fatima', 'city' => 'Rabat', 'fr' => "Exactement comme décrit. Service client au top.", 'en' => 'Exactly as described. Great customer service.', 'ar' => 'تماما كما هو موضح. خدمة العملاء ممتازة.'],
        ['name' => 'Youssef', 'city' => 'Marrakech', 'fr' => "J'étais hésitant mais au final très satisfait. Merci !", 'en' => 'I was hesitant but in the end very satisfied. Thank you!', 'ar' => 'كنت مترددا لكن في النهاية راض جدا. شكرا !'],
    ];
@endphp
<body class="antialiased bg-[#f5f5f0]" :class="{'rtl': currentLang === 'ar'}">

    <!-- Top promo marquee -->
    <div class="bg-black text-white text-xs font-bold py-2 overflow-hidden whitespace-nowrap relative">
        <div class="flex animate-marquee gap-8 w-max pl-8">
            @for($i = 0; $i < 2; $i++)
                <span>🔥 {{ $promoBadge }}</span>
                <span>•</span>
                <span x-text="({ fr: '{{ $i18n['fr']['limited_stock'] }}', en: '{{ $i18n['en']['limited_stock'] }}', ar: '{{ $i18n['ar']['limited_stock'] }}' })[currentLang]">{{ $i18n['fr']['limited_stock'] }}</span>
                <span>•</span>
                <span>🚚 {{ $badgeLabels['fr']['free_shipping'][1] }}</span>
                <span>•</span>
                <span>💵 {{ $badgeLabels['fr']['cod'][1] }}</span>
                <span>•</span>
            @endfor
        </div>
    </div>


    <!-- HERO: Big bold product title + image + order form + price -->
    <section class="relative bg-gradient-to-br {{ $heroBg }} stripe-bg text-white overflow-hidden">
        <div class="container mx-auto px-4 py-6 md:py-12 relative z-10 max-w-6xl">
            <div class="grid lg:grid-cols-2 gap-6 items-start">
                <!-- Left: Title + image + price -->
                <div class="text-center lg:text-left space-y-4">
                    @if($shortDescription)
                    <div class="inline-block bg-yellow-300 text-gray-900 px-3 py-1 rounded-md text-xs font-extrabold uppercase tracking-wider shadow-md">
                        {{ $shortDescription }}
                    </div>
                    @endif

                    <h1 class="font-display text-5xl md:text-7xl lg:text-8xl font-black uppercase leading-none drop-shadow-[0_4px_0_rgba(0,0,0,0.25)] text-white">
                        {{ $product->name }}
                    </h1>

                    @if(!empty($images))
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <div class="absolute -inset-4 bg-white/20 rounded-[40px] blur-xl"></div>
                        <div class="relative bg-white rounded-3xl p-3 shadow-2xl border-4 border-white">
                            <img src="{{ $images[0] }}" alt="{{ $product->name }}" class="w-full h-auto object-contain rounded-2xl" style="max-height: 420px;">
                        </div>
                    </div>
                    @endif

                    @php
                        // Helper function to format price - show decimals only when needed
                        if (!function_exists('formatPrice')) {
                            function formatPrice($price) {
                                if ($price == floor($price)) {
                                    return number_format($price, 0);
                                } else {
                                    return rtrim(rtrim(number_format($price, 2), '0'), '.');
                                }
                            }
                        }
                    @endphp
                    <!-- Big price display -->
                    <div class="flex items-center justify-center lg:justify-start gap-4 pt-2">
                        <div class="bg-white text-gray-900 px-6 py-3 rounded-2xl shadow-xl">
                            <div class="flex items-baseline gap-2">
                                <span class="font-display text-5xl md:text-6xl font-black">{{ formatPrice($product->price) }}</span>
                                <span class="text-xl font-bold text-gray-600">DHS</span>
                            </div>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                            <div class="text-sm line-through text-red-500 text-center font-semibold">{{ formatPrice($product->compare_at_price) }} DHS</div>
                            @endif
                        </div>
                        @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <div class="bg-yellow-300 text-red-700 font-black text-2xl md:text-3xl px-4 py-2 rounded-xl rotate-[-8deg] shadow-lg animate-pulse-scale">
                            -{{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}%
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Order form card -->
                <div class="bg-white rounded-3xl shadow-2xl p-5 md:p-7 text-gray-900 border-4 border-yellow-300" id="order-form">
                    <div class="text-center mb-4">
                        <div class="inline-block bg-red-500 text-white px-4 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider mb-2">
                            <span x-text="({ fr: '{{ $i18n['fr']['cod'] }}', en: '{{ $i18n['en']['cod'] }}', ar: '{{ $i18n['ar']['cod'] }}' })[currentLang]">{{ $i18n['fr']['cod'] }}</span>
                        </div>
                        <h2 class="font-display text-3xl md:text-4xl font-black uppercase text-gray-900"
                            x-text="({ fr: '{{ $i18n['fr']['order_now'] }}', en: '{{ $i18n['en']['order_now'] }}', ar: '{{ $i18n['ar']['order_now'] }}' })[currentLang]">
                            {{ $i18n['fr']['order_now'] }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1 font-semibold"
                            x-text="({ fr: '{{ $i18n['fr']['only_today'] }}', en: '{{ $i18n['en']['only_today'] }}', ar: '{{ $i18n['ar']['only_today'] }}' })[currentLang]">
                            {{ $i18n['fr']['only_today'] }}
                        </p>
                    </div>

                    @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-sm text-center">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                        <p class="font-semibold mb-1 text-center">يرجى تصحيح الأخطاء:</p>
                        <ul class="list-disc list-inside text-xs text-right">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @php
                        // Get form fields or use defaults
                        $formFields = $product->form_fields ?? [
                            ['id' => 'name', 'type' => 'text', 'label_ar' => 'الاسم', 'placeholder_ar' => 'الاسم الكامل', 'required' => true],
                            ['id' => 'phone', 'type' => 'tel', 'label_ar' => 'الهاتف', 'placeholder_ar' => 'رقم الهاتف', 'required' => true],
                            ['id' => 'note', 'type' => 'textarea', 'label_ar' => 'ملاحظة', 'placeholder_ar' => 'ملاحظة', 'required' => false],
                        ];
                    @endphp
                    <form action="{{ route('store.product.submit-lead', [$store->subdomain, $product->slug]) }}" method="POST" class="space-y-3" dir="rtl">
                        @csrf
                        <input type="hidden" name="language" value="ar">

                        @foreach($formFields as $field)
                            @if(($field['type'] ?? 'text') === 'textarea')
                                <textarea 
                                    name="{{ $field['id'] }}" 
                                    rows="2"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    placeholder="{{ $field['placeholder_ar'] ?? $field['label_ar'] ?? '' }}"
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 text-gray-900 font-semibold text-right"></textarea>
                            @elseif(($field['type'] ?? 'text') === 'select')
                                <select 
                                    name="{{ $field['id'] }}"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 text-gray-900 font-semibold text-right">
                                    <option value="">{{ $field['placeholder_ar'] ?? 'اختر...' }}</option>
                                    @if(!empty($field['options']))
                                        @foreach($field['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @else
                                <input 
                                    type="{{ $field['type'] ?? 'text' }}" 
                                    name="{{ $field['id'] }}" 
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    placeholder="{{ $field['placeholder_ar'] ?? $field['label_ar'] ?? '' }}"
                                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-yellow-400 text-gray-900 font-semibold text-right">
                            @endif
                        @endforeach

                        <button type="submit" class="w-full bg-gradient-to-r {{ $ctaBg }} text-white font-display text-2xl md:text-3xl uppercase py-4 rounded-xl shadow-xl hover:shadow-2xl transition-all hover:-translate-y-0.5 animate-pulse-scale">
                            ✓ أرسل طلبك
                        </button>
                    </form>

                    <!-- Trust badges row -->
                    <div class="flex flex-wrap justify-center gap-2 mt-4 pt-4 border-t border-gray-200">
                        @foreach(array_slice(empty($badges) ? ['free_shipping','money_back','cod','warranty'] : $badges, 0, 4) as $badgeKey)
                            @php $b = $badgeLabels['fr'][$badgeKey] ?? null; @endphp
                            @if($b)
                            <div class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded-lg text-xs font-bold text-gray-700">
                                <span>{{ $b[0] }}</span>
                                <span x-text="({ fr: '{{ addslashes($badgeLabels['fr'][$badgeKey][1] ?? '') }}', en: '{{ addslashes($badgeLabels['en'][$badgeKey][1] ?? '') }}', ar: '{{ addslashes($badgeLabels['ar'][$badgeKey][1] ?? '') }}' })[currentLang]">{{ $b[1] }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Red stats bar with big numbers -->
    <section class="bg-red-600 text-white py-5 md:py-7 relative overflow-hidden stripe-bg">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="grid grid-cols-3 gap-3 md:gap-6 text-center">
                <div>
                    <div class="font-display text-3xl md:text-5xl font-black text-yellow-300">{{ $statsCustomers }}+</div>
                    <div class="text-xs md:text-sm font-bold uppercase tracking-wider mt-1"
                        x-text="({ fr: '{{ $i18n['fr']['customers'] }}', en: '{{ $i18n['en']['customers'] }}', ar: '{{ $i18n['ar']['customers'] }}' })[currentLang]">{{ $i18n['fr']['customers'] }}</div>
                </div>
                <div class="border-x-2 border-red-500/70">
                    <div class="font-display text-3xl md:text-5xl font-black text-yellow-300">⭐ {{ $statsRating }}</div>
                    <div class="text-xs md:text-sm font-bold uppercase tracking-wider mt-1"
                        x-text="({ fr: '{{ $i18n['fr']['rating'] }}', en: '{{ $i18n['en']['rating'] }}', ar: '{{ $i18n['ar']['rating'] }}' })[currentLang]">{{ $i18n['fr']['rating'] }}</div>
                </div>
                <div>
                    <div class="font-display text-3xl md:text-5xl font-black text-yellow-300">{{ $statsReviews }}</div>
                    <div class="text-xs md:text-sm font-bold uppercase tracking-wider mt-1"
                        x-text="({ fr: '{{ $i18n['fr']['reviews'] }}', en: '{{ $i18n['en']['reviews'] }}', ar: '{{ $i18n['ar']['reviews'] }}' })[currentLang]">{{ $i18n['fr']['reviews'] }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content sections from user uploads (alternating colored banners) -->
    @if(!empty($product->landing_page_sections))
        @php $sectionColors = ['bg-gray-900 text-white', 'bg-red-500 text-white', 'bg-amber-400 text-gray-900', 'bg-emerald-600 text-white', 'bg-indigo-600 text-white']; @endphp
        @foreach($product->landing_page_sections as $i => $section)
            @php
                $sectionImg = null;
                if (!empty($section['image'])) {
                    $sectionImg = \App\Models\Product::resolvePublicImageUrl($section['image']);
                } elseif (isset($section['image_index']) && isset($images[$section['image_index']])) {
                    $sectionImg = $images[$section['image_index']];
                }
                $title = $section['title_fr'] ?? '';
                $desc = $section['description_fr'] ?? '';
                $bandClass = $sectionColors[$i % count($sectionColors)];
            @endphp

            @if($title)
            <div class="{{ $bandClass }} py-4 md:py-6 relative stripe-bg">
                <div class="container mx-auto px-4 max-w-4xl text-center">
                    <h2 class="font-display text-3xl md:text-5xl font-black uppercase tracking-wide">{{ $title }}</h2>
                </div>
            </div>
            @endif

            @if($sectionImg || $desc)
            <section class="py-6 md:py-10 bg-white">
                <div class="container mx-auto px-4 max-w-4xl space-y-5">
                    @if($sectionImg)
                    <div class="rounded-2xl overflow-hidden shadow-xl border-4 border-white ring-1 ring-gray-200">
                        <img src="{{ $sectionImg }}" alt="{{ $title }}" class="w-full h-auto object-cover">
                    </div>
                    @endif
                    @if($desc)
                    <p class="text-gray-700 text-base md:text-lg leading-relaxed text-center font-medium">{{ $desc }}</p>
                    @endif
                </div>
            </section>
            @endif
        @endforeach
    @endif

    <!-- Product description -->
    @if($product->description)
    <section class="py-6 md:py-10 bg-[#f5f5f0]">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="prose prose-lg max-w-none text-gray-800 font-medium text-center">
                {!! $product->description !!}
            </div>
        </div>
    </section>
    @endif

    <!-- Why choose / Features -->
    @if(!empty(array_filter($features, fn($f) => !empty($f['text']))))
    <section class="py-10 md:py-14 bg-gray-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 stripe-bg opacity-60"></div>
        <div class="container mx-auto px-4 max-w-5xl relative z-10">
            <h2 class="font-display text-3xl md:text-5xl font-black text-center uppercase mb-8 text-yellow-300"
                x-text="({ fr: '{{ $i18n['fr']['why_choose'] }}', en: '{{ $i18n['en']['why_choose'] }}', ar: '{{ $i18n['ar']['why_choose'] }}' })[currentLang]">
                {{ $i18n['fr']['why_choose'] }}
            </h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($features as $feature)
                    @if(!empty($feature['text']))
                    <div class="bg-white text-gray-900 rounded-2xl p-5 shadow-lg border-b-4 border-yellow-300 text-center hover:-translate-y-1 transition">
                        <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center text-3xl mb-3 mx-auto">
                            {{ $featureIconMap[$feature['icon'] ?? 'clean'] ?? '✨' }}
                        </div>
                        <p class="font-bold text-sm leading-snug">{{ $feature['text'] }}</p>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Big CTA banner (order again) -->
    <section class="bg-gradient-to-r {{ $ctaBg }} text-white py-10 md:py-14 relative overflow-hidden stripe-bg">
        <div class="container mx-auto px-4 text-center max-w-3xl relative z-10">
            <div class="inline-block bg-yellow-300 text-gray-900 px-4 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider mb-4">
                🔥 {{ $promoBadge }}
            </div>
            <h2 class="font-display text-4xl md:text-6xl font-black uppercase mb-3 drop-shadow-lg">
                {{ $product->name }}
            </h2>
            <p class="text-lg md:text-xl font-bold mb-6 opacity-95"
                x-text="({ fr: '{{ $i18n['fr']['guarantee'] }}', en: '{{ $i18n['en']['guarantee'] }}', ar: '{{ $i18n['ar']['guarantee'] }}' })[currentLang]">
                {{ $i18n['fr']['guarantee'] }}
            </p>
            <a href="#order-form" class="inline-block bg-white text-gray-900 font-display text-2xl md:text-3xl uppercase px-8 py-4 rounded-2xl shadow-2xl hover:scale-105 transition-transform animate-pulse-scale">
                ➤ <span x-text="({ fr: '{{ $i18n['fr']['order_now'] }}', en: '{{ $i18n['en']['order_now'] }}', ar: '{{ $i18n['ar']['order_now'] }}' })[currentLang]">{{ $ctaText }}</span>
            </a>
        </div>
    </section>

    <!-- How to order: 3 steps -->
    <section class="py-10 md:py-14 bg-white">
        <div class="container mx-auto px-4 max-w-5xl">
            <h2 class="font-display text-3xl md:text-5xl font-black text-center uppercase mb-10 text-gray-900"
                x-text="({ fr: '{{ $i18n['fr']['how_to_order'] }}', en: '{{ $i18n['en']['how_to_order'] }}', ar: '{{ $i18n['ar']['how_to_order'] }}' })[currentLang]">
                {{ $i18n['fr']['how_to_order'] }}
            </h2>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach([1,2,3] as $n)
                @php
                    $icons = [1 => '📝', 2 => '📞', 3 => '🚚'];
                    $colors = [1 => 'bg-red-500', 2 => 'bg-amber-500', 3 => 'bg-emerald-600'];
                @endphp
                <div class="text-center">
                    <div class="relative inline-block mb-3">
                        <div class="w-20 h-20 rounded-full {{ $colors[$n] }} text-white flex items-center justify-center text-4xl shadow-xl">
                            {{ $icons[$n] }}
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-gray-900 text-yellow-300 flex items-center justify-center font-black text-lg shadow-md">{{ $n }}</div>
                    </div>
                    <h3 class="font-black text-lg text-gray-900 mb-1"
                        x-text="({ fr: '{{ $i18n['fr']["step{$n}_t"] }}', en: '{{ $i18n['en']["step{$n}_t"] }}', ar: '{{ $i18n['ar']["step{$n}_t"] }}' })[currentLang]">{{ $i18n['fr']["step{$n}_t"] }}</h3>
                    <p class="text-sm text-gray-600 font-medium"
                        x-text="({ fr: '{{ $i18n['fr']["step{$n}_d"] }}', en: '{{ $i18n['en']["step{$n}_d"] }}', ar: '{{ $i18n['ar']["step{$n}_d"] }}' })[currentLang]">{{ $i18n['fr']["step{$n}_d"] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-10 md:py-14 bg-amber-50">
        <div class="container mx-auto px-4 max-w-6xl">
            <h2 class="font-display text-3xl md:text-5xl font-black text-center uppercase mb-8 text-gray-900"
                x-text="({ fr: '{{ $i18n['fr']['testimonials'] }}', en: '{{ $i18n['en']['testimonials'] }}', ar: '{{ $i18n['ar']['testimonials'] }}' })[currentLang]">
                {{ $i18n['fr']['testimonials'] }}
            </h2>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach($testimonials as $tst)
                <div class="bg-white rounded-2xl p-5 shadow-md border-b-4 border-amber-300">
                    <div class="flex gap-1 text-yellow-400 text-xl mb-2">★★★★★</div>
                    <p class="text-gray-700 text-sm leading-relaxed mb-4"
                        x-text="({ fr: '{{ addslashes($tst['fr']) }}', en: '{{ addslashes($tst['en']) }}', ar: '{{ addslashes($tst['ar']) }}' })[currentLang]">
                        "{{ $tst['fr'] }}"
                    </p>
                    <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-red-500 text-white font-black flex items-center justify-center">
                            {{ strtoupper(substr($tst['name'], 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 text-sm">{{ $tst['name'] }}</div>
                            <div class="text-xs text-gray-500">{{ $tst['city'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Trust badges strip -->
    @if(!empty($badges))
    <section class="bg-white py-6 border-y-2 border-gray-100">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($badges as $badgeKey)
                    @php $b = $badgeLabels['fr'][$badgeKey] ?? null; @endphp
                    @if($b)
                    <div class="flex items-center gap-2 bg-gray-50 border-l-4 border-emerald-500 px-3 py-2.5 rounded-lg">
                        <span class="text-2xl">{{ $b[0] }}</span>
                        <span class="font-bold text-gray-800 text-xs md:text-sm"
                            x-text="({ fr: '{{ addslashes($badgeLabels['fr'][$badgeKey][1] ?? '') }}', en: '{{ addslashes($badgeLabels['en'][$badgeKey][1] ?? '') }}', ar: '{{ addslashes($badgeLabels['ar'][$badgeKey][1] ?? '') }}' })[currentLang]">
                            {{ $b[1] }}
                        </span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Sticky order bar - Full width for all devices -->
    <a href="#order-form" class="fixed bottom-0 left-0 right-0 z-40 bg-gradient-to-r {{ $ctaBg }} text-white font-display text-xl md:text-2xl uppercase py-5 md:py-6 text-center shadow-2xl animate-pulse-scale flex items-center justify-center gap-3">
        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        ➤ <span x-text="({ fr: '{{ $i18n['fr']['order_now'] }}', en: '{{ $i18n['en']['order_now'] }}', ar: '{{ $i18n['ar']['order_now'] }}' })[currentLang]">{{ $ctaText }}</span>
        - {{ number_format($product->price, 0) }} DHS
        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </a>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-6 pb-24 text-center text-xs">
        <div class="container mx-auto px-4">
            <div class="font-bold text-white mb-1">{{ $store->name ?? 'Store' }}</div>
            © {{ date('Y') }} — All rights reserved.
        </div>
    </footer>

</body>
</html>
