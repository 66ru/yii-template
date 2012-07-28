<?php

/**
 * @property int id
 * @property string email
 * @property string password
 */
class User extends CActiveRecord
{
	/**
	 * @static
	 * @param string $className
	 * @return User|CActiveRecord
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function init()
	{
		$this->scenario = 'save';
	}

	public function attributeLabels()
	{
		return array(
			'email' => 'E-mail',
			'password' => 'Пароль',
		);
	}

	public function rules()
	{
		return array(
			array('email', 'email', 'allowEmpty'=>false),
			array('email', 'unique'),
			array('email, password', 'required'),
			array('password', 'length', 'is'=>32, 'allowEmpty'=>false, 'on'=>'save'),
			array('password', 'length', 'max'=>31, 'allowEmpty'=>false, 'on'=>'edit'),

			array('email', 'safe', 'on'=>'search'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('email', $this->email, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}
