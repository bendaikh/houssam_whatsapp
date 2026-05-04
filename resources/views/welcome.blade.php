<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->site_name ?? config('app.name') }} - متجر إلكتروني</title>
    @if($settings->meta_description)
    <meta name="description" content="{{ $settings->meta_description }}">
    @endif
    @if($settings->meta_keywords)
    <meta name="keywords" content="{{ $settings->meta_keywords }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @if($settings->site_favicon)
    <link rel="icon" href="/storage/{{ $settings->site_favicon }}">
    @endif
    
    @if($store->facebook_pixel_enabled && $store->facebook_pixel_id)
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $store->facebook_pixel_id }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $store->facebook_pixel_id }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
    @endif
    
    @if($store->tiktok_pixel_enabled && $store->tiktok_pixel_id)
    <!-- TikTok Pixel Code -->
    <script>
        !function (w, d, t) {
          w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
          ttq.load('{{ $store->tiktok_pixel_id }}');
          ttq.page();
        }(window, document, 'ttq');
    </script>
    <!-- End TikTok Pixel Code -->
    @endif
    
    <style>
        :root {
            --primary-color: {{ $settings->primary_color }};
            --secondary-color: {{ $settings->secondary_color }};
            --accent-color: {{ $settings->accent_color }};
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-900">
    <!-- Preview Mode Banner -->
    @if(isset($isPreview) && $isPreview)
    <div class="fixed top-0 left-0 right-0 z-[100] bg-yellow-500 text-black py-2 px-4 text-center font-bold shadow-lg">
        <span class="material-icons align-middle text-sm ml-2">visibility</span>
        وضع المعاينة - هذا هو شكل موقعك
        <a href="{{ route('app.website-customization') }}" class="mr-4 underline hover:no-underline">العودة للمحرر</a>
    </div>
    <div class="h-10"></div>
    @endif

    <!-- Top Banner -->
    @if($settings->show_top_banner)
    <div class="text-white py-3 px-4 text-center" style="background-color: {{ $settings->banner_bg_color }}">
        <div class="flex items-center justify-center gap-2 text-sm font-medium">
            <span class="material-icons text-xl animate-pulse">{{ $settings->banner_icon }}</span>
            <span>{{ $settings->banner_text }}</span>
        </div>
    </div>
    @endif

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    @if($settings->site_logo)
                    <img src="/storage/{{ $settings->site_logo }}" alt="{{ $settings->site_name }}" class="h-12">
                    @else
                    <a href="{{ route('store.home', $store->subdomain) }}" class="text-3xl font-bold bg-gradient-to-r from-emerald-500 to-blue-600 bg-clip-text text-transparent">
                        {{ $settings->site_name }}
                    </a>
                    @endif
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('store.home', $store->subdomain) }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">الرئيسية</a>
                    <a href="#categories" class="text-gray-700 hover:text-emerald-600 font-medium transition">الفئات</a>
                    <a href="#featured" class="text-gray-700 hover:text-emerald-600 font-medium transition">المميزة</a>
                    <a href="#contact" class="text-gray-700 hover:text-emerald-600 font-medium transition">اتصل بنا</a>
                </div>
                <div class="flex items-center gap-4">
                    @if($settings->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp_number) }}" target="_blank" class="text-green-600 hover:text-green-700 font-medium transition flex items-center gap-2" title="تواصل معنا عبر واتساب">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span class="hidden md:inline">واتساب</span>
                    </a>
                    @endif
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition">
                            <span class="material-icons align-middle">dashboard</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-16 pb-12 px-4 sm:px-6 lg:px-8 min-h-[500px] flex items-center" 
        @if($settings->hero_background_image)
            style="background-image: url('{{ url('storage/' . $settings->hero_background_image) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
        @else
            style="background-color: {{ $settings->hero_background_color }}"
        @endif
    >
        <!-- Overlay for better text readability -->
        @if($settings->hero_background_image)
        <div class="absolute inset-0 bg-black/40"></div>
        @endif
        
        <div class="container mx-auto max-w-7xl relative z-10">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6 {{ $settings->hero_background_image ? 'text-white' : 'text-gray-900' }}">
                    {{ $settings->hero_title ?? 'مرحباً بكم في متجرنا' }}
                </h1>
                <p class="text-xl md:text-2xl mb-8 max-w-4xl mx-auto leading-relaxed {{ $settings->hero_background_image ? 'text-white' : 'text-gray-600' }}">
                    {!! nl2br(e($settings->hero_subtitle ?? 'مرحباً بكم في متجرنا، وجهتكم الأولى لاكتشاف منتجات فريدة تم اختيارها بعناية لتناسب احتياجاتكم اليومية وتمنحكم تجربة تسوق استثنائية. معنا ستجدون كل ما هو مميز ونادر في السوق.')) !!}
                </p>
                <a href="#featured" class="inline-flex items-center justify-center px-8 py-4 text-white text-lg font-semibold rounded-lg transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" style="background-color: {{ $settings->primary_color }}">
                    <span class="material-icons ml-2">shopping_bag</span>
                    {{ $settings->hero_button_text ?? 'تسوق الآن' }}
                </a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-16 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="container mx-auto max-w-7xl">
            <div class="flex items-center justify-center mb-12">
                <span class="material-icons text-4xl ml-3" style="color: {{ $settings->accent_color }}">auto_awesome</span>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">فئاتنا</h2>
                    <p class="text-gray-600">اختر حسب الفئة</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($categories as $category)
                <a href="{{ route('store.home', ['subdomain' => $store->subdomain, 'category' => $category->slug]) }}#products" class="group bg-gradient-to-br rounded-xl p-8 text-center hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-translate-y-1" style="background: linear-gradient(to bottom right, {{ $category->color }}20, {{ $category->color }}10)">
                    <span class="material-icons text-5xl mb-4" style="color: {{ $category->color }}">{{ $category->icon }}</span>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $category->name }}</h3>
                    <span class="material-icons text-gray-400">arrow_forward_ios</span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    @if($featuredProducts->count() > 0)
    <section id="featured" class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto max-w-7xl">
            <div class="flex items-center justify-center mb-12">
                <span class="material-icons text-4xl ml-3" style="color: {{ $settings->accent_color }}">auto_awesome</span>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">المنتجات المميزة</h2>
                    <p class="text-gray-600">احصل على أفضل المنتجات</p>
                </div>
            </div>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">
                إليكم مجموعة من أفضل المنتجات مبيعاً لدينا.
            </p>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                @foreach($featuredProducts as $product)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group transform hover:-translate-y-2">
                    <div class="relative">
                        <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center overflow-hidden">
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        </div>
                        @if($product->discount_percentage > 0)
                        <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            -{{ $product->discount_percentage }}%
                        </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-baseline gap-2 mb-3">
                            <span class="text-2xl font-bold" style="color: {{ $settings->primary_color }}">{{ number_format($product->price, 2) }} درهم</span>
                            @if($product->compare_at_price)
                            <span class="text-gray-400 line-through text-sm">{{ number_format($product->compare_at_price, 2) }} درهم</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition line-clamp-2">{{ $product->name }}</h3>
                        <a href="{{ route('store.product.show', [$store->subdomain, $product->slug]) }}" class="block w-full py-3 text-white text-center rounded-lg font-medium transition" style="background-color: {{ $settings->primary_color }}">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- All Products Section -->
    <section id="products" class="py-16 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="container mx-auto max-w-7xl">
            @php
                $activeCategory = request('category') ? $categories->where('slug', request('category'))->first() : null;
            @endphp
            
            <div class="flex items-center justify-center mb-12">
                <span class="material-icons text-4xl ml-3" style="color: {{ $settings->secondary_color }}">inventory_2</span>
                <div>
                    @if($activeCategory)
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $activeCategory->name }}</h2>
                        <p class="text-gray-600">منتجات فئة {{ $activeCategory->name }}</p>
                    @else
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">جميع المنتجات</h2>
                        <p class="text-gray-600">تصفح كتالوجنا الكامل</p>
                    @endif
                </div>
            </div>
            
            @if($activeCategory)
            <div class="text-center mb-8">
                <a href="{{ route('store.home', $store->subdomain) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                    <span class="material-icons ml-2 text-sm">close</span>
                    إزالة الفلتر
                </a>
            </div>
            @endif

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                @foreach($products as $product)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group transform hover:-translate-y-2">
                    <div class="relative">
                        <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center overflow-hidden">
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        </div>
                        @if($product->discount_percentage > 0)
                        <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            -{{ $product->discount_percentage }}%
                        </div>
                        @endif
                        @if($product->stock <= 0)
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                            <span class="bg-red-500 text-white px-4 py-2 rounded-lg font-bold">نفذت الكمية</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-2">{{ $product->category->name ?? 'غير مصنف' }}</div>
                        <div class="flex items-baseline gap-2 mb-3">
                            <span class="text-2xl font-bold" style="color: {{ $settings->primary_color }}">{{ number_format($product->price, 2) }} درهم</span>
                            @if($product->compare_at_price)
                            <span class="text-gray-400 line-through text-sm">{{ number_format($product->compare_at_price, 2) }} درهم</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition line-clamp-2">{{ $product->name }}</h3>
                        <a href="{{ route('store.product.show', [$store->subdomain, $product->slug]) }}" class="block w-full py-3 text-white text-center rounded-lg font-medium transition" style="background-color: {{ $settings->primary_color }}">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            @if($products->count() === 0)
            <p class="text-center text-gray-500">لا توجد منتجات متاحة حالياً.</p>
            @endif

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </section>

    <!-- Contact/FAQ Section -->
    <section id="contact" class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto max-w-4xl">
            <div class="flex items-center justify-center mb-12">
                <span class="material-icons text-4xl ml-3" style="color: {{ $settings->secondary_color }}">question_answer</span>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">أسئلة وأجوبة</h2>
                    <p class="text-gray-600">الأسئلة الشائعة</p>
                </div>
            </div>
            
            <div class="space-y-4 mb-12">
                <details class="bg-white rounded-lg border-2 border-gray-200 hover:border-emerald-500 transition-colors group">
                    <summary class="px-6 py-4 font-semibold cursor-pointer flex justify-between items-center text-gray-900 group-hover:text-emerald-600 transition">
                        <span>كيف أقوم بتقديم طلب؟</span>
                        <span class="material-icons transform group-open:rotate-180 transition-transform">keyboard_arrow_down</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        تصفح منتجاتنا، انقر على المنتج الذي يعجبك، وتواصل معنا عبر واتساب لإتمام طلبك.
                    </div>
                </details>
                <details class="bg-white rounded-lg border-2 border-gray-200 hover:border-emerald-500 transition-colors group">
                    <summary class="px-6 py-4 font-semibold cursor-pointer flex justify-between items-center text-gray-900 group-hover:text-emerald-600 transition">
                        <span>سياسة الدفع عند الاستلام</span>
                        <span class="material-icons transform group-open:rotate-180 transition-transform">keyboard_arrow_down</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        نوفر لكم خيار الدفع عند الاستلام في معظم المدن المغربية لراحتكم وثقتكم. تدفعون مباشرة عند استلام الطرد من عامل التوصيل.
                    </div>
                </details>
                <details class="bg-white rounded-lg border-2 border-gray-200 hover:border-emerald-500 transition-colors group">
                    <summary class="px-6 py-4 font-semibold cursor-pointer flex justify-between items-center text-gray-900 group-hover:text-emerald-600 transition">
                        <span>كم تستغرق عملية الشحن؟</span>
                        <span class="material-icons transform group-open:rotate-180 transition-transform">keyboard_arrow_down</span>
                    </summary>
                    <div class="px-6 pb-4 text-gray-600">
                        تستغرق عملية الشحن عادةً من 2 إلى 5 أيام عمل حسب موقعك في المغرب.
                    </div>
                </details>
            </div>

            <div class="text-center p-8 bg-gradient-to-r from-emerald-50 to-blue-50 rounded-2xl">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">هل لديك أسئلة أخرى؟</h3>
                <p class="text-gray-600 mb-6">إذا كنت تريد المزيد من المعلومات، يمكنك دائماً التواصل معنا.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if($settings->contact_email)
                    <a href="mailto:{{ $settings->contact_email }}" class="inline-flex items-center px-6 py-3 text-white rounded-lg font-medium transition shadow-lg hover:shadow-xl" style="background-color: {{ $settings->primary_color }}">
                        <span class="material-icons mr-2">email</span>
                        {{ $settings->contact_email }}
                    </a>
                    @endif
                    @if($settings->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp_number) }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition shadow-lg hover:shadow-xl">
                        <span class="material-icons mr-2">chat</span>
                        WhatsApp
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-gray-900 to-black text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl">
            <!-- Footer Top Badges -->
            @if($settings->features && count($settings->features) > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12 text-center">
                @foreach($settings->features as $feature)
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3" style="background-color: {{ $feature['color'] ?? '#10b981' }}">
                        <span class="material-icons text-white text-3xl">{{ $feature['icon'] ?? 'star' }}</span>
                    </div>
                    <h3 class="font-bold text-sm mb-1">{{ $feature['title'] ?? 'Feature' }}</h3>
                </div>
                @endforeach
            </div>
            @endif

            @if($settings->contact_phone)
            <div class="border-t border-gray-700 pt-8 mb-8">
                <p class="text-center text-sm text-gray-400 mb-4">
                    متاحون من الساعة 9 صباحاً حتى 8 مساءً. نحن جاهزون للرد على استفساراتكم. 
                    <a href="tel:{{ $settings->contact_phone }}" style="color: {{ $settings->primary_color }}" class="hover:underline">{{ $settings->contact_phone }}</a>
                </p>
            </div>
            @endif

            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">من نحن</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#contact" class="hover:text-emerald-400 transition">اتصل بنا</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">عن المتجر</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">سياسة الخصوصية</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">الفئات</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @foreach($categories->take(4) as $category)
                        <li><a href="{{ route('store.home', ['subdomain' => $store->subdomain, 'category' => $category->slug]) }}" class="hover:text-emerald-400 transition">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-4">روابط سريعة</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('store.home', $store->subdomain) }}" class="hover:text-emerald-400 transition">الرئيسية</a></li>
                        <li><a href="#featured" class="hover:text-emerald-400 transition">المنتجات المميزة</a></li>
                        <li><a href="#contact" class="hover:text-emerald-400 transition">اتصل بنا</a></li>
                    </ul>
                </div>
                <div>
                    @if($settings->site_logo)
                    <img src="/storage/{{ $settings->site_logo }}" alt="{{ $settings->site_name }}" class="h-12 mb-4">
                    @else
                    <div class="text-2xl font-bold bg-gradient-to-r from-emerald-500 to-blue-600 bg-clip-text text-transparent mb-4">{{ $settings->site_name }}</div>
                    @endif
                    <p class="text-sm text-gray-400 mb-4">{{ $settings->footer_about ?? 'متجركم الموثوق للمنتجات عالية الجودة.' }}</p>
                    @if($settings->facebook_url || $settings->instagram_url || $settings->twitter_url)
                    <div class="flex gap-3">
                        @if($settings->facebook_url)
                        <a href="{{ $settings->facebook_url }}" target="_blank" class="hover:opacity-75 transition">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if($settings->instagram_url)
                        <a href="{{ $settings->instagram_url }}" target="_blank" class="hover:opacity-75 transition">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @endif
                        @if($settings->twitter_url)
                        <a href="{{ $settings->twitter_url }}" target="_blank" class="hover:opacity-75 transition">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="pt-8 border-t border-gray-700">
                <p class="text-center text-sm text-gray-400">{{ $settings->footer_copyright ?? '© 2026 ' . $settings->site_name . '. All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    @if($settings->whatsapp_number)
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp_number) }}" target="_blank" class="fixed bottom-6 left-6 z-50 w-16 h-16 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 animate-bounce" title="تواصل معنا عبر واتساب">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </a>
    @endif
</body>
</html>
