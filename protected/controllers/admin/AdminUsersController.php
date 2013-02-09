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
                'type' => TbInput::TYPE_TEXT,
            ),
            'authItems' => array(
                'type' => TbInput::TYPE_DROPDOWN,
                'data' => EHtml::listData(AuthItem::model()),
                'htmlOptions' => array(
                    'multiple' => true,
                    'size' => 20,
                ),
            ),
            'password' => array(
                'type' => TbInput::TYPE_PASSWORD,
                'htmlOptions' => array(
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
     * @param array $attributes
     */
    public function beforeSetAttributes($model, &$attributes)
    {
        if (empty($attributes['password']))
            unset($attributes['password']);

        parent::beforeSetAttributes($model, $attributes);
    }
}
