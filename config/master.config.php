<?php

return [
	'APP_NAME' => 'MadagascarShop',
	'APP_LANG' => 'en',
	'IMAGE_COMPRESSION_VALUE_JPEG' => 80,
	'IMAGE_COMPRESSION_VALUE_PNG' => 9,
	'NUMBER_OF_PRODUCTS_PER_PAGE' => 6,
	'JWT_SECRET' => "secret_key",
	'MAX_FILE_SIZE' => 1024 * 1024 * 5,
	'ALLOWED_IMAGES_TYPE' => ['jpg', 'jpeg', 'png', 'gif'],
	'JWT_ALG' => 'HS256',
	'JWT_EXP_ACCESS' => 60 * 5,
	'JWT_EXP_REFRESH' => 60 * 60 * 24,
	'TIMEZONE' => "Europe/Kaliningrad",
	"NUMBER_OF_PRODUCTS_PER_PREVIEW" => 3,
];
