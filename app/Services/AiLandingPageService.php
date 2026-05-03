<?php

namespace App\Services;

use App\Models\AiApiSetting;
use App\Models\Product;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiLandingPageService
{
    protected $user;
    protected $aiSetting;

    public function __construct($user)
    {
        $this->user = $user;
        $this->aiSetting = AiApiSetting::where('user_id', $user->id)->first();
    }

    public function generateLandingPage(Product $product, array $languages = ['ar'])
    {
        if (!$this->aiSetting) {
            throw new \Exception('AI API settings not configured. Please configure your AI settings first.');
        }

        $categoryName = $product->category ? $product->category->name : 'General';

        $results = [];
        foreach ($languages as $language) {
            $prompt = $this->buildPrompt($product, $categoryName, $language);

            if (!empty($this->aiSetting->openai_api_key_encrypted)) {
                $results[$language] = $this->generateWithOpenAI($prompt);
            } elseif (!empty($this->aiSetting->anthropic_api_key_encrypted)) {
                $results[$language] = $this->generateWithAnthropic($prompt);
            } else {
                throw new \Exception('No AI API key configured. Please add an OpenAI or Anthropic API key.');
            }
        }

        return $results;
    }

    protected function buildPrompt(Product $product, string $categoryName, string $language = 'fr'): string
    {
        $languageInstructions = [
            'fr' => 'Generate all content in French (Français)',
            'en' => 'Generate all content in English',
            'ar' => 'Generate all content in Arabic (العربية)'
        ];

        $instruction = $languageInstructions[$language] ?? $languageInstructions['fr'];

        // Count product images (excluding the first/main image)
        $images = $product->images ?? [];
        $imageSectionCount = max(0, count($images) - 1);
        
        $imageSectionsPrompt = '';
        if ($imageSectionCount > 0) {
            $imageSectionsPrompt = ",
    \"image_sections\": [
        " . implode(",\n        ", array_fill(0, $imageSectionCount, "{\"title\": \"Catchy benefit title (5-8 words)\", \"description\": \"2-3 sentences describing this specific benefit/feature of the product\"}")) . "
    ]";
            
            $imageSectionsInstructions = "

IMPORTANT - IMAGE SECTIONS:
- This product has {$imageSectionCount} feature images that will be displayed on the landing page
- Generate EXACTLY {$imageSectionCount} image_sections, one for each image
- Each section should highlight a DIFFERENT benefit, feature, or use case
- Think of these like the sections you see on professional landing pages where each image shows a different product advantage
- Examples: \"Effective sun protection\", \"Easy to install\", \"Premium quality materials\", \"Perfect for families\"
- Make each title unique and each description persuasive and specific";
        } else {
            $imageSectionsInstructions = '';
        }

        return "You are a professional marketing copywriter and landing page designer. {$instruction}.

Create compelling landing page content for the following product:

Product Name: {$product->name}
Category: {$categoryName}
Price: {$product->price} DHS
" . ($product->compare_at_price ? "Original Price: {$product->compare_at_price} DHS\n" : "") . "
Description: {$product->description}

Generate a professional, conversion-optimized landing page in JSON format with these fields:

