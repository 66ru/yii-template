<?php

class ImageFileRowWidget extends CWidget
{
	/** @var CActiveRecord */
	public $model;

	/** @var string refers to fullsize image URL */
	public $attributeName;

	/** @var BootActiveForm */
	public $form;

	/** @var string refers to CUploadedFile instance */
	public $uploadedFileFieldName = '_image';

	/** @var string refers to checkbox field */
	public $removeImageFieldName = '_removeImageFlag';

	/** @var int */
	public $maxImageSize = 120;

	/** @var null|string URL to thumbnail image. If empty, used fullsize image */
	public $thumbnailImageUrl = null;

	public function run()
	{
		$model = $this->model;
		$attributeName = $this->attributeName;
		$form = $this->form;

		$hint = array();
		if (!empty($model->$attributeName)) {
			if (empty($this->thumbnailImageUrl))
				$this->thumbnailImageUrl = $model->$attributeName;
			$hint = array(
				'hint' => CHtml::link(
					CHtml::image($this->thumbnailImageUrl, '', array('style'=>"max-width:{$this->maxImageSize}px; max-height:{$this->maxImageSize}px")),
					$model->$attributeName,
					array('target' => '_blank')
				),
			);
		}
		echo $form->fileFieldRow($model, $this->uploadedFileFieldName, $hint);
		if (!empty($model->$attributeName)) {
			echo $form->checkboxRow($model, $this->removeImageFieldName);
		}
	}

}
