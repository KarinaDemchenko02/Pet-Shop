<?php

namespace Up\Util;

class Session
{
	public static function init(): void
	{
		session_start();
	}

	public static function set($key, $value): void
	{
		$_SESSION[$key] = $value;
	}

	public static function get($key): mixed
	{
		return $_SESSION[$key] ?? null;
	}

	public static function delete()
	{
		unset($_SESSION);
		session_destroy();
	}
}
