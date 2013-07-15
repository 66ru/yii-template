<?php

/**
 * Class EHtml
 * version 1.4
 */
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
     * Works like CHtml::listData. Supports magic fields.
     * @param CActiveRecord $model
     * @param string $valueField defaults to primary key field
     * @param string $textField defaults to primary key field
     * @return array if ($valueField == $textField) <br> returns Array($valueField, ...) <br> else Array($valueField => $textField, ...)
     */
    public static function listData($model, $valueField = '', $textField = '')
    {
        $pk = $model->metaData->tableSchema->primaryKey;
        if ($valueField === '') {
            $valueField = $pk;
        }
        if ($textField === '') {
            $textField = $valueField;
        }

        $columnNames = $model->tableSchema->columnNames;
        $select = '*';
        if (in_array($valueField, $columnNames) && in_array($textField, $columnNames)) {
            $select = ($valueField == $textField) ? $valueField : $valueField . ',' . $textField;
        }
        $data = CHtml::listData(
            $model->findAll(array('select' => $select)),
            $valueField,
            $textField
        );
        if ($valueField == $textField) {
            $data = array_keys($data);
        }

        return $data;
    }
}
