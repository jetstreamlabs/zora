<?php

namespace Serenity\Zora;

use Illuminate\Support\ServiceProvider;
use Serenity\Zora\CommandTranslationGenerator;

class ZoraServiceProvider extends Serviceprovider
{
	public function boot()
	{
		if ($this->app->runningInConsole()) {
            $this->commands([
                CommandTranslationGenerator::class,
            ]);
        }
	}
}
