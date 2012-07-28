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
			$this->getOwner()->$fieldName = serialize($this->getOwner()->$fieldName);
	}

	public function afterFind($event)
	{
		foreach ($this->serializedFields as $fieldName)
			$this->getOwner()->$fieldName = unserialize($this->getOwner()->$fieldName);
	}
}
