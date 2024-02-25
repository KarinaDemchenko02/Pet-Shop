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

	public static function validateToken(string $jwt): bool
	{
		if ($jwt === '')
		{
			return false;
		}

		$configuration = Configuration::getInstance();
		$secret = $configuration->option('JWT_SECRET');
		$alg = $configuration->option('JWT_ALG');
		try
		{
			$decodedToken = JWT::decode($jwt, new Key($secret, $alg));
			return true;
		}
		catch (\Exception)
		{
			return false;
		}
	}

}
