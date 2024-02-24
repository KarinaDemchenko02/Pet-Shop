<?php

namespace Up\Util;

class Session
{
	public static function init(): void
	{
		if (session_status() === PHP_SESSION_NONE)
		{
			session_start();
		}
	}

	public static function set($key, $value): void
	{
		$_SESSION[$key] = $value;
	}

	public static function unset($key): void
	{
		unset($_SESSION[$key]);
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
