<?php

class DependedInputWidget extends CWidget
{
    /**
     * @var CActiveRecord
     */
    public $model;

    /**
     * @var string not used
     */
    public $attributeName;

    /**
     * @var TbActiveForm
     */
    public $form;

    /**
     * @var null|string master element Name. Onchange event will bind on it
     */
    public $masterAttributeName = null;

    /**
     * @var null|string html data, loaded from url will assigned to this element
     */
    public $dependedAttributeName = null;

    /**
     * @var array id=>array(). Depended values to master values mapping. Id - master value, array contains depended ids
     */
    public $valuesMap = array();

    public function run()
    {
        if (!empty($this->masterAttributeName) &&
            !empty($this->dependedAttributeName) &&
            !empty($this->model)
        ) {
            /** @var $app CWebApplication */
            $app = Yii::app();

            $app->clientScript->registerScript(
                $this->dependedAttributeName . '-map-' . $this->masterAttributeName,
                'var masterElementId = "' . EHtml::resolveId($this->model, $this->masterAttributeName) . '";
				var dependedElementId = "' . EHtml::resolveId($this->model, $this->dependedAttributeName) . '";
				var valuesMap = ' . json_encode($this->valuesMap) . ';
				
				$("#"+masterElementId).bind("change", function() {
					var $d = $("#"+dependedElementId);
					$d.val([]);
					var selectedValues = $(this).val();
					for (i in selectedValues)
						if (selectedValues.hasOwnProperty(i)) {
							var newValues = valuesMap[selectedValues[i]];
							if ($d.val() instanceof Array)
								newValues = newValues.concat( $d.val() );
							$d.val(newValues);
						}
				})'
            );
        }
    }
}
