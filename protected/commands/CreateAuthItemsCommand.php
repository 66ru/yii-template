<?php

class CreateAuthItemsCommand extends CConsoleCommand
{
	public function actionIndex($email, $password) {
		/** @var $auth CAuthManager */
		$auth=Yii::app()->authManager;
		$existingOperations = $auth->getOperations();
		if (!array_key_exists('adminActions', $existingOperations))
			$auth->createOperation('adminActions');
		if (!array_key_exists('userActions', $existingOperations))
			$auth->createOperation('userActions');

		$existingRoles = $auth->getRoles();
		if (!array_key_exists('user', $existingRoles)) {
			$role = $auth->createRole('user');
			$role->addChild('userActions');
		}
		if (!array_key_exists('admin', $existingRoles)) {
			$role = $auth->createRole('admin');
			$role->addChild('adminActions');
		}

		$newAdmin = User::model()->findByAttributes(array('email'=>$email));
		if (empty($newAdmin))
			$newAdmin = new User();
		$newAdmin->email = $email;
		$newAdmin->password = md5($password.Yii::app()->params['md5Salt']);
		if (!$newAdmin->save())
			throw new CException(print_r($newAdmin->getErrors(), true));

		$userRoles = $auth->getRoles($newAdmin->id);
		if (!array_key_exists('admin', $userRoles))
			$auth->assign('admin', $newAdmin->id);
	}
}
