<?php

namespace Up\Util\Compression;

use Up\Exceptions\Images\ImageNotCopy;
use Up\Exceptions\Images\ImageNotResize;

class CompressionImages
{
	private string $path;
	private string $destination;
	private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

	public function __construct(string $path, string $destination)
	{
		$this->path = $path;
		$this->destination = $destination;
	}
	public function compressImages(): bool
	{
		$images = $this->getImages($this->getPath());

		if (empty($images))
		{
			return false;
		}

		foreach ($images as $image)
		{
			try
			{
				$this->resizeImage($this->getDestination() . $image, $this->getDestination(), $image);
			}
			catch (ImageNotResize $e)
			{
				echo $e->getMessage();
				return false;
			}
		}

		return true;
	}

	private function getImages(string $path): bool | array
	{
		$imagesName = [];

		if (is_dir($path))
		{
			$files = array_diff(scandir($path), ['..', '.']);

			foreach ($files as $file)
			{
				$newPath = $path . '/' . $file;
				$nameNewPath = basename($newPath);

				if (is_dir($newPath))
				{
					$subFiles = $this->getImages($newPath);
					$imagesName = [...$subFiles, ...$imagesName];
				}
				else
					if (!file_exists($this->getDestination() . $nameNewPath) && $this->checkImage(basename($nameNewPath)))
					{
						try
						{
							$imagesName[] = $nameNewPath;
							copy($newPath, $this->getDestination() . $nameNewPath);
						}
						catch (ImageNotCopy $e)
						{
							echo $e->getMessage();
							return false;
						}
					}
			}
		}

		return $imagesName;
	}

	private function checkImage(string $filePath): bool
	{
		$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

		if (in_array($fileExtension, $this->allowedExtensions, true)) {
			return true;
		}

		return false;
	}

	private function resizeImage(string $path, string $movePath, string $nameImage): void
	{
		$imageSize = getimagesize($path);

		if (!$imageSize)
		{
			return;
		}

		switch ($imageSize['mime']) {
			case 'image/jpeg':
				$image = imagecreatefromjpeg($path);
				imagejpeg($image, $movePath . '/' . $nameImage, 80);
				break;
			case 'image/png':
				$image = imagecreatefrompng($path);
				imagepng($image, $movePath .  '/' . $nameImage, 9);
				break;
			case 'image/git':
				$image = imagecreatefromgif($path);
				imagegif($image, $movePath .  '/' . $nameImage);
				break;
			default:
				$image = imagecreatefromjpeg($path);
				imagejpeg($image, $movePath .  '/' . $nameImage, 80);
		}

	}
	public function getDestination(): string
	{
		return $this->destination;
	}

	public function getPath(): string
	{
		return $this->path;
	}
}
