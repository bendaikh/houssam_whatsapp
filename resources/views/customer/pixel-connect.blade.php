@extends('layouts.customer')

@section('title', 'Pixel Connect')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h1 class="text-3xl font-bold text-white">Pixel Connect</h1>
        </div>
        <p class="text-gray-400">Connect Facebook Pixel and TikTok Pixel to track conversions and optimize your marketing</p>
    </div>

    <div id="notification-container"></div>

    <!-- Facebook Pixel Section -->
    <div class="bg-[#0f1c2e] rounded-xl shadow-sm border border-white/10 mb-6 overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <div>
                        <h2 class="text-xl font-bold text-white">Facebook Pixels</h2>
                        <p class="text-sm text-gray-400">Manage multiple Facebook Pixels for conversion tracking</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-medium">
                    {{ $activeStore->facebookPixels->where('is_enabled', true)->count() }} active
                </span>
            </div>
        </div>

        <div class="p-6">
            @if($activeStore->facebookPixels->isNotEmpty())
            <div class="space-y-3 mb-6">
                @foreach($activeStore->facebookPixels as $pixel)
                <div class="bg-[#0a1628] border border-white/10 rounded-lg p-4" id="pixel-row-{{ $pixel->id }}">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Name</label>
                                <input type="text" id="pixel-name-{{ $pixel->id }}" value="{{ $pixel->name }}"
                                    class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Pixel ID</label>
                                <input type="text" id="pixel-id-{{ $pixel->id }}" value="{{ $pixel->pixel_id }}"
                                    class="w-full px-3 py-2 bg-[#0f1c2e] border border-white/10 rounded-lg text-white text-sm font-mono">
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <button onclick="togglePixel({{ $pixel->id }})"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition {{ $pixel->is_enabled ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}"
                                id="pixel-toggle-{{ $pixel->id }}">
                                {{ $pixel->is_enabled ? 'Enabled' : 'Disabled' }}
                            </button>
                            <button onclick="updatePixel({{ $pixel->id }})" class="px-3 py-2 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 rounded-lg text-sm transition">
                                Save
                            </button>
                            <button onclick="deletePixel({{ $pixel->id }})" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-sm transition">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="mb-6 bg-gray-500/10 border border-gray-500/30 rounded-lg p-4 text-sm text-gray-400">
                No Facebook Pixels configured yet. Add your first pixel below.
            </div>
            @endif

            <div class="mb-6 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                <p class="text-sm font-medium text-blue-400 mb-1">How to Find Your Facebook Pixel ID</p>
                <ol class="text-xs text-blue-300 space-y-1 list-decimal list-inside">
                    <li>Go to <a href="https://business.facebook.com/events_manager2" target="_blank" class="underline hover:text-blue-200">Facebook Events Manager</a></li>
                    <li>Select your pixel from the list</li>
                    <li>Copy the Pixel ID and add it below</li>
                </ol>
            </div>

            <form id="facebook-pixel-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Pixel Name</label>
                        <input type="text" name="name" placeholder="Main Store Pixel"
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Facebook Pixel ID *</label>
                        <input type="text" name="pixel_id" placeholder="123456789012345" required
                            class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500">
                    </div>
                </div>

                <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Facebook Pixel
                </button>
            </form>
        </div>
    </div>

    <!-- TikTok Pixel Section -->
    <div class="bg-[#0f1c2e] rounded-xl shadow-sm border border-white/10 mb-6 overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/>
                    </svg>
                    <div>
                        <h2 class="text-xl font-bold text-white">TikTok Pixel</h2>
                        <p class="text-sm text-gray-400">Track conversions and optimize TikTok ads</p>
                    </div>
                </div>
                @if($activeStore->tiktok_pixel_enabled && $activeStore->tiktok_pixel_id)
                <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Connected
                </span>
                @else
                <span class="px-3 py-1 bg-gray-500/20 text-gray-400 rounded-full text-sm font-medium">Not Connected</span>
                @endif
            </div>
        </div>

        <div class="p-6">
            @if($activeStore->tiktok_pixel_enabled && $activeStore->tiktok_pixel_id)
            <!-- Connected State -->
            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-green-400 mb-1">TikTok Pixel is Active</p>
                        <p class="text-xs text-green-300">Pixel ID: <code class="bg-green-500/20 px-2 py-0.5 rounded">{{ $activeStore->tiktok_pixel_id }}</code></p>
                    </div>
                </div>
            </div>
            
            <button onclick="disconnectTikTokPixel()" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg font-medium transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Disconnect TikTok Pixel
            </button>
            @else
            <!-- Not Connected State -->
            <div class="mb-6 bg-pink-500/10 border border-pink-500/30 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-pink-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-pink-400 mb-1">How to Find Your TikTok Pixel ID</p>
                        <ol class="text-xs text-pink-300 space-y-1 list-decimal list-inside">
                            <li>Go to <a href="https://ads.tiktok.com/i18n/events_manager" target="_blank" class="underline hover:text-pink-200">TikTok Events Manager</a></li>
                            <li>Select "Web Events" and choose your pixel</li>
                            <li>Click on "Settings" to view your Pixel ID</li>
                            <li>Copy the Pixel ID and paste it below</li>
                        </ol>
                    </div>
                </div>
            </div>

            <form id="tiktok-pixel-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        TikTok Pixel ID *
                    </label>
                    <input type="text" name="tiktok_pixel_id" placeholder="ABCDEFGHIJKLMNOP" required class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-white placeholder-gray-500">
                    <p class="text-xs text-gray-400 mt-1">Your TikTok Pixel ID</p>
                </div>

                <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Connect TikTok Pixel
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Help Section -->
    <div class="bg-[#0f1c2e] rounded-xl shadow-sm border border-white/10 p-6">
        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Why Connect Pixels?
        </h2>
        <div class="space-y-3 text-sm text-gray-300">
            <div class="flex gap-3">
                <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <div>
                    <strong class="text-white">Track Conversions:</strong> Monitor purchases, sign-ups, and other important actions on your website.
                </div>
            </div>
            <div class="flex gap-3">
                <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <div>
                    <strong class="text-white">Optimize Ads:</strong> Use conversion data to optimize your ad campaigns for better performance.
                </div>
            </div>
            <div class="flex gap-3">
                <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <div>
                    <strong class="text-white">Build Audiences:</strong> Create custom audiences based on website visitors for retargeting campaigns.
                </div>
            </div>
            <div class="flex gap-3">
                <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <div>
                    <strong class="text-white">Automatic Integration:</strong> Once connected, the pixels will be automatically added to all pages of your website.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    const colors = {
        success: 'bg-green-500/10 border-green-500/30 text-green-400',
        error: 'bg-red-500/10 border-red-500/30 text-red-400'
    };
    
    const notification = document.createElement('div');
    notification.className = `mb-6 ${colors[type]} border rounded-lg px-4 py-3 flex items-center gap-2`;
    notification.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
        </svg>
        <span>${message}</span>
    `;
    
    container.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

document.getElementById('facebook-pixel-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('{{ route("app.pixel-connect.facebook.save") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                pixel_id: formData.get('pixel_id'),
                name: formData.get('name')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.error || 'Failed to add Facebook Pixel', 'error');
        }
    } catch (error) {
        showNotification('An error occurred. Please try again.', 'error');
    }
});

async function updatePixel(pixelId) {
    const name = document.getElementById(`pixel-name-${pixelId}`).value;
    const pixel_id = document.getElementById(`pixel-id-${pixelId}`).value;

    try {
        const response = await fetch(`/app/pixel-connect/facebook/${pixelId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, pixel_id })
        });

        const data = await response.json();
        showNotification(data.success ? data.message : (data.error || 'Update failed'), data.success ? 'success' : 'error');
    } catch (error) {
        showNotification('An error occurred. Please try again.', 'error');
    }
}

