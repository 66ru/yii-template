<?php

Yii::import('zii.widgets.jui.CJuiAutoComplete');

class AutoCompleteRowWidget extends CJuiAutoComplete
{
    /**
     * internal
     * @var CActiveRecord
     */
    public $model;

    /**
     * internal
     * @var string
     */
    public $attribute;

    /**
     * internal
     * @var TbActiveForm
     */
    public $form;

    public function run()
    {
        echo '<div class="control-group">';
        $htmlOptions['class'] = 'control-label';
        echo $this->form->labelEx($this->model, $this->attribute, $htmlOptions);

        echo '<div class="controls">';
        parent::run();
        echo $this->form->error($this->model, $this->attribute);
        echo "</div>";

        echo "</div>";
    }

}