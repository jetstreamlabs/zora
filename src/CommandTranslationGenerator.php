<?php

namespace Jetlabs\Zora;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class CommandTranslationGenerator extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'zora:generate {path=./resources/js/zora.js}';

  /**
   * The console command description.
   *
   * @var string|null
   */
  protected $description = 'Generate translation js file for including in build process';

  /**
   * Filesystem instance for moving files.
   *
   * @var \Illuminate\Filesystem\Filesystem
   */
  protected $files;

  /**
   * Create a new console command instance.
   *
   * @return void
   */
  public function __construct(Filesystem $files)
  {
    parent::__construct();

    $this->files = $files;
  }

  /**
   * Process the command.
   *
   * @return void
   */
  public function handle()
  {
    $path = $this->argument('path');

    $translations = $this->generate();

    $this->makeDirectory($path);

    $this->files->put($path, $translations);

    $this->info('Translations file generated.');
  }

  /**
   * Generate the translations for the file.
   *
   * @return file
   */
  public function generate()
  {
    $locales = [];

    $locales = [];

    $directories = File::directories(lang_path());

    foreach ($directories as $dir) {
      $path = str_replace(lang_path().DIRECTORY_SEPARATOR, '', $dir);
      $locales[] = $path;
    }

    $json = TranslationPayload::compile($locales)->toJson();

    return <<<EOT
const Zora = { translations: $json }

if (typeof window !== 'undefined' && typeof window.Zora !== 'undefined') {
  Object.assign(Zora.routes, window.Zora.routes);
}

export { Zora }

EOT;
  }

  /**
   * Make the directory if it doesn't exist.
   *
   * @param  string  $path
   * @return string
   */
  protected function makeDirectory($path)
  {
    if (! $this->files->isDirectory(dirname($path))) {
      $this->files->makeDirectory(dirname($path), 0777, true, true);
    }

    return $path;
  }
}