{
    \"hero_description\": \"Write 2-3 compelling sentences highlighting the main benefits\",
    \"features\": [
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"✓\"},
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"⚡\"},
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"🎯\"},
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"💎\"}
    ],
    \"steps\": [
        {\"number\": \"1\", \"title\": \"First step\", \"description\": \"Explain what customer does\"},
        {\"number\": \"2\", \"title\": \"Second step\", \"description\": \"Explain what customer does\"},
        {\"number\": \"3\", \"title\": \"Third step\", \"description\": \"Explain what customer does\"}
    ],
    \"steps_title\": \"Section heading for the steps\",
    \"testimonials\": [
        {\"name\": \"Ahmed\", \"text\": \"J'ai commandé ce produit il y a deux semaines et je suis vraiment impressionné par la qualité. Le service client était excellent et la livraison rapide. Je recommande vivement!\", \"rating\": 5},
        {\"name\": \"Fatima\", \"text\": \"Exactement ce que je recherchais! Le rapport qualité-prix est imbattable. Mes amies m'ont déjà demandé où je l'ai acheté. Très satisfaite de mon achat!\", \"rating\": 5},
        {\"name\": \"Hassan\", \"text\": \"Produit conforme à la description. L'équipe a été très professionnelle du début à la fin. Je commanderai à nouveau sans hésiter. Merci beaucoup!\", \"rating\": 5}
    ],
    \"testimonials_title\": \"Section heading for testimonials\",
    \"faqs\": [
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"},
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"},
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"},
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"}
    ],
    \"faqs_title\": \"Section heading for FAQs\",
    \"cta\": \"Action button text\",
    \"full_description\": \"Write 3-4 detailed persuasive paragraphs about the product\",
    \"form_title\": \"Contact form heading\",
    \"form_subtitle\": \"Contact form subheading\",
    \"form_name_placeholder\": \"Name input placeholder\",
    \"form_phone_placeholder\": \"Phone input placeholder\",
    \"form_note_placeholder\": \"Note input placeholder\",
    \"form_submit_button\": \"Submit button text\"{$imageSectionsPrompt}
}

CRITICAL INSTRUCTIONS FOR TESTIMONIALS:
- The testimonials array shows examples of REAL customer reviews in French
- You MUST write similar AUTHENTIC, DETAILED testimonials for this specific product
- Each testimonial should be 2-4 sentences describing actual product experience
- Use Moroccan names: Ahmed, Fatima, Hassan, Salma, Youssef, Aisha, Karim, Nadia, Omar, Zineb
- Mention specific aspects of the product, delivery, quality, or customer service
- Write as if you are real customers sharing their genuine experience
- DO NOT use generic phrases like \"testimonial text\", \"great product\", or placeholders
- Each testimonial must be unique and believable{$imageSectionsInstructions}

Other requirements:
- Make content specific to {$categoryName} category and this exact product
- Focus on real benefits, not just features
- Use persuasive, action-oriented language
- All FAQs should address real concerns about buying/ordering
- Return ONLY valid JSON, no markdown or extra text
- All content must be in {$instruction}";
    }

    protected function generateWithOpenAI(string $prompt): array
    {
        try {
            $apiKey = Crypt::decryptString($this->aiSetting->openai_api_key_encrypted);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to decrypt OpenAI API key.');
        }

        $model = $this->aiSetting->openai_model ?: 'gpt-4o-mini';

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional marketing copywriter. Always respond with valid JSON only, no markdown or additional text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            Log::error('OpenAI API Error', ['error' => $error, 'status' => $response->status()]);
            throw new \Exception('OpenAI API request failed: ' . $error);
        }

        $content = $response->json('choices.0.message.content');
        
        return $this->parseAiResponse($content);
    }

    protected function generateWithAnthropic(string $prompt): array
    {
        try {
            $apiKey = Crypt::decryptString($this->aiSetting->anthropic_api_key_encrypted);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to decrypt Anthropic API key.');
        }

        $model = $this->aiSetting->anthropic_model ?: 'claude-3-5-sonnet-20241022';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(60)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => 2000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            Log::error('Anthropic API Error', ['error' => $error, 'status' => $response->status()]);
            throw new \Exception('Anthropic API request failed: ' . $error);
        }

        $content = $response->json('content.0.text');
        
        return $this->parseAiResponse($content);
    }

    protected function parseAiResponse(string $content): array
    {
        $content = trim($content);
        
        $content = preg_replace('/^```json\s*/s', '', $content);
        $content = preg_replace('/\s*```$/s', '', $content);
        $content = trim($content);

        try {
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
            }

            if (!isset($data['features']) || !isset($data['full_description'])) {
                throw new \Exception('AI response missing required fields.');
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('Failed to parse AI response', ['content' => $content, 'error' => $e->getMessage()]);
            throw new \Exception('Failed to parse AI response: ' . $e->getMessage());
        }
    }

    public function saveLandingPageToProduct(Product $product, array $landingPageData): void
    {
        $updateData = [];

        // Save Arabic as the only language
        if (isset($landingPageData['ar'])) {
            $updateData['landing_page_ar'] = $landingPageData['ar'];
            
            // Save Arabic as default (no hero_title - use product name instead)
            $updateData['landing_page_hero_description'] = $landingPageData['ar']['hero_description'] ?? null;
            $updateData['landing_page_features'] = $landingPageData['ar']['features'] ?? [];
            $updateData['landing_page_cta'] = $landingPageData['ar']['cta'] ?? null;
            $updateData['landing_page_content'] = $landingPageData['ar']['full_description'] ?? null;
        }

        // Generate landing page sections from product images (excluding the first/main image)
        $landingSections = $this->generateLandingSectionsFromImages($product, $landingPageData);
        
        // Force update the landing_page_sections field
        $updateData['landing_page_sections'] = $landingSections;

        $product->update($updateData);
        
        Log::info('Landing page data saved for product: ' . $product->id, [
            'sections_count' => count($landingSections)
        ]);
    }

    /**
     * Generate landing page sections from product images with AI-generated descriptions
     */
    protected function generateLandingSectionsFromImages(Product $product, array $landingPageData): array
    {
        $images = $product->images ?? [];
        
        // Skip if no images or only 1 image (which is the main/hero image)
        if (count($images) <= 1) {
            return [];
        }

        // Get AI-generated image sections from Arabic data only
        $imageSectionsAr = $landingPageData['ar']['image_sections'] ?? $landingPageData['image_sections'] ?? null;

        $sections = [];
        
        // Skip the first image (it's the hero/main image), use remaining images
        $sectionImages = array_slice($images, 1);
        
        foreach ($sectionImages as $index => $imagePath) {
            $section = [
                'image' => $imagePath,
                'title_ar' => $imageSectionsAr[$index]['title'] ?? '',
                'description_ar' => $imageSectionsAr[$index]['description'] ?? '',
            ];
            
            // If AI didn't provide enough sections, use generic ones based on product info
            if (empty($section['title_ar'])) {
                $section['title_ar'] = "تفاصيل المنتج " . ($index + 1);
                $section['description_ar'] = "اكتشف الجودة الاستثنائية لمنتجنا " . $product->name . ".";
            }
            
            $sections[] = $section;
        }

        return $sections;
    }

    /**
     * Generate image section descriptions using AI
     */
    public function generateImageSections(Product $product, array $languages = ['ar']): array
    {
        if (!$this->aiSetting) {
            throw new \Exception('AI API settings not configured.');
        }

        $images = $product->images ?? [];
        
        // Skip if no images or only 1 image
        if (count($images) <= 1) {
            return [];
        }

        $imageCount = count($images) - 1; // Exclude first/main image
        $categoryName = $product->category ? $product->category->name : 'General';

        $results = [];
        foreach ($languages as $language) {
            $prompt = $this->buildImageSectionsPrompt($product, $categoryName, $imageCount, $language);

            if (!empty($this->aiSetting->openai_api_key_encrypted)) {
                $results[$language] = $this->generateWithOpenAI($prompt);
            } elseif (!empty($this->aiSetting->anthropic_api_key_encrypted)) {
                $results[$language] = $this->generateWithAnthropic($prompt);
            }
        }

        return $results;
    }

    protected function buildImageSectionsPrompt(Product $product, string $categoryName, int $imageCount, string $language = 'fr'): string
    {
        $languageInstructions = [
            'fr' => 'Generate all content in French (Français)',
            'en' => 'Generate all content in English',
            'ar' => 'Generate all content in Arabic (العربية)'
        ];

        $instruction = $languageInstructions[$language] ?? $languageInstructions['fr'];

        return "You are a professional marketing copywriter. {$instruction}.

Create compelling section titles and descriptions for product images on a landing page.

Product Name: {$product->name}
Category: {$categoryName}
Description: {$product->description}

This product has {$imageCount} feature images (excluding the main hero image).
Generate a title and description for EACH of the {$imageCount} images.

The sections should highlight different aspects/benefits/uses of the product, similar to how professional landing pages show:
- Product benefits
- Key features
- Use cases
- Quality highlights

Generate JSON with this exact format:
{
    \"image_sections\": [
        {\"title\": \"Short catchy title (5-8 words)\", \"description\": \"Detailed description of this feature/benefit (2-3 sentences)\"},
        {\"title\": \"Short catchy title (5-8 words)\", \"description\": \"Detailed description of this feature/benefit (2-3 sentences)\"}
    ]
}

Requirements:
- Generate EXACTLY {$imageCount} sections (one for each image)
- Each title should be unique and highlight a different benefit
- Descriptions should be persuasive and specific to the product
- Focus on benefits, not just features
- Make content specific to {$categoryName} category
- Return ONLY valid JSON, no markdown or extra text";
    }
}
