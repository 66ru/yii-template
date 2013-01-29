<?php

class FindOrCreateBehavior extends CActiveRecordBehavior
{
    /**
     * @param mixed $value used for search
     * @param bool|string $fieldName false means primary pey field
     * @return CActiveRecord
     */
    public function findOrCreate($value, $fieldName = false)
    {
        if (!$fieldName)
            $ar = $this->owner->findByPk($value);
        else
            $ar = $this->owner->findByAttributes(array($fieldName => $value));
        $className = get_class($this->owner);
        if (!$ar)
            $ar = new $className;

        return $ar;
    }
}