async function togglePixel(pixelId) {
    try {
        const response = await fetch(`/app/pixel-connect/facebook/${pixelId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            const button = document.getElementById(`pixel-toggle-${pixelId}`);
            if (button) {
                button.textContent = data.is_enabled ? 'Enabled' : 'Disabled';
                button.className = data.is_enabled
                    ? 'px-3 py-2 rounded-lg text-sm font-medium transition bg-green-500/20 text-green-400'
                    : 'px-3 py-2 rounded-lg text-sm font-medium transition bg-gray-500/20 text-gray-400';
            }
            showNotification(data.message, 'success');
        } else {
            showNotification(data.error || 'Toggle failed', 'error');
        }
    } catch (error) {
        showNotification('An error occurred. Please try again.', 'error');
    }
}

async function deletePixel(pixelId) {
    if (!confirm('Delete this Facebook Pixel?')) return;

    try {
        const response = await fetch(`/app/pixel-connect/facebook/${pixelId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (data.success) {
            showNotification(data.message, 'success');
            document.getElementById(`pixel-row-${pixelId}`)?.remove();
        } else {
            showNotification(data.error || 'Delete failed', 'error');
        }
    } catch (error) {
        showNotification('An error occurred. Please try again.', 'error');
    }
}

document.getElementById('tiktok-pixel-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('{{ route("app.pixel-connect.tiktok.save") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                tiktok_pixel_id: formData.get('tiktok_pixel_id')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(data.error || 'Failed to connect TikTok Pixel', 'error');
        }
    } catch (error) {
        showNotification('An error occurred. Please try again.', 'error');
    }
});

async function disconnectTikTokPixel() {
    if (!confirm('Are you sure you want to disconnect TikTok Pixel?')) return;
    
    try {
        const response = await fetch('{{ route("app.pixel-connect.tiktok.disconnect") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(data.error || 'Failed to disconnect TikTok Pixel', 'error');
        }
    } catch (error) {
        showNotification('An error occurred. Please try again.', 'error');
    }
}
</script>
@endsection
