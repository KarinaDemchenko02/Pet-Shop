<?php

namespace Up\Auth;
use Firebase\JWT\ExpiredException;
use \Firebase\JWT\JWT;

use Firebase\JWT\Key;
use Up\Dto\TokenDto;
use Up\Exceptions\Auth\EmptyToken;
use Up\Exceptions\Auth\InvalidToken;
use Up\Exceptions\Auth\TokenNotUpdated;
use Up\Repository\Token\TokenRepositoryImpl;
use Up\Util\Configuration;

class JwtService
{
	public static function generateToken(TokenType $tokenType, string $email, int $userId, string $role): string
	{
		if ($tokenType === TokenType::ACCESS)
		{
			$exp = time() + self::getConfig('expAccess');
			$token = [
				'exp' => $exp,
				'iat' => time(),
				'sub' => $email,
				'role' => $role,
				'uid' => $userId,
				];
		}
		else
		{
			$exp = time() + self::getConfig('expRefresh');
			$token = [
				'exp' => $exp,
				'iat' => time(),
				'sub' => $email,
				'uid' => $userId,
				'role' => $role,
				'jti' => uniqid('', true),
			];
		}

		/*$token = array_merge($token, $data);*/
		return JWT::encode($token, self::getConfig('secret'), self::getConfig('alg'));
	}

	/**
	 * @throws EmptyToken
	 * @throws ExpiredException
	 * @throws InvalidToken
	 */
	public static function validateToken(string $jwt): bool
	{
		if ($jwt === '')
		{
			throw new EmptyToken();
		}

		try
		{
			JWT::decode($jwt, new Key(self::getConfig('secret'), self::getConfig('alg')));
			return true;
		}
		catch (ExpiredException)
		{
			throw new ExpiredException();
		}
		catch (\Exception)
		{
			throw new InvalidToken();
		}
	}

	private static function getConfig(string $name): mixed
	{
		static $config = null;
		if ($config !== null)
		{
			return $config[$name] ?? null;
		}
		$configuration = Configuration::getInstance();
		$config['secret'] = $configuration->option('JWT_SECRET');
		$config['alg'] = $configuration->option('JWT_ALG');
		$config['expAccess'] = $configuration->option('JWT_EXP_ACCESS');
		$config['expRefresh'] = $configuration->option('JWT_EXP_REFRESH');

		return $config[$name] ?? null;
	}

	public static function getPayload(string $jwt): array
	{
		$decoded = JWT::decode($jwt, new Key(self::getConfig('secret'), self::getConfig('alg')));
		return json_decode(json_encode($decoded, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
	}

	public static function saveTokenInCookie(string $jwt, TokenType $tokenType): bool
	{

		if ($tokenType === TokenType::ACCESS)
		{
			$exp = self::getConfig('expAccess');
		}
		else
		{
			$exp = self::getConfig('expRefresh');
		}


		$cookieOptions = [
			'expires' => time() + $exp,
			'path' => '/',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Strict'
		];
		$cookieName = 'JWT-' . $tokenType->name;
		return setcookie($cookieName, $jwt, $cookieOptions);
	}
	public static function deleteCookie(TokenType $tokenType): bool
	{
		$cookieName = 'JWT-' . $tokenType->name;
		return setcookie($cookieName, '', time() - 1, '/');
	}

	/**
	 * @throws InvalidToken|\JsonException|EmptyToken
	 */
	public static function refreshTokens(string $refreshToken): array
	{

		if (!self::validateToken($refreshToken))
		{
			throw new InvalidToken();
		}
		try
		{
			$payload = self::getPayload($refreshToken);
			$newRefreshToken = self::generateToken(TokenType::REFRESH, $payload['sub'], $payload['uid'], $payload['role']);

			$newTokenPayload = self::getPayload($newRefreshToken);

			$dto = new TokenDto(
				$newTokenPayload['uid'],
				$newTokenPayload['jti'],
				$newTokenPayload['exp'],
			);

			TokenRepositoryImpl::updateByJti($payload['jti'], $dto);

			$newAccessToken = self::generateToken(
				TokenType::ACCESS,
				$payload['sub'],
				$payload['uid'],
				$payload['role'],
			);

			return [
				'access' => $newAccessToken,
				'refresh' => $newRefreshToken,
			];
		}
		catch (TokenNotUpdated)
		{
			throw new InvalidToken();
		}
	}
}
