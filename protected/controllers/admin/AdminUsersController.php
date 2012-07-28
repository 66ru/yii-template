<?php

Yii::import('application.controllers.admin.*');

class AdminUsersController extends AdminController
{
	public $modelName = 'User';
	public $modelHumanTitle = array('пользователя', 'пользователя', 'пользователей');

	public function getEditFormElements() {
		return array(
			'email' => array(
				'type' => 'textField'
			),
			'password' => array(
				'type' => 'passwordField'
			),
		);
	}

	public function getTableColumns()
	{
		$attributes = array(
			'email',
			$this->getButtonsColumn(),
		);

		return $attributes;
	}

	/**
	 * @param User $model
	 */
	public function beforeSave($model)
	{
		$model->password = md5($model->password.Yii::app()->params['md5Salt']);;
		parent::beforeSave($model);
	}

	/**
	 * @param User $model
	 */
	public function beforeEdit($model)
	{
		$model->password = '';
		parent::beforeEdit($model);
	}
}
