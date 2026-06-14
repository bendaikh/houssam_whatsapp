@php
    $product = $lead->product;
    $orderValue = $lead->selected_price ?? $product->price;
@endphp
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

    @include('partials.facebook-pixels', [
        'store' => $store,
        'facebookPixelEvents' => $trackConversion ? [[
            'name' => 'Lead',
            'params' => [
                'content_name' => $product->name,
                'content_ids' => [(string) $product->id],
                'content_type' => 'product',
                'value' => (float) $orderValue,
                'currency' => 'MAD',
                'order_id' => (string) $lead->id,
            ],
        ]] : [],
    ])

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

    @include('partials.thank-you-conversion-events', compact('store', 'lead', 'trackConversion'))
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

        <!-- Order Summary -->
        <div class="bg-gray-50 border-2 border-gray-200 rounded-2xl p-6 mb-6 text-right">
            <h2 class="text-lg font-bold text-gray-900 mb-4 text-center">تفاصيل الطلب</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">رقم الطلب</dt>
                    <dd class="text-gray-900 font-bold">#{{ $lead->id }}</dd>
                </div>
                @if($lead->name)
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">الاسم</dt>
                    <dd class="text-gray-900 font-semibold">{{ $lead->name }}</dd>
                </div>
                @endif
                @if($lead->phone)
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">الهاتف</dt>
                    <dd class="text-gray-900 font-semibold" dir="ltr">{{ $lead->phone }}</dd>
                </div>
                @endif
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">المنتج</dt>
                    <dd class="text-gray-900 font-semibold">{{ $product->name }}</dd>
                </div>
                @if($lead->promotion)
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">العرض</dt>
                    <dd class="text-gray-900">{{ $lead->promotion->label ?? $lead->promotion->quantity_range }}</dd>
                </div>
                @endif
                @if($lead->variation)
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">الخيار</dt>
                    <dd class="text-gray-900">
                        @if(!empty($lead->variation->attributes) && is_array($lead->variation->attributes))
                            {{ implode(' / ', array_map(fn($k, $v) => "$k: $v", array_keys($lead->variation->attributes), $lead->variation->attributes)) }}
                        @else
                            {{ $lead->variation->name ?? '—' }}
                        @endif
                    </dd>
                </div>
                @endif
                @if($orderValue)
                <div class="flex justify-between gap-4 border-t border-gray-200 pt-3">
                    <dt class="text-gray-500">المبلغ</dt>
                    <dd class="text-green-700 font-black text-lg">{{ number_format((float) $orderValue, 2) }} درهم</dd>
                </div>
                @endif
            </dl>
        </div>
        
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
    </div>
</body>
</html>
