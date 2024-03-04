<?php

namespace Up\Repository\Token;

use Up\Dto\TokenDto;
use Up\Exceptions\Auth\TokenNotDeleted;
use Up\Exceptions\Auth\TokenNotUpdated;
use Up\Util\Database\Orm;
use Up\Util\Database\Tables\TokenTable;

class TokenRepositoryImpl implements TokenRepository
{
	public static function getByJti(string $jti): TokenDto
	{
		return self::createTokenList(self::getTokenList(['AND', ['=jti' => $jti]]))[$jti];
	}

	/**
	 * @throws TokenNotDeleted
	 */
	public static function deleteByJti(string $jti): void
	{
		$affectedRows = TokenTable::delete(['AND', ['=jti' => $jti]]);
		if ($affectedRows === 0)
		{
			throw new TokenNotDeleted();
		}

	}

	/**
	 * @throws TokenNotUpdated
	 */
	public static function updateByJti(string $jti, TokenDto $newToken): void
	{
		$orm = Orm::getInstance();
		$orm->begin();
		try
		{
			self::deleteByJti($jti);
			self::addToken($newToken);
			$orm->commit();
		}
		catch (\Throwable)
		{
			$orm->rollback();
			throw new TokenNotUpdated();
		}
	}

	/**
	 * @param int $uid
	 *
	 * @return  TokenDto[]
	 */
	public static function getAllByUserId(int $uid): array
	{
		return self::createTokenList(self::getTokenList(['AND', ['=user_id' => $uid]]));
	}

	/**
	 * @throws TokenNotDeleted
	 */
	public static function deleteAllByUserId(int $uid): void
	{
		$affectedRows = TokenTable::delete(['AND', ['=user_id' => $uid]]);
		if ($affectedRows === 0)
		{
			throw new TokenNotDeleted();
		}
	}

	public static function addToken(TokenDto $newToken): void
	{
		$fingerPrint = is_null($newToken->fingerPrint) ? 'NULL' : $newToken->fingerPrint;
		TokenTable::add(
			[
				'jti' => $newToken->jti,
				'user_id' => $newToken->uid,
				'expiration' => $newToken->exp,
				'finger_print' => $fingerPrint,
			]
		);
	}

	public static function createTokenEntity(array $row): TokenDto
	{
		return new TokenDto($row['user_id'], $row['jti'], $row['expiration'], $row['finger_print']);
	}

	private static function createTokenList(\mysqli_result $result): array
	{
		$tokens = [];
		while ($row = mysqli_fetch_assoc($result))
		{
			$tokens[$row['jti']] = self::createTokenEntity($row);
		}

		return $tokens;
	}

	private static function getTokenList($where = []): \mysqli_result|bool
	{
		return TokenTable::getList(['jti', 'user_id', 'expiration', 'finger_print'], conditions: $where);
	}
}
