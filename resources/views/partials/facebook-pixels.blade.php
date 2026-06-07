@php
    if ($store->relationLoaded('activeFacebookPixels')) {
        $facebookPixels = $store->activeFacebookPixels;
    } else {
        $facebookPixels = $store->activeFacebookPixels()->get();
    }

    if ($facebookPixels->isEmpty() && $store->facebook_pixel_enabled && $store->facebook_pixel_id) {
        $facebookPixels = collect([(object) [
            'pixel_id' => $store->facebook_pixel_id,
            'name' => 'Primary Pixel',
        ]]);
    }
@endphp

@if($facebookPixels->isNotEmpty())
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
    @foreach($facebookPixels as $pixel)
    fbq('init', '{{ $pixel->pixel_id }}');
    @endforeach
    fbq('track', 'PageView');
    @if(!empty($facebookPixelEvents))
        @foreach($facebookPixelEvents as $event)
            fbq('track', '{{ $event['name'] }}'{!! !empty($event['params']) ? ', ' . json_encode($event['params']) : '' !!});
        @endforeach
    @endif
</script>
<noscript>
    @foreach($facebookPixels as $pixel)
    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixel->pixel_id }}&ev=PageView&noscript=1"/>
    @endforeach
</noscript>
<!-- End Facebook Pixel Code -->
@endif
