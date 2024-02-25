<?php

namespace Up\Http;

enum Status: int
{
	case OK = 200;
	case CREATED = 201;
	case BAD_REQUEST = 400;
	case UNAUTHORIZED = 401;
	case FORBIDDEN = 403;
	case NOT_FOUND = 404;

	public function responseCode(): void
	{
		http_response_code($this->value);
	}
}
