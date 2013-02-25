<?php

/**
 * Display images at edit page
 * Supports thumbnails and image editing (add, update, remove)
 *
 * Model must contains following attributes:
 * <code>
 * public $_image; // CUploadedFile[]
 * public $_removeImageFlag; // bool[]
 * </code>
 * With these sample validation rules:
 * <code>
 * array('_removeImageFlag', 'safe'),
 * array('_image', 'file', 'types' => 'jpg, png, gif, jpeg', 'maxFiles' => 10, 'allowEmpty' => true),
 * </code>
 * And controller must implement following code:
 * <code>
 * public function beforeSave($model)
 * {
 *     if (is_array($model->_removeImageFlag)) {
 *         foreach ($model->_removeImageFlag as $id => $remove) {
 *             if ($remove) {
 *                 // deleting file
 *                 // removing file id from attribute
 *             }
 *         }
 *     }
 *     if (is_array($model->_image)) {
 *         foreach ($model->_image as $key => $file) {
 *             $file = CUploadedFile::getInstance($model, '_image[' . $key . ']');
 *             if (!is_null($file) && !$file->error) {
 *                 // saving file from CUploadFile instance $file
 *                 // adding file id to attribute
 *             }
 *         }
 *     }
 *
 *     parent::beforeSave($model);
 * }
 * </code>
 *
 * @see AdminController::getEditFormElements
 */
class MultiImageFileRowWidget extends CInputWidget
{
    /**
     * internal
     * @var CActiveRecord
     */
    public $model;

    /**
     * @var string refers to array with image URLs
     */
    public $attribute;

    /**
     * internal
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
     * @var string php expression for thumbnail url
     * $data refer to model
     * $value refer to attribute value
     */
    public $thumbnailImage = '';

    /**
     * @var string php expression for full sized image url
     * $data refer to model
     * $value refer to attribute value
     */
    public $image = '';

    public function run()
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $form = $this->form;

        echo '<style type="text/css">
		.controls-line {
			margin-bottom: 5px;
		}
		</style>';
        echo '<div class="control-group">';
        $htmlOptions['class'] = 'control-label';
        echo CHtml::activeLabelEx($model, $attribute, $htmlOptions);
        if (is_array($model->$attribute)) {
            foreach ($model->$attribute as $id => $value) {
                $thumbnail = $value;
                if (!empty($this->thumbnailImage)) {
                    $thumbnail = $this->evaluateExpression(
                        $this->thumbnailImage,
                        array('data' => $model, 'value' => $value)
                    );
                }
                $image = $this->evaluateExpression($this->image, array('data' => $model, 'value' => $value));

                echo '<div class="controls controls-line">';
                echo CHtml::link(
                    CHtml::image(
                        $thumbnail,
                        '',
                        array('style' => "max-width:{$this->maxImageSize}px; max-height:{$this->maxImageSize}px")
                    ),
                    $image,
                    array('target' => '_blank', 'style' => 'margin-right:1em')
                );
                echo '<label class="checkbox" style="display:inline-block" for="' . EHtml::resolveId(
                    $model,
                    $this->removeImageFieldName . "[$id]"
                ) . '">';
                echo $form->checkBox($model, $this->removeImageFieldName . "[$id]");
                echo $model->getAttributeLabel($this->removeImageFieldName);
                echo '</label></div>';
            }
        }
        echo '<div class="controls">';
        echo $this->form->error($model, $this->uploadedFileFieldName);
        echo '</div>';

        $fileUploadTemplate = '<div class="controls">';
        $fileUploadTemplate .= CHtml::activeFileField($model, $this->uploadedFileFieldName . "[]");
        $fileUploadTemplate .= "</div>";
        $fileUploadTemplate = json_encode($fileUploadTemplate);
        echo '<div class="controls js-button">';
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'label' => 'Добавить изображение',
                'icon' => 'plus',
            )
        );
        echo "</div>";
        echo '<script type="text/javascript">
		$("div.js-button a").bind("click", function() {
			$(' . $fileUploadTemplate . ').insertBefore($(this).closest("div.controls")).show();
		});
		</script>';

        echo "</div>";
    }

}
