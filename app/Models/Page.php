<?php

namespace App\Models;

use App\Traits\HasContentTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Language;

class Page extends Model
{
    use HasFactory, HasContentTranslations;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'meta_title',
        'meta_description',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function getImageUrlAttribute()
    {
        return getImageOrPlaceholder($this->image, '1200x600');
    }

    /**
     * About page section meta for a given locale. Default locale uses page.meta;
     * other locales use content_translations (about_* fields) with fallback to page.meta.
     */
    public function getAboutMetaForLocale(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $defaultCode = Language::getDefaultCode();
        $baseMeta = is_array($this->meta) ? $this->meta : [];

        if ($locale === $defaultCode) {
            return $baseMeta;
        }

        if (! $this->relationLoaded('contentTranslations')) {
            $this->load('contentTranslations');
        }

        $trans = $this->contentTranslations
            ->where('language_code', $locale)
            ->pluck('value', 'field')
            ->toArray();

        $aboutKeys = [
            'about_hero_title', 'about_hero_subtitle', 'about_hero_text', 'about_hero_image',
            'about_story_title', 'about_story_subtitle',
            'about_story_1_image', 'about_story_1_heading', 'about_story_1_text',
            'about_story_2_image', 'about_story_2_heading', 'about_story_2_text',
            'about_story_3_image', 'about_story_3_heading', 'about_story_3_text',
            'about_mission_title', 'about_mission_intro',
            'about_mission_1_image', 'about_mission_1_text', 'about_mission_2_image', 'about_mission_2_text',
            'about_testimonial_title', 'about_testimonial_subtitle',
            'about_testimonial_1_avatar', 'about_testimonial_1_name', 'about_testimonial_1_role', 'about_testimonial_1_quote',
            'about_testimonial_2_avatar', 'about_testimonial_2_name', 'about_testimonial_2_role', 'about_testimonial_2_quote',
        ];

        $result = $baseMeta;
        foreach ($aboutKeys as $key) {
            if (array_key_exists($key, $trans)) {
                $result[$key] = $trans[$key];
            }
        }

        return $result;
    }
}
