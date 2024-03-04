<?php

namespace Up\Util\Middleware\PreMiddleware;

use Firebase\JWT\ExpiredException;
use Up\Auth\JwtService;
use Up\Auth\TokenType;
use Up\Exceptions\Auth\EmptyToken;
use Up\Exceptions\Auth\InvalidToken;
use Up\Exceptions\Auth\TokensNotRefreshed;
use Up\Http\Request;
use Up\Http\Response;

class IsLogin implements PreMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(Request $request, callable $next): Response
	{
		$accessToken = $request->getCookie('JWT-ACCESS');
		$refreshToken = $request->getCookie('JWT-REFRESH');

		if ($accessToken === '' && $refreshToken === '')
		{
			$request->setData('email', null);
			$request->setData('role', null);
			$request->setData('userId', null);
			return $next($request);
		}

		try
		{
			try
			{
				JwtService::validateToken($accessToken);
			}
			catch (ExpiredException|EmptyToken)
			{
				$accessToken = self::refreshTokens($request->getCookie('JWT-REFRESH'));
			}
		}
		catch (InvalidToken|TokensNotRefreshed)
		{
			self::disableUser($request);
			return $next($request);
		}


		$payload = JwtService::getPayload($accessToken);
		$request->setData('email', $payload['sub']);
		$request->setData('role', $payload['role']);
		$request->setData('userId', $payload['uid']);

		return $next($request);
	}

	private static function disableUser(Request $request): void
	{
		JwtService::deleteTokenFromDb($request->getCookie('JWT-REFRESH'));
		JwtService::deleteCookie(TokenType::ACCESS);
		JwtService::deleteCookie(TokenType::REFRESH);
		$request->setData('email', null);
		$request->setData('role', null);
		$request->setData('userId', null);
	}

	/**
	 * @throws TokensNotRefreshed
	 */
	private static function refreshTokens(string $refreshToken): string
	{
		try
		{
			$tokens = JwtService::refreshTokens($refreshToken);
		}
		catch (\JsonException|InvalidToken|EmptyToken)
		{
			JwtService::deleteCookie(TokenType::ACCESS);
			JwtService::deleteCookie( TokenType::REFRESH);
			throw new TokensNotRefreshed();
		}
		$access = $tokens['access'];
		$refresh = $tokens['refresh'];

		JwtService::saveTokenInCookie($access, TokenType::ACCESS);
		JwtService::saveTokenInCookie($refresh, TokenType::REFRESH);
		return $access;
	}
}
