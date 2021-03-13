<?php

namespace Serenity\Zora;

use Illuminate\Support\Facades\File;

class TranslationPayload
{
    /**
     * Compile all of the local translations.
     *
     * @param  array  $locales
     * @return array
     */
    public static function compile($locales = [])
    {
        $payload = new static;

        $translations = collect();

        foreach ($locales as $locale) { // suported locales
            $translations[$locale] = [
                'php'  => $payload->phpTranslations($locale),
                'json' => $payload->jsonTranslations($locale),
            ];
        }

        return $translations;
    }

    /**
     * Compile the PHP file translations.
     *
     * @param  string $locale
     * @return array
     */
    private function phpTranslations($locale)
    {
        $path = resource_path("lang/$locale");

        return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
            $key = ($translation = $file->getBasename('.php'));

            return [$key => trans($translation, [], $locale)];
        });
    }

    /**
     * Compile the JSON file translations.
     *
     * @param  string $locale
     * @return array
     */
    private function jsonTranslations($locale)
    {
        $path = resource_path("lang/$locale.json");

        if (is_string($path) && is_readable($path)) {
            return json_decode(file_get_contents($path), true);
        }

        return [];
    }
}
