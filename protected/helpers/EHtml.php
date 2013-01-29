<?php

class EHtml
{
    /**
     * Generates a valid HTML ID based for a model attribute.
     * Note, the attribute name may be modified after calling this method if the name
     * contains square brackets (mainly used in tabular input) before the real attribute name.
     * @param CModel $model the data model
     * @param string $attribute the attribute
     * @return string the ID generated based on name.
     */
    public static function resolveId($model, $attribute)
    {
        return CHtml::getIdByName(CHtml::resolveName($model, $attribute));
    }

    /**
     * @param CActiveRecord $model
     * @param string $valueField defaults to primary key field
     * @param string $textField defaults to primary key field
     * @return array
     */
    public static function listData($model, $valueField = '', $textField = '')
    {
        $pk = $model->metaData->tableSchema->primaryKey;
        if ($valueField === '')
            $valueField = $pk;
        if ($textField === '')
            $textField = $valueField;

        return CHtml::listData($model->findAll(array(
            'select' => ($valueField == $textField) ? $valueField : $valueField . ',' . $textField
        )), $valueField, $textField);
    }
}
