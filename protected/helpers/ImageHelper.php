<?php

class ImageHelper
{
	public static function checkImageCorrect($imagePath)
	{
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
