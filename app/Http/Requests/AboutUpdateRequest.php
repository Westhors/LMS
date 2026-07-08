<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'name_ar' => 'nullable|string',
            'description_ar' => 'nullable|string',

            'google_meta' => 'nullable|string',
            'facebook_meta' => 'nullable|string',
            'tiktok_meta' => 'nullable|string',
            'you_tube_meta' => 'nullable|string',

            'facebook_count' => 'nullable|string',
            'google_count' => 'nullable|string',
            'tiktok_count' => 'nullable|string',
            'you_tube_count' => 'nullable|string',

            // Site Info
            'site_name' => 'nullable|string',
            'site_title' => 'nullable|string',
            'site_description' => 'nullable|string',
            'site_url' => 'nullable|string',
            'site_keywords' => 'nullable|string',
            'default_language' => 'nullable|string',

            // Favicon
            'favicon' => 'nullable|string',
            'favicon_svg' => 'nullable|string',
            'favicon_32' => 'nullable|string',
            'favicon_16' => 'nullable|string',
            'favicon_apple' => 'nullable|string',
            'favicon_android' => 'nullable|string',
            'favicon_ms' => 'nullable|string',
            'manifest_json' => 'nullable|string',
            'browserconfig_xml' => 'nullable|string',

            // SEO
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'google_site_verification' => 'nullable|string',
            // Open Graph
            'og_title' => 'nullable|string',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|string',
            'og_image_width' => 'nullable|string',
            'og_image_height' => 'nullable|string',
            'og_type' => 'nullable|string',
            'og_url' => 'nullable|string',
            'og_site_name' => 'nullable|string',

            // Geo
            'geo_region' => 'nullable|string',
            'geo_placename' => 'nullable|string',
            'geo_position' => 'nullable|string',
            'geo_icbm' => 'nullable|string',

            // Canonical
            'canonical_url' => 'nullable|string',
            'language' => 'nullable|string',

            // Twitter
            'twitter_card' => 'nullable|string',

            // Social
            'facebook_app_id' => 'nullable|string',
            'facebook_page' => 'nullable|string',
            'twitter_username' => 'nullable|string',
            'instagram_url' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'linkedin_url' => 'nullable|string',

            // Analytics
            'google_analytics_id' => 'nullable|string',
            'google_tag_manager_id' => 'nullable|string',
            'facebook_pixel_id' => 'nullable|string',
            'clarity_id' => 'nullable|string',

            'teacher_id' => 'nullable|exists:teachers,id',
        ];
    }
}
