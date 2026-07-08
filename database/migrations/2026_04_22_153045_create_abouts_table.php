<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('name_ar')->nullable();
            $table->text('description_ar')->nullable();

            $table->text('facebook_meta')->nullable();
            $table->text('google_meta')->nullable();
            $table->text('tiktok_meta')->nullable();
            $table->text('you_tube_meta')->nullable();

            $table->string('facebook_count')->nullable();
            $table->string('google_count')->nullable();
            $table->string('tiktok_count')->nullable();
            $table->string('you_tube_count')->nullable();

            // Site Info
            $table->string('site_name')->nullable();
            $table->string('site_title')->nullable();
            $table->text('site_description')->nullable();
            $table->string('site_url')->nullable();
            $table->text('site_keywords')->nullable();
            $table->string('default_language')->nullable();

            // Favicon
            $table->string('favicon')->nullable();
            $table->string('favicon_svg')->nullable();
            $table->string('favicon_32')->nullable();
            $table->string('favicon_16')->nullable();
            $table->string('favicon_apple')->nullable();
            $table->string('favicon_android')->nullable();
            $table->string('favicon_ms')->nullable();
            $table->string('manifest_json')->nullable();
            $table->string('browserconfig_xml')->nullable();

            // SEO
            $table->string('seo_title')->nullable();
            $table->string('google_site_verification')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            // Open Graph
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_image_width')->nullable();
            $table->string('og_image_height')->nullable();
            $table->string('og_type')->nullable();
            $table->string('og_url')->nullable();
            $table->string('og_site_name')->nullable();

            // Geo Tags
            $table->string('geo_region')->nullable();
            $table->string('geo_placename')->nullable();
            $table->string('geo_position')->nullable();
            $table->string('geo_icbm')->nullable();

            // Canonical & Language
            $table->string('canonical_url')->nullable();
            $table->string('language')->nullable();

            // Twitter
            $table->string('twitter_card')->nullable();

            // Social Media
            $table->string('facebook_app_id')->nullable();
            $table->string('facebook_page')->nullable();
            $table->string('twitter_username')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('linkedin_url')->nullable();

            // Analytics
            $table->string('google_analytics_id')->nullable();
            $table->string('google_tag_manager_id')->nullable();
            $table->string('facebook_pixel_id')->nullable();
            $table->string('clarity_id')->nullable();

            $table->boolean('active')->default(1);

            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
