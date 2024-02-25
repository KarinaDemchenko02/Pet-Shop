<?php

namespace Up\Http;

interface Passable
{
	public function getDataByKey(string $key): mixed;
}
