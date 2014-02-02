<?php

class AdminUsersController extends CommonAdminController
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
                'type' => 'textField',
            ),
            'authItems' => array(
                'type' => 'select2',
                'htmlOptions' => array(
                    'data' => CHtml::listData(AuthItem::model()->findAll(), 'name', 'name'),
                    'htmlOptions' => array(
                        'multiple' => true,
                        'class' => 'input-xlarge',
                    ),
                ),
            ),
            'password' => array(
                'type' => 'passwordField',
                'rowOptions' => array(
                    'hint' => $model->isNewRecord ? '' : 'Если ничего не вводить, то пароль не будет изменен',
                ),
            ),
        );
    }

    public function getTableColumns($model)
    {
        $attributes = array(
            'email',
            $this->getButtonsColumn(),
        );

        return $attributes;
    }

    /**
     * @param User $model
     * @param array $attributes
     * @return bool
     */
    public function beforeSetAttributes($model, &$attributes)
    {
        if (empty($attributes['password'])) {
            unset($attributes['password']);
        }

        return parent::beforeSetAttributes($model, $attributes);
    }
}
