<?php

namespace Jetlabs\Zora;

use JsonSerializable;

interface ZoraInterface extends JsonSerializable
{
	/**
	 * Make the class json serialized.
	 *
	 * @return array
	 */
	public function jsonSerialize(): array;

	/**
	 * Convert this Zora instance to an array.
	 */
	public function toArray(): array;

	/**
	 * Static function to clear the cache.
	 *
	 * @return void
	 */
	public static function clearTranslations(): void;

	/**
	 * Loop through lang directory and get all locales
	 * that we need to process for the app.
	 *
	 * @return array
	 */
	public function makeLocales(): array;

	/**
	 * Convert this Zora instance to JSON.
	 */
	public function toJson(int $options = 0): string;
}
