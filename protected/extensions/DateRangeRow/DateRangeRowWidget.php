<?php

class DateRangeRowWidget extends CWidget
{
    /**
     * internal
     * @var CActiveRecord
     */
    public $model;

    /**
     * @var string dateFrom
     */
    public $attributeName;

    /**
     * @var string dateTo
     */
    public $attributeDateTo;

    /**
     * inernal
     * @var TbActiveForm
     */
    public $form;

    public function init()
    {
        parent::init();

        $url = Yii::app()->assetManager->publish(__DIR__ . '/assets', false, -1, YII_DEBUG);
        Yii::app()->clientScript->registerScriptFile($url . '/js/jquery-ui-1.8.23.custom.min.js');
        Yii::app()->clientScript->registerScriptFile($url . '/js/jquery.ui.datepicker-ru.js');

        Yii::app()->clientScript->registerCssFile($url . '/css/jquery-ui-1.8.23.custom.css');
    }

    public function run()
    {
        $idFrom = EHtml::resolveId($this->model, $this->attributeName);
        $idTo = EHtml::resolveId($this->model, $this->attributeDateTo);
        Yii::app()->clientScript->registerScript('datePickerInitialize', '
			$.datepicker.setDefaults( $.datepicker.regional["ru"] );
			$("#' . $idFrom . '").datepicker({
				onSelect: function( selectedDate ) {
					$( "#' . $idTo . '" ).datepicker( "option", "minDate", selectedDate );
				}
			});
			$("#' . $idTo . '").datepicker({
				onSelect: function( selectedDate ) {
					$( "#' . $idFrom . '" ).datepicker( "option", "maxDate", selectedDate );
				}
			});
		');

        echo "
<style type='text/css'>
.controls-line {
	margin-bottom: 5px;
}
</style>
<div class='control-group'>
	" . CHtml::activeLabelEx($this->model, $this->attributeName, array('class' => 'control-label')) . "
	<div class='controls controls-line'>
		<div class='input-append'>
			{$this->form->textField($this->model, $this->attributeName)}<span class='add-on'><i class='icon-calendar'></i></span>
		</div>
		<div class='input-append'>
			<label style='margin: 0 20px; display:inline;' for='" . $idTo . "'>
				{$this->model->getAttributeLabel($this->attributeDateTo)}
			</label>
			{$this->form->textField($this->model, $this->attributeDateTo)}<span class='add-on'><i class='icon-calendar'></i></span>
		</div>
	</div>
	<div class='controls'>
		{$this->form->error($this->model, $this->attributeName)}
		{$this->form->error($this->model, $this->attributeDateTo)}
	</div>
</div>
";
    }
}
