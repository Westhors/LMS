<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'name_ar' => $this->name_ar,
            'description_ar' => $this->description_ar,

            'facebook_meta' => $this->facebook_meta,
            'google_meta' => $this->google_meta,
            'tiktok_meta' => $this->tiktok_meta,
            'you_tube_meta' => $this->you_tube_meta,

            'facebook_count' => $this->facebook_count,
            'google_count' => $this->google_count,
            'tiktok_count' => $this->tiktok_count,
            'you_tube_count' => $this->you_tube_count,
            'seo_setting' => [
                        'site_name' => $this->site_name,
                        'site_title' => $this->site_title,
                        'site_description' => $this->site_description,
                        'site_url' => $this->site_url,
                        'site_keywords' => $this->site_keywords,
                        'default_language' => $this->default_language,

                        'favicon' => $this->favicon,
                        'favicon_svg' => $this->favicon_svg,
                        'favicon_32' => $this->favicon_32,
                        'favicon_16' => $this->favicon_16,
                        'favicon_apple' => $this->favicon_apple,
                        'favicon_android' => $this->favicon_android,
                        'favicon_ms' => $this->favicon_ms,
                        'manifest_json' => $this->manifest_json,
                        'browserconfig_xml' => $this->browserconfig_xml,

                        'seo_title' => $this->seo_title,
                        'seo_description' => $this->seo_description,
                        'seo_keywords' => $this->seo_keywords,

                        'og_title' => $this->og_title,
                        'og_description' => $this->og_description,
                        'og_image' => $this->og_image,
                        'og_image_width' => $this->og_image_width,
                        'og_image_height' => $this->og_image_height,
                        'og_type' => $this->og_type,
                        'og_url' => $this->og_url,
                        'og_site_name' => $this->og_site_name,

                        'geo_region' => $this->geo_region,
                        'geo_placename' => $this->geo_placename,
                        'geo_position' => $this->geo_position,
                        'geo_icbm' => $this->geo_icbm,

                        'canonical_url' => $this->canonical_url,
                        'language' => $this->language,

                        'twitter_card' => $this->twitter_card,

                        'facebook_app_id' => $this->facebook_app_id,
                        'facebook_page' => $this->facebook_page,
                        'twitter_username' => $this->twitter_username,
                        'instagram_url' => $this->instagram_url,
                        'youtube_url' => $this->youtube_url,
                        'linkedin_url' => $this->linkedin_url,

                        'google_analytics_id' => $this->google_analytics_id,
                        'google_tag_manager_id' => $this->google_tag_manager_id,
                        'facebook_pixel_id' => $this->facebook_pixel_id,
                        'clarity_id' => $this->clarity_id,
                    ],


            'active' => $this->active,
            'teacher_id' => $this->teacher_id,

            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),

            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
