@extends('layouts.customer')

@section('title', 'Website Customization')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Website Customization</h1>
            <p class="text-gray-400 mt-2">Customize your storefront appearance and content</p>
        </div>
        <a href="{{ route('app.website-preview') }}" target="_blank" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
            <span class="material-icons text-sm">visibility</span>
            Preview Site
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <p class="font-semibold flex items-center gap-2">
            <span class="material-icons">error</span>
            Please fix the following errors:
        </p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('app.website-customization.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Site Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-blue-600">info</span>
                Site Information
            </h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Name *</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Logo</label>
                    <input type="file" name="site_logo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @if($settings->site_logo)
                    <img src="/storage/{{ $settings->site_logo }}" alt="Logo" class="mt-2 h-16 object-contain">
                    @endif
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                    <textarea name="site_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('site_description', $settings->site_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Top Banner -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-orange-600">campaign</span>
                Top Banner
            </h2>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="show_top_banner" value="1" {{ old('show_top_banner', $settings->show_top_banner) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                    <label class="text-sm font-medium text-gray-700">Show Top Banner</label>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Text</label>
                        <input type="text" name="banner_text" value="{{ old('banner_text', $settings->banner_text) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Icon (Material Icons)</label>
                        <input type="text" name="banner_icon" value="{{ old('banner_icon', $settings->banner_icon) }}" placeholder="local_fire_department" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Visit <a href="https://fonts.google.com/icons" target="_blank" class="text-blue-600 hover:underline">Material Icons</a></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Background Color</label>
                        <input type="color" name="banner_bg_color" value="{{ old('banner_bg_color', $settings->banner_bg_color) }}" class="w-full h-12 px-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-purple-600">auto_awesome</span>
                Hero Section
            </h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                    <input type="text" name="hero_button_text" value="{{ old('hero_button_text', $settings->hero_button_text) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Link</label>
                    <input type="text" name="hero_button_link" value="{{ old('hero_button_link', $settings->hero_button_link) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Background Image</label>
                    <input type="file" name="hero_background_image" id="hero_background_image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="previewHeroImage(event)">
                    
                    <!-- Image Preview Container -->
                    <div id="hero_image_preview_container" class="mt-3 {{ $settings->hero_background_image ? '' : 'hidden' }}">
                        <p class="text-xs text-gray-600 mb-2">Current background image:</p>
                        <img id="hero_image_preview" src="{{ $settings->hero_background_image ? url('storage/' . $settings->hero_background_image) : '' }}" alt="Hero Background" class="w-full max-w-md h-48 object-cover rounded-lg border border-gray-300">
                        <label class="flex items-center gap-2 mt-2">
                            <input type="checkbox" name="remove_hero_background_image" value="1" class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500" onchange="toggleRemoveImage(this)">
                            <span class="text-sm text-red-600">Remove current background image</span>
                        </label>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-1">Upload an image to use as hero section background (recommended size: 1920x1080px)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
                    <input type="color" name="hero_background_color" value="{{ old('hero_background_color', $settings->hero_background_color) }}" class="w-full h-12 px-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Used when no image is uploaded</p>
                </div>
            </div>
        </div>

        <!-- Colors & Theme -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-pink-600">palette</span>
                Colors & Theme
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                    <input type="color" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}" class="w-full h-12 px-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Used for buttons and links</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                    <input type="color" name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color) }}" class="w-full h-12 px-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Used for accents</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                    <input type="color" name="accent_color" value="{{ old('accent_color', $settings->accent_color) }}" class="w-full h-12 px-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Used for highlights</p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-green-600">contact_phone</span>
                Contact Information
            </h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="contact_address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('contact_address', $settings->contact_address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- WhatsApp Integration -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                WhatsApp Integration
            </h2>
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-900 mb-1">Enable WhatsApp Chat for Your Website</p>
                            <p class="text-xs text-green-700">Add your WhatsApp number below to enable direct customer chat. A floating WhatsApp button will appear on your website, and customers can contact you with one click.</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        WhatsApp Number
                        <span class="text-xs text-gray-500 font-normal ml-2">(Include country code, e.g., +212661360879)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </div>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="+212661360879" class="w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-green-500 focus:border-transparent focus:outline-none">
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <strong>Examples:</strong> +212661360879 (Morocco), +1234567890 (USA), +447700900000 (UK)
                    </p>
                </div>

                @if($settings->whatsapp_number)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900 mb-2">WhatsApp is Connected!</p>
                            <p class="text-xs text-blue-700 mb-3">Current number: <strong>{{ $settings->whatsapp_number }}</strong></p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->whatsapp_number) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                Test WhatsApp Connection
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700 mb-1">No WhatsApp Number Connected</p>
                            <p class="text-xs text-gray-600">Add your WhatsApp number above and click "Save Changes" to enable WhatsApp chat on your website.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Social Media -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-blue-600">share</span>
                Social Media
            </h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/yourpage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/yourpage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $settings->twitter_url) }}" placeholder="https://twitter.com/yourpage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $settings->youtube_url) }}" placeholder="https://youtube.com/yourchannel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-gray-600">web</span>
                Footer
            </h2>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">About Text</label>
                    <textarea name="footer_about" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('footer_about', $settings->footer_about) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Copyright Text</label>
                    <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $settings->footer_copyright) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Footer Features/Badges -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-yellow-600">stars</span>
                Footer Features/Badges
            </h2>
            <div id="features-container" class="space-y-4">
                @foreach(old('features', $settings->features ?? []) as $index => $feature)
                <div class="feature-row grid md:grid-cols-4 gap-4 items-end p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                        <input type="text" name="features[{{ $index }}][icon]" value="{{ $feature['icon'] ?? '' }}" placeholder="local_shipping" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" name="features[{{ $index }}][title]" value="{{ $feature['title'] ?? '' }}" placeholder="Free Delivery" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                        <input type="color" name="features[{{ $index }}][color]" value="{{ $feature['color'] ?? '#10b981' }}" class="w-full h-10 px-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <button type="button" onclick="this.closest('.feature-row').remove()" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Remove</button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addFeature()" class="mt-4 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                <span class="material-icons text-sm">add</span>
                Add Feature
            </button>
        </div>

        <!-- SEO -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="material-icons text-indigo-600">search</span>
                SEO Settings
            </h2>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Brief description of your store for search engines">{{ old('meta_description', $settings->meta_description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $settings->meta_keywords) }}" placeholder="online store, products, Morocco, shopping" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Separate keywords with commas</p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end gap-4 sticky bottom-0 bg-white p-4 rounded-xl shadow-lg border border-gray-200">
            <a href="{{ route('app.dashboard') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition inline-flex items-center gap-2">
                <span class="material-icons text-sm">save</span>
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
let featureIndex = {{ count(old('features', $settings->features ?? [])) }};

function addFeature() {
    const container = document.getElementById('features-container');
    const html = `
        <div class="feature-row grid md:grid-cols-4 gap-4 items-end p-4 bg-gray-50 rounded-lg">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                <input type="text" name="features[${featureIndex}][icon]" placeholder="local_shipping" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="features[${featureIndex}][title]" placeholder="Free Delivery" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                <input type="color" name="features[${featureIndex}][color]" value="#10b981" class="w-full h-10 px-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <button type="button" onclick="this.closest('.feature-row').remove()" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Remove</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    featureIndex++;
}

// Live preview for hero background image
function previewHeroImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('hero_image_preview');
    const container = document.getElementById('hero_image_preview_container');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

// Handle remove image checkbox
function toggleRemoveImage(checkbox) {
    const fileInput = document.getElementById('hero_background_image');
    const preview = document.getElementById('hero_image_preview');
    const container = document.getElementById('hero_image_preview_container');
    
    if (checkbox.checked) {
        fileInput.disabled = true;
        preview.style.opacity = '0.3';
    } else {
        fileInput.disabled = false;
        preview.style.opacity = '1';
    }
}
</script>
@endsection
