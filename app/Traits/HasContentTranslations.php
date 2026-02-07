<?php

namespace App\Traits;

use App\Models\ContentTranslation;
use App\Models\Language;

/**
 * Translatable dynamic content via single content_translations table.
 * Use translate($field, $locale) on frontend; default locale uses main columns.
 */
trait HasContentTranslations
{
    /**
     * Content translation records (field-based, per language).
     */
    public function contentTranslations()
    {
        return $this->morphMany(ContentTranslation::class, 'translatable');
    }

    /**
     * Get translated value for a field. Falls back to main column for default locale or when missing.
     *
     * @param  string  $field  Attribute name (e.g. title, content, name, description)
     * @param  string|null  $locale  Language code (default: current app locale)
     * @return string|null
     */
    public function translate(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $defaultCode = Language::getDefaultCode();

        if ($locale === $defaultCode) {
            $value = $this->getAttribute($field);
            return $value === null ? null : (string) $value;
        }

        if (! $this->relationLoaded('contentTranslations')) {
            $this->load('contentTranslations');
        }

        $row = $this->contentTranslations
            ->where('language_code', $locale)
            ->where('field', $field)
            ->first();

        if ($row && $row->value !== null && $row->value !== '') {
            return $row->value;
        }

        $value = $this->getAttribute($field);
        return $value === null ? null : (string) $value;
    }

    /**
     * Save translation inputs from request (e.g. translations[bn][title]).
     * Default locale values must be saved to main columns by the caller.
     *
     * @param  array<string, array<string, mixed>>  $translations  [ 'bn' => ['title' => '...', 'content' => '...'], ... ]
     */
    public function saveContentTranslationsFromInput(array $translations): void
    {
        $defaultCode = Language::getDefaultCode();

        foreach ($translations as $languageCode => $fields) {
            if ($languageCode === $defaultCode || ! is_array($fields)) {
                continue;
            }

            foreach ($fields as $field => $value) {
                if ($field === '' || ! is_string($field)) {
                    continue;
                }
                $this->contentTranslations()->updateOrCreate(
                    [
                        'language_code' => $languageCode,
                        'field' => $field,
                    ],
                    ['value' => $value === null ? '' : (string) $value]
                );
            }
        }
    }

    /**
     * Get translation values for a language (for admin edit form).
     *
     * @return array<string, string>
     */
    public function getTranslationValuesForLocale(string $locale): array
    {
        $defaultCode = Language::getDefaultCode();
        if ($locale === $defaultCode) {
            return [];
        }

        if (! $this->relationLoaded('contentTranslations')) {
            $this->load('contentTranslations');
        }

        return $this->contentTranslations
            ->where('language_code', $locale)
            ->pluck('value', 'field')
            ->toArray();
    }
}
