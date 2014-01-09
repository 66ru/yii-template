<?php

class FindOrCreateBehavior extends CActiveRecordBehavior
{
    /**
     * @param string $value used for search
     * @param bool|string $fieldName false means primary key field
     * @return CActiveRecord
     */
    public function findOrCreate($value, $fieldName = false)
    {
        if (!$fieldName) {
            $object = $this->owner->findByPk($value);
        } else {
            $object = $this->owner->findByAttributes(array($fieldName => $value));
        }
        $className = get_class($this->owner);
        if (!$object) {
            $object = new $className;
            if ($fieldName !== false) {
                $object->$fieldName = $value;
            }
        }

        return $object;
    }
}
