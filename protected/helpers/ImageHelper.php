<?php

class ImageHelper
{
	public static function findIdentify($fileName = 'identify')
	{
		if (array_key_exists('PATH', $_ENV)) {
			$envPath = trim($_ENV['PATH']);
		} else if (($envPath = getenv('PATH')) !== false) {
			$envPath = trim($envPath);
		}
		if (!empty($envPath))
		{
			$dirs = explode( ':', $envPath);
			foreach ($dirs as $dir) {
				if (file_exists("{$dir}/{$fileName}")) {
					return "{$dir}/{$fileName}";
				}
			}
		}
		// The @-operator is used here mainly to avoid open_basedir
		// warnings. If open_basedir (or any other circumstance)
		// prevents the desired file from being accessed, it is fine
		// for file_exists() to return false, since it is useless for
		// use then, anyway.
		elseif (@file_exists("./{$fileName}")) {
			return $fileName;
		}

		return false;
	}

	public static function checkImageCorrect($imagePath)
	{
		if (!self::findIdentify())
			throw new CException('Can\'t find identify');

		$cmd = "identify -format \"%w|%h|%k\" ".escapeshellarg($imagePath)." 2>&1";
		$returnVal = 0;
		$output = array();
		exec($cmd, $output, $returnVal);
		if ($returnVal == 0 && count($output) == 1) {
			return true;
		} else {
			return false;
		}
	}
}
