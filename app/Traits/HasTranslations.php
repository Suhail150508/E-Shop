<?php

namespace App\Traits;

trait HasTranslations
{
    /**
     * Get the default locale (from config or first available).
     */
    public static function getDefaultLocale(): string
    {
        return config('app.locale', 'en');
    }

    /**
     * Get available locales for admin translation UI.
     */
    public static function getAvailableLocales(): array
    {
        $locales = config('app.available_locales', ['en']);
        return is_array($locales) ? $locales : ['en'];
    }

    /**
     * Get translated value for an attribute. Uses current app locale, falls back to default locale column.
     */
    public function getTranslation(string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = static::getDefaultLocale();

        if ($locale === $defaultLocale) {
            return $this->getAttribute($attribute);
        }

        $translations = $this->getTranslations();
        $value = $translations[$locale][$attribute] ?? null;

        if ($value !== null && $value !== '') {
            return $value;
        }

        return $this->getAttribute($attribute);
    }

    /**
     * Get all translations array from the translations column.
     */
    public function getTranslations(): array
    {
        $val = $this->getAttribute('translations');
        if (is_string($val)) {
            $decoded = json_decode($val, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($val) ? $val : [];
    }

    /**
     * Set translations from request/data. Merges with existing, then saves.
     * Default locale values should be saved to main columns by the caller.
     */
    public function setTranslationsFromInput(array $translations): void
    {
        $defaultLocale = static::getDefaultLocale();
        $existing = $this->getTranslations();

        foreach ($translations as $locale => $attrs) {
            if ($locale === $defaultLocale || ! is_array($attrs)) {
                continue;
            }
            $existing[$locale] = array_merge($existing[$locale] ?? [], $attrs);
        }

        $this->translations = $existing;
        $this->save();
    }
}
