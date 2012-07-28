<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CBaseUserIdentity
{
	private $_id;

	/**
	 * @var string email
	 */
	public $email;
	/**
	 * @var string password
	 */
	public $password;

	/**
	 * Constructor.
	 * @param string $email email
	 * @param string $password password
	 */
	public function __construct($email,$password)
	{
		$this->email=$email;
		$this->password=$password;
	}

	public function authenticate()
	{
		$record = User::model()->findByAttributes(array('email' => $this->email));
		if ($record === null)
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		else if ($record->password !== md5($this->password . Yii::app()->params['md5Salt']))
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		else {
			$this->_id = $record->id;
			$this->errorCode = self::ERROR_NONE;
		}
		return !$this->errorCode;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getName()
	{
		return $this->email;
	}
}