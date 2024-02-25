<?php

namespace Up\Util;

class Upload
{
	public static function upload(): string
	{
		$targetDir = "images/";
		$configuration = Configuration::getInstance();

		$allowedTypes = $configuration->option('ALLOWED_IMAGES_TYPE');
		$maxFileSize = $configuration->option('MAX_FILE_SIZE');

		$fileName = uniqid('', true) . '_' . $_FILES["fileToUpload"]["name"];
		$targetFile = $targetDir . $fileName;
		$fileType = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);

		if (getimagesize($_FILES["fileToUpload"]["tmp_name"]) === false)
		{
			throw new \RuntimeException("Error: The file is not an image.");
		}

		if (!in_array(strtolower($fileType), $allowedTypes, true))
		{
			$allowedTypes = implode(', ', $allowedTypes);
			throw  new \RuntimeException("Error: Invalid file type. Only images are allowed: $allowedTypes");
		}

		if ($_FILES["fileToUpload"]["size"] > $maxFileSize)
		{
			throw  new \RuntimeException("Error: The file is too big. The maximum file size is $maxFileSize MB.");
		}

		if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK)
		{
			throw  new \RuntimeException("An error occurred while uploading the file.");
		}

		if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile))
		{
			throw  new \RuntimeException("An error occurred while saving the file.");
		}

		return $targetFile;
	}

}
