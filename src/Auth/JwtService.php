<?php

namespace Up\Auth;
use \Firebase\JWT\JWT;

use Firebase\JWT\Key;
use Up\Util\Configuration;

class JwtService
{
	public static function generateToken(array $data): string
	{
		$configuration = Configuration::getInstance();
		$exp = time() + $configuration->option('JWT_EXP_ACCESS');
		$secret = $configuration->option('JWT_SECRET');
		$alg = $configuration->option('JWT_ALG');


		$token = [
			'exp' => $exp,
			'data' => $data,
		];

		return JWT::encode($token, $secret, $alg);
	}

	public static function validateToken(string $jwt): array
	{
		if ($jwt === '')
		{
			return [];
		}
		$configuration = Configuration::getInstance();
		$secret = $configuration->option('JWT_SECRET');
		$alg = $configuration->option('JWT_ALG');
		try
		{
			$decoded = JWT::decode($jwt, new Key($secret, $alg));
			return self::getPayload($decoded);
		}
		catch (\Exception)
		{
			return [];
		}
	}

	private static function getPayload(\stdClass $decoded): array
	{
		return json_decode(json_encode($decoded, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
	}

	public static function saveTokenInCookie(string $jwt/*, TokenType $tokenType*/): bool
	{
		$configuration = Configuration::getInstance();
		$cookieOptions = [
			'expires' => time() + $configuration->option('JWT_EXP_REFRESH'),
			'path' => '/',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Strict'
		];
		return setcookie('jwt', $jwt, $cookieOptions);
	}
	public static function deleteCookie(string $name/*, TokenType $tokenType*/): bool
	{
		return setcookie($name, '', time() - 1, '/');
	}
}
