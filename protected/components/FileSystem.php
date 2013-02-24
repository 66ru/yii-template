<?php

/**
 * Upload files, images. Resize images. Get URLs.
 */
class FileSystem extends CComponent
{
    /**
     * @var null|string path to storage dir. Default = www/storage
     */
    public $storagePath = null;
    /**
     * @var null|string url to storage dir. Default = /storage
     */
    public $storageUrl = null;

    /**
     * @var int how much intermediate folders must be created in storage folder for publishing file. <br />
     * <b>WARNING!</b> Do not change if storage dir is not empty!
     */
    public $nestedFolders = 0;

    /**
     * @var int jpeg compress quality (0-100)
     */
    public $jpegQuality = 90;

    public function init()
    {
        if (is_null($this->storagePath)) {
            $this->storagePath = Yii::app()->basePath . '/../www/storage';
        }
        if (is_null($this->storageUrl)) {
            $this->storageUrl = '/storage/';
        }
        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0775, true);
        }
        $this->storagePath = realpath($this->storagePath) . '/';
        if (!is_dir($this->storagePath)) {
            throw new CException('FileSystem->storagePath is not dir (' . $this->storagePath . ')');
        }
    }

    private function getUniqId()
    {
        return md5(microtime(true) . mt_rand());
    }

    public function getIntermediatePath($uid)
    {
        $path = '';
        $fileName = pathinfo($uid, PATHINFO_FILENAME);
        for ($i = 0; $i < $this->nestedFolders; $i++) {
            $path .= substr($fileName, $i * 2, 2) . '/';
        }

        return $path;
    }

    /**
     * @param string $uid
     * @return string full path in filesystem to file
     */
    public function getFilePath($uid)
    {
        return $this->storagePath . $this->getIntermediatePath($uid) . $uid;
    }

    /**
     * @param string $uid
     * @return string Url to file
     */
    public function getFileUrl($uid)
    {
        return $this->storageUrl . $this->getIntermediatePath($uid) . $uid;
    }

    /**
     * @param string $fileName uploaded file
     * @param string $originalName original file name. Used for getting extension. If null - using $fileName.
     * @return string Uid of new file
     */
    public function publishFile($fileName, $originalName = null)
    {
        if (is_null($originalName)) {
            $originalName = $fileName;
        }
        $ext = strtolower(CFileHelper::getExtension($originalName));
        if (empty($ext)) { // we have empty extension. Trying determine using mime type
            $ext = EFileHelper::getExtensionByMimeType($fileName);
        }
        if (!empty($ext)) {
            $ext = '.' . $ext;
        }

        $uid = $this->getUniqId() . $ext;
        $publishedFileName = $this->getFilePath($uid);
        $newDirName = pathinfo($publishedFileName, PATHINFO_DIRNAME);
        if (!file_exists($newDirName)) {
            mkdir($newDirName, 0775, true);
        }

        copy($fileName, $publishedFileName);

        return $uid;
    }

    /**
     * @param string $uid
     */
    public function removeFile($uid)
    {
        $filePath = $this->getFilePath($uid);
        $dirName = pathinfo($filePath, PATHINFO_DIRNAME);
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        foreach (glob($dirName . '/' . $fileName . '*') as $file) {
            unlink($file);
        }
    }

    /********************************/

    /**
     * @param string $uid
     * @param array $size
     * @return string
     */
    public function getResizedImageUrl($uid, $size)
    {
        $fileName = pathinfo($uid, PATHINFO_FILENAME);
        $ext = pathinfo($uid, PATHINFO_EXTENSION);
        if (!empty($ext)) {
            $ext = '.' . $ext;
        }

        return $this->storageUrl . $this->getIntermediatePath($uid) . $fileName . $this->getSizeSuffix($size) . $ext;
    }

    /**
     * @param array $size
     * @return string
     */
    public function getSizeSuffix($size)
    {
        $suffix = '';
        if (empty($size[2])) {
            $size[2] = Image::AUTO;
        }

        if ($size[2] == Image::AUTO) {
            $suffix = "-{$size[0]}x{$size[1]}";
        } elseif ($size[2] == Image::WIDTH) {
            $suffix = "-w{$size[0]}";
        } elseif ($size[2] == Image::HEIGHT) {
            $suffix = "-h{$size[1]}";
        }

        return $suffix;
    }

    /**
     * @param string $uid
     * @param array $sizes
     * @param bool $forceCreate
     * @return void
     */
    public function resizeImage($uid, $sizes = array(), $forceCreate = false)
    {
        /** @var $cImage CImageComponent */
        $cImage = Yii::app()->image;
        $imageFile = $this->getFilePath($uid);
        $pathInfo = pathinfo($imageFile);
        $ext = $pathInfo['extension'] ? '.' . $pathInfo['extension'] : '';

        $originalImage = $cImage->load($imageFile);
        if (!is_array($sizes[0])) {
            $sizes = array($sizes);
        }

        foreach ($sizes as $size) {
            $newImageName = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . $this->getSizeSuffix($size) . $ext;
            if (file_exists($newImageName) && !$forceCreate) {
                continue;
            }

            $image = $originalImage;
            $image->resize($size[0], $size[1], !empty($size[2]) ? $size[2] : Image::AUTO)->quality($this->jpegQuality);
            $image->save($newImageName, 0664);
        }
    }
}
