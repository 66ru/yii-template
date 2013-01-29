<?php

class EFileHelper
{
    /**
     * @param string $fileName
     * @return string
     */
    public static function getExtensionByMimeType($fileName)
    {
        $mimeTypes = require(Yii::getPathOfAlias('system.utils.mimeTypes') . '.php');
        $unsetArray = array('jpe', 'jpeg');
        foreach ($unsetArray as $key)
            unset($mimeTypes[$key]);

        $mimeType = CFileHelper::getMimeType($fileName);
        return (string)array_search($mimeType, $mimeTypes);
    }
}
