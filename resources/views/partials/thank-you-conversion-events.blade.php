@php
    $product = $lead->product;
    $orderValue = (float) ($lead->selected_price ?? $product->price);
@endphp

@if($trackConversion)
    @php
        $hasFacebookPixels = ($store->activeFacebookPixels ?? collect())->isNotEmpty()
            || ($store->facebook_pixel_enabled && $store->facebook_pixel_id);
    @endphp
    @if($hasFacebookPixels)
    <script>
        if (typeof fbq === 'function') {
            fbq('track', 'Lead', {
                content_name: @json($product->name),
                content_ids: [@json((string) $product->id)],
                content_type: 'product',
                value: {{ $orderValue }},
                currency: 'MAD',
                order_id: @json((string) $lead->id)
            });
        }
    </script>
    @endif

    @if($store->tiktok_pixel_enabled && $store->tiktok_pixel_id)
    <script>
        if (typeof ttq === 'object' && typeof ttq.track === 'function') {
            ttq.track('SubmitForm', {
                content_name: @json($product->name),
                content_id: @json((string) $product->id),
                content_type: 'product',
                value: {{ $orderValue }},
                currency: 'MAD'
            });
        }
    </script>
    @endif
@endif
