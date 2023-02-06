<?php

namespace Jetlabs\Zora;

use Illuminate\Support\Facades\File;

class Zora implements ZoraInterface
{
  /**
   * Local cache property.
   *
   * @var array
   */
  protected static array $cache = [];

  /**
   * Locales property array.
   *
   * @var array
   */
  protected array $locales = [];

  /**
   * Local translations array.
   *
   * @var array
   */
  protected array $translations = [];

  /**
   * Create a new instance of the class.
   */
  public function __construct()
  {
    $this->locales = $this->makeLocales();

    if (app()->environment('production')) {
      return $this->runProduction();
    }

    $this->translations = $this->makeTranslations();

    return $this;
  }

  /**
   * Run the class with caching in production.
   *
   * @return \Jetlabs\Zora\Zora
   */
  protected function runProduction(): Zora
  {
    if (! static::$cache) {
      static::$cache = $this->makeTranslations();
    }

    $this->translations = static::$cache;

    return $this;
  }

  /**
   * Loop through lang directory and get all locales
   * that we need to process for the app.
   *
   * @return array
   */
  public function makeLocales(): array
  {
    $locales = [];

    $directories = File::directories(lang_path());

    foreach ($directories as $dir) {
      $path = str_replace(lang_path().'/', '', $dir);
      $locales[] = $path;
    }

    return $locales;
  }

  /**
   * Static function to clear the cache.
   *
   * @return void
   */
  public static function clearTranslations(): void
  {
    static::$cache = [];
  }

  /**
   * Build our translations.
   *
   * @return array
   */
  protected function makeTranslations(): array
  {
    $translations = [];

    foreach ($this->locales as $locale) {
      $translations[$locale] = [
        'php' => $this->translatePhp($locale),
        'json' => $this->translateJson($locale),
      ];
    }

    return $translations;
  }

  /**
   * Rollup the PHP language vars.
   *
   * @param  string  $locale
   * @return array
   */
  protected function translatePhp(string $locale): array
  {
    $path = lang_path($locale);

    return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
      $key = ($translation = $file->getBasename('.php'));

      return [$key => trans($translation, [], $locale)];
    })->toArray();
  }

  /**
   * Rollup the JSON language vars.
   *
   * @param  string  $locale
   * @return array
   */
  protected function translateJson(string $locale): array
  {
    $path = lang_path("$locale.json");

    if (is_string($path) && is_readable($path)) {
      return json_decode(file_get_contents($path), true);
    }

    return [];
  }

  /**
   * Convert this Zora instance to an array.
   */
  public function toArray(): array
  {
    return [
      'translations' => $this->translations,
    ];
  }

  /**
   * Convert this Zora instance into something JSON serializable.
   */
  public function jsonSerialize(): array
  {
    return array_merge($translations = $this->toArray(), [
      'defaults' => (object) $translations['translations'],
    ]);
  }

  /**
   * Convert this Zora instance to JSON.
   */
  public function toJson(int $options = 0): string
  {
    return json_encode($this->jsonSerialize(), $options);
  }
}
