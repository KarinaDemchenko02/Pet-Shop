<?php

namespace Up\Repository\Token;

use Up\Dto\TokenDto;

interface TokenRepository
{
	public static function getByJti(string $jti): TokenDto;

	public static function deleteByJti(string $jti): void;

	public static function updateByJti(string $jti, TokenDto $newToken): void;

	public static function getAllByUserId(int $uid): array;
}
