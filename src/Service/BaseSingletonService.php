<?php

namespace Up\Service;

abstract class BaseSingletonService
{
	private static ?BaseSingletonService $instance = null;

	public static function getInstance(): BaseSingletonService
	{
		$class = static::class;
		if (static::$instance)
		{
			return static::$instance;
		}

		static::$instance = new $class();

		return static::$instance;
	}
}