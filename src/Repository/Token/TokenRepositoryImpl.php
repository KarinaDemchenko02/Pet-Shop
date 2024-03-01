<?php

namespace Up\Repository\Token;

use Up\Dto\TokenDto;
use Up\Exceptions\Auth\TokenNotDeleted;
use Up\Exceptions\Auth\TokenNotUpdated;

class TokenRepositoryImpl implements TokenRepository
{

	public static function getByJti(string $jti): TokenDto
	{
		// TODO: Implement getByJti() method.
	}

	public static function deleteByJti(string $jti): void
	{
		// TODO: Implement deleteByJti() method.
	}

	/**
	 * @throws TokenNotUpdated
	 */
	public static function updateByJti(string $jti, TokenDto $newToken): void
	{
		$result = true;

		if (!$result)
		{
			throw new TokenNotUpdated();
		}
	}

	public static function getAllByUserId(int $uid): TokenDto
	{
		// TODO: Implement getAllByUserId() method.
	}

	/**
	 * @throws TokenNotDeleted
	 */
	public static function deleteAllByUserId(int $uid): void
	{
		// TODO: Implement getAllByUserId() method.
	}
}
