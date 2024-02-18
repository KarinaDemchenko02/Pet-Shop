<?php

namespace Up\Util\Compression;

use Up\Exceptions\Images\ImageNotCopy;
use Up\Exceptions\Images\ImageNotResize;

class CompressionImages
{
	private string $path;
	private string $destination;

	public function __construct(string $path, string $destination)
	{
		$this->path = $path;
		$this->destination = $destination;
	}
	public function compressImages(): bool | array
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

		if (!empty($this->getErrors()))
		{
			return $this->getErrors();
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

				if (is_dir($newPath))
				{
					$subFiles = $this->getImages($newPath);
					$imagesName = [...$subFiles, ...$imagesName];
				}
				else
					if (!file_exists($this->getDestination() . basename($newPath)))
					{
						try
						{
							$imagesName[] = basename($newPath);
							copy($newPath, $this->getDestination() . basename($newPath));
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

	private function resizeImage(string $path, string $movePath, string $nameImage): void
	{
		$imageSize = getimagesize($path);

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
