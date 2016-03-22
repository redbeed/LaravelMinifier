<?php namespace Redbeed\LaravelMinifier;

use Illuminate\Support\Facades\Facade;

class LaravelMinifierFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'laravel-minifyer'; }

}