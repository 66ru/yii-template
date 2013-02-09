<?php

class ImageFileRowWidget extends CWidget
{
    /**
     * @var CActiveRecord
     */
    public $model;

    /**
     * @var string refers to fullsize image URL
     */
    public $attributeName;

    /**
     * @var TbActiveForm
     */
    public $form;

    /**
     * @var string refers to CUploadedFile instance
     */
    public $uploadedFileFieldName = '_image';

    /**
     * @var string refers to checkbox field
     */
    public $removeImageFieldName = '_removeImageFlag';

    /**
     * @var int
     */
    public $maxImageSize = 120;

    /**
     * @var null|string URL to thumbnail image. If empty, used fullsize image
     */
    public $thumbnailImageUrl = null;

    /**
     * @var string Hint will appended to file field
     */
    public $hint = '';

    public function run()
    {
        $model = $this->model;
        $attributeName = $this->attributeName;
        $form = $this->form;

        $htmlOptions = array();
        if (!empty($this->hint)) {
            $this->hint = "{$this->hint}";
            $htmlOptions = array('hint' => $this->hint);
        }
        if (!empty($model->$attributeName)) {
            if (empty($this->thumbnailImageUrl))
                $this->thumbnailImageUrl = $model->$attributeName;
            $htmlOptions = array(
                'hint' => $this->hint . "<br /><br />" . CHtml::link(
                    CHtml::image($this->thumbnailImageUrl, '', array('style' => "max-width:{$this->maxImageSize}px; max-height:{$this->maxImageSize}px")),
                    $model->$attributeName,
                    array('target' => '_blank')
                ),
            );
        }
        echo $form->fileFieldRow($model, $this->uploadedFileFieldName, $htmlOptions);
        if (!empty($model->$attributeName)) {
            echo $form->checkboxRow($model, $this->removeImageFieldName);
        }
    }

}
