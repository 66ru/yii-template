<?php

class ARHelper {

    /**
     * @param CActiveRecord $model
     * @param callable $callable function (CActiveRecord $model) {}
     * @param int $limit
     * @param string $iterateField
     */
    public static function processInBatch($model, $callable, $limit = 100, $iterateField = 'id')
    {
        $c = new CDbCriteria(array(
                'order' => $iterateField,
                'limit' => $limit,
                'offset' => 0,
            ));
        while ($items = $model->findAll($c)) {
            foreach ($items as $item) {
                $callable($item);
            }
            $c->offset+= $c->limit;
        }
    }

    /**
     * @param CActiveRecord $model
     * @param callable $callable function (array $fields) {}
     */
    public static function streamProcessInPlainSql($model, $callable)
    {
        $dataReader = Yii::app()->db->commandBuilder->createFindCommand($model->tableName(), $model->dbCriteria)->query();
        foreach ($dataReader as $row) {
            $callable($row);
        }
    }
}