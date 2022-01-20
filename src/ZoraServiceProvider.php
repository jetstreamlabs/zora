<?php

namespace Jetlabs\Zora;

use Illuminate\Support\ServiceProvider;

class ZoraServiceProvider extends Serviceprovider
{
	/**
	 * Boot up our service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				CommandTranslationGenerator::class,
			]);
		}
	}
}
