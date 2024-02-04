<?php

namespace Up\Util;

abstract class Singleton
{
	private static array $instances = [];

	final private function __construct($params)
	{
		$class = static::class;
		if (array_key_exists($class, self::$instances)) {
			throw new \RuntimeException("An instance of $class already exists");
		}

		$this->initialize($params);
	}

	abstract protected function initialize($params);

	public static function getInstance(...$params): Singleton
	{
		$class = static::class;
		if (array_key_exists($class, self::$instances)) {
			return static::$instances[$class];
		}

		static::$instances[$class] = new $class($params);

		return static::$instances[$class];
	}
}
