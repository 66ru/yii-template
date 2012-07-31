<?php
/**
 * FHexValidator class file.
 *
 * @author Stefan Volkmar <volkmar_yii@email.de>
 * @version 1.0
 * @link http://www.yiiframework.com/extension/
 * @license BSD
 */

/**
 * FHexValidator verifies if the attribute represents only hexadecimal digit characters.
 */
class FHexValidator extends CValidator
{
	/**
	 * @var boolean whether the attribute value can be null or empty. Defaults to true,
	 * meaning that if the attribute is empty, it is considered valid.
	 */
	public $allowEmpty=true;

	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel the data object being validated
	 * @param string the name of the attribute to be validated.
	 */
	protected function validateAttribute($object,$attribute)
    {
		$value=$object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value))
			return;

		if(!ctype_xdigit((string) $value))
		{
			$message=$this->message!==null?$this->message : Yii::t(__CLASS__,'{attribute} has not only hexadecimal digit characters.');
			$this->addError($object,$attribute,$message);
		}

	}

}