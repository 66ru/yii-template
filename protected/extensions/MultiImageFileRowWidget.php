<?php

class MultiImageFileRowWidget extends CWidget
{
	/** @var CActiveRecord */
	public $model;

	/** @var string refers to fullsize image URL */
	public $attributeName;

	/** @var TbActiveForm */
	public $form;

	/** @var string refers to CUploadedFile instance */
	public $uploadedFileFieldName = '_image';

	/** @var string refers to checkbox field */
	public $removeImageFieldName = '_removeImageFlag';

	/** @var int */
	public $maxImageSize = 120;

	/** @var string php expression for thumbnail url */
	public $thumbnailImage = '';

	/** @var string php expression for full sized image url */
	public $image = '';

	public function run()
	{
		$model = $this->model;
		$attributeName = $this->attributeName;
		$form = $this->form;

		echo '<style type="text/css">
		.controls-line {
			margin-bottom: 5px;
		}
		</style>';
		echo '<div class="controls-group">';
		$htmlOptions['class'] = 'control-label';
		echo CHtml::activeLabelEx($model, $attributeName, $htmlOptions);
		if (is_array($model->$attributeName))
			foreach ($model->$attributeName as $id => $value) {
				$thumbnail = $value;
				if (!empty($this->thumbnailImage))
					$thumbnail = $this->evaluateExpression($this->thumbnailImage, array('data'=>$model, 'value'=>$value));
				$image = $this->evaluateExpression($this->image, array('data'=>$model, 'value'=>$value));

				echo '<div class="controls controls-line">';
				echo CHtml::link(
						CHtml::image($thumbnail, '', array('style'=>"max-width:{$this->maxImageSize}px; max-height:{$this->maxImageSize}px")),
						$image,
						array('target' => '_blank', 'style'=>'margin-right:1em')
					);
				echo '<label class="checkbox" style="display:inline-block" for="'.EHtml::resolveId($model, $this->removeImageFieldName."[$id]").'">';
				echo $form->checkBox($model, $this->removeImageFieldName."[$id]");
				echo $model->getAttributeLabel($this->removeImageFieldName);
				echo '</label></div>';
			}

		$fileUploadTemplate = '<div class="controls">';
		$fileUploadTemplate.= CHtml::activeFileField($model, $this->uploadedFileFieldName."[]");
		$fileUploadTemplate.= "</div>";
		$fileUploadTemplate = str_replace('"', '\\"', $fileUploadTemplate);
		echo '<div class="controls js-button">';
		$this->widget('bootstrap.widgets.TbButton', array(
			'label' => 'Добавить изображение',
			'icon' => 'plus',
		));
		echo "</div>";
		echo '<script type="text/javascript">
		$("div.js-button a").bind("click", function() {
			$("'.$fileUploadTemplate.'").insertBefore($(this).closest("div.controls")).show();
		});
		</script>';

		echo "</div>";
	}

}
