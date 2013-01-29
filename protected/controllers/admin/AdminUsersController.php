<?php

Yii::import('application.controllers.admin.*');

class AdminUsersController extends AdminController
{
    public $modelName = 'User';
    public $modelHumanTitle = array('пользователя', 'пользователя', 'пользователей');

    /**
     * @param User $model
     * @return array
     */
    public function getEditFormElements($model)
    {
        return array(
            'email' => array(
                'type' => 'textField'
            ),
            'authItems' => array(
                'type' => 'dropDownList',
                'data' => EHtml::listData(AuthItem::model()),
                'htmlOptions' => array(
                    'multiple' => true,
                    'size' => 20,
                ),
            ),
            'password' => array(
                'type' => 'passwordField',
                'htmlOptions' => array(
                    'value' => '',
                    'hint' => $model->isNewRecord ? '' : 'Если ничего не вводить, то пароль не будет изменен.',
                ),
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
        if (mb_strlen($model->password) < 32)
            $model->password = md5($model->password . Yii::app()->params['md5Salt']);
        ;
        parent::beforeSave($model);
    }

    /**
     * @param User $model
     * @param array $attributes
     */
    public function beforeSetAttributes($model, &$attributes)
    {
        if (empty($attributes['password']))
            unset($attributes['password']);

        parent::beforeSetAttributes($model, $attributes);
    }
}
