<?php

class CreateAuthItemsCommand extends CConsoleCommand
{
    public function actionIndex($email, $password)
    {
        /** @var $auth CAuthManager */
        $auth = Yii::app()->authManager;

        $existingRoles = $auth->getRoles();
        if (!array_key_exists('admin', $existingRoles)) {
            $auth->createRole('admin');
        }

        $newAdmin = User::model()->findByAttributes(array('email' => $email));
        if (empty($newAdmin))
            $newAdmin = new User();
        $newAdmin->email = $email;
        $newAdmin->password = $password;
        if (!$newAdmin->save())
            throw new CException(print_r($newAdmin->getErrors(), true));

        $userRoles = $auth->getRoles($newAdmin->id);
        if (!array_key_exists('admin', $userRoles))
            $auth->assign('admin', $newAdmin->id);
    }
}
