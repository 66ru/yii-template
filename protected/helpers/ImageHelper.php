<?php

class ImageHelper
{

	/**
	 * @param $imagePath
	 * @return array|bool array(width, height) if success
	 * @throws CException
	 */
	public static function checkImageCorrect($imagePath)
	{
		$cmd = "identify -format \"%w|%h|%k\" ".escapeshellarg($imagePath)." 2>&1";
		$returnVal = 0;
		$output = array();
		exec($cmd, $output, $returnVal);
		if ($returnVal == 0 && count($output) == 1) {
			$imageSizes = explode('|', $output[0]);
			array_pop($imageSizes);
			return $imageSizes;
		} elseif ($returnVal == 127) {
			throw new CException('Can\'t find identify');
		} else {
			return false;
		}
	}
}
