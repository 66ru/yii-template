<?php

class SerializedFieldsBehavior extends CActiveRecordBehavior
{
	/**
	 * @var array Array of field names that should be serialized in db.
	 */
	public $serializedFields = array();

	public function beforeSave($event)
	{
		foreach ($this->serializedFields as $fieldName)
			$this->getOwner()->$fieldName = json_encode($this->getOwner()->$fieldName);
	}

	public function afterFind($event)
	{
		foreach ($this->serializedFields as $fieldName)
			if (is_string($this->getOwner()->$fieldName))
				$this->getOwner()->$fieldName = json_decode($this->getOwner()->$fieldName, true);
	}
}
