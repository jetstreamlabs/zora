<?php

namespace Serenity\Zora;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CommandTranslationGenerator extends Command
{
    protected $signature = 'zora:generate {path=./resources/js/plugins/translations.js}';

    protected $description = 'Generate translation js file for including in build process';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $path = $this->argument('path');

        $translations = $this->generate();

        $this->makeDirectory($path);

        $this->files->put($path, $translations);

        $this->info('Translations file generated.');
    }

    public function generate()
    {
        $locales = [];

        $iterator = new \DirectoryIterator(resource_path('lang'));

        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $locales[] = $fileinfo->getFilename();
            }
        }

        $json = TranslationPayload::compile($locales)->toJson();

        return <<<EOT
    var Zora = {
        translations: $json,
    };

    if (typeof window.Zora !== 'undefined') {
        window.Zora.translations.forEach((index, trans) => {
            Zora.translations[index] = trans
        })
    }

    export {
        Zora
    }

EOT;
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}
