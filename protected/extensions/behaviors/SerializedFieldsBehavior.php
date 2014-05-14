<?php

class SerializedFieldsBehavior extends CActiveRecordBehavior
{
    /**
     * @var string[]|string Array or comma separated string with field names that should be serialized in db.
     */
    public $fields = array();

    private function getSerializedFields()
    {
        if (is_array($this->fields)) {
            return $this->fields;
        } else {
            $res = explode(',', $this->fields);
            array_walk(
                $res,
                function (&$value) {
                    $value = trim($value);
                }
            );

            return $res;
        }
    }

    public function beforeSave($event)
    {
        foreach ($this->getSerializedFields() as $fieldName) {
            $this->getOwner()->$fieldName = json_encode($this->getOwner()->$fieldName);
        }
    }

    public function afterSave($event)
    {
        foreach ($this->getSerializedFields() as $fieldName) {
            if (is_string($this->getOwner()->$fieldName)) {
                $this->getOwner()->$fieldName = json_decode($this->getOwner()->$fieldName, true);
            }
        }
    }

    public function afterFind($event)
    {
        foreach ($this->getSerializedFields() as $fieldName) {
            if (is_string($this->getOwner()->$fieldName)) {
                $this->getOwner()->$fieldName = json_decode($this->getOwner()->$fieldName, true);
            }
        }
    }
}
