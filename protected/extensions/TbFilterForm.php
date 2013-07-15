<?php

Yii::import('bootstrap.widgets.TbForm');

class TbFilterForm extends TbForm
{
    /**
     * Create the TbForm and assign the TbActiveForm with options as activeForm
     *
     * @param $config
     * @param $parent
     * @param array $options
     * @return mixed
     */
    public static function createForm($config, $parent, $options = array())
    {
        $class = __CLASS__;
        $options['class'] = 'TbActiveForm';

        $form = new $class($config, $parent);
        $form->activeForm = $options;

        return $form;
    }

    protected function init()
    {
        /** @var $model CActiveRecord */
        $model = $this->getModel(false);
        if (empty($model)) {
            $parentForm = $this->getParent();
            if ($parentForm instanceof CForm) {
                $relation = $this->attributes['name'];
                $model = $parentForm->model;
                $relations = $model->relations();
                $this->model = new $relations[$relation][1]();
            }
        }
    }

    public function renderElement($element)
    {
        $prepend = '';
        if ($element instanceof CFormInputElement) {
            if ($element->type == 'select2') {
                $prepend = CHtml::hiddenField(CHtml::resolveName($element->parent->model, $element->name).'[]', '', array('id' => false));
            }

            if (empty($element->attributes['class'])) {
                $element->attributes['class'] = 'input-block-level';
            } else {
                $element->attributes['class'] .= ' input-block-level';
            }

            if ($element->type == 'dropdownlist') {
                $element->attributes['empty'] = '';
            }
        }

        return $prepend . parent::renderElement($element);
    }

    public function render()
    {
        $render =  parent::render();

        /** @var $clientScript CClientScript */
        $clientScript = Yii::app()->clientScript;
        $formId = $this->root->activeFormWidget->id;
        $clientScript->registerScript(
            __CLASS__,
            '$("#' . $formId . ' input, #' . $formId . ' select").live("change", function () {

                $("#' . $formId . ' input, #' . $formId . ' select").each(function (){
                    $("div.grid-view [id="+$(this).attr("id")+"]").val($(this).val());
                });

                $.fn.yiiGridView.update($("div.grid-view").attr("id"));

                return false;
            });'
        );
        $clientScript->registerCss(__CLASS__, '#' . $formId . ' span.required { display:none; }');

        return $render;
    }
}