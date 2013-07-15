<?php

/**
 * Display file link at edit page
 *
 * Model must contains following attributes:
 * <code>
 * public $_file; // CUploadedFile
 * public $_removeFileFlag; // bool
 * </code>
 * And controller must implement following code:
 * <code>
 * public function beforeSave($model)
 * {
 *     if ($model->_removeFileFlag) {
 *         // removing file
 *         // set attribute to null
 *     }
 *     $model->_file = CUploadedFile::getInstance($model, '_file');
 *     if ($model->validate() && !empty($model->_file)) {
 *         // saving file from CUploadFile instance $model->_file
 *     }
 *
 *     parent::beforeSave($model);
 * }
 * </code>
 *
 * @see AdminController::getEditFormElements
 */
class FileRowWidget extends CInputWidget
{
    /**
     * internal
     * @var CActiveRecord
     */
    public $model;

    /**
     * internal
     * @var string link to file
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
    public $uploadedFileFieldName = '_file';

    /**
     * @var string refers to checkbox field
     */
    public $removeFileFieldName = '_removeFileFlag';

    /**
     * @var bool Whether display remove checkbox or not
     */
    public $allowRemove = true;

    public function run()
    {
        $model = $this->model;
        $attributeName = $this->attribute;
        $form = $this->form;
        $htmlOptions = $this->htmlOptions;

        if (!empty($model->$attributeName)) {
            $link = CHtml::link(
                CHtml::encode($model->$attributeName),
                $model->$attributeName,
                array('target' => '_blank')
            );
            if (!empty($htmlOptions['hint'])) {
                $htmlOptions['hint'].= "<br /><br />" . $link;
            } else {
                $htmlOptions['hint'] = $link;
            }
        }
        echo $form->fileFieldRow($model, $this->uploadedFileFieldName, $htmlOptions);
        if (!empty($model->$attributeName) && $this->allowRemove) {
            echo $form->checkboxRow($model, $this->removeFileFieldName);
        }
    }

}
