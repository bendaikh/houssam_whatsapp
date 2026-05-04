<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>شكرًا لثقتكم بنا</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center">
        <!-- Success Icon -->
        <div class="w-24 h-24 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center">
            <span class="text-5xl">✅</span>
        </div>
        
        <!-- Main Title -->
        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-6">
            شكرًا لثقتكم بنا
        </h1>
        
        <!-- Success Message -->
        <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6 mb-6">
            <p class="text-lg text-gray-800 leading-relaxed">
                تم تسجيل طلبكم بنجاح، وهو الآن قيد المراجعة والتحضير.
            </p>
        </div>
        
        <!-- Phone Call Notice -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-4">
                <span class="text-3xl">📞</span>
                <div class="text-right">
                    <p class="text-gray-800 leading-relaxed">
                        سيتواصل معكم فريقنا خلال وقت قصير لتأكيد الطلب والتحقق من معلومات التوصيل.
                    </p>
                    <p class="text-gray-700 mt-2 leading-relaxed">
                        يرجى التأكد من إبقاء هاتفكم متاحًا والرد على المكالمة حتى نتمكن من تأكيد طلبكم وإرساله بسرعة.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Delivery Notice -->
        <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-4">
                <span class="text-3xl">🚚</span>
                <p class="text-gray-800 leading-relaxed text-right">
                    بعد التأكيد، سيتم تجهيز وشحن طلبكم مع توصيل سريع خلال 24 إلى 48 ساعة كحد أقصى.
                </p>
            </div>
        </div>
        
        <!-- Warning Notice -->
        <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6 mb-8">
            <div class="flex items-start gap-4">
                <span class="text-3xl">⚠️</span>
                <div class="text-right">
                    <p class="text-red-800 font-bold mb-1">مهم:</p>
                    <p class="text-red-700 leading-relaxed">
                        عدم الرد على مكالمة التأكيد قد يؤدي إلى تأخير أو إلغاء الطلب تلقائيًا.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Closing Message -->
        <div class="border-t-2 border-gray-100 pt-6">
            <p class="text-gray-700 text-lg leading-relaxed">
                نشكركم على اختياركم لنا، ونتطلع إلى تقديم تجربة ممتازة لكم 🌿
            </p>
        </div>
        
        <!-- Back Button -->
        @if(isset($product) && isset($store))
        <div class="mt-8">
            <a href="{{ route('store.product.show', [$store->subdomain, $product->slug]) }}" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold text-lg rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>العودة للمنتج</span>
            </a>
        </div>
        @elseif(isset($store))
        <div class="mt-8">
            <a href="{{ route('store.home', $store->subdomain) }}" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold text-lg rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>تصفح المنتجات</span>
            </a>
        </div>
        @endif
    </div>
</body>
</html>
