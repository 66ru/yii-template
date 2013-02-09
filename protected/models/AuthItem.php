<?php

/**
 * @property string $name
 * @property int $type
 * @property string $data
 */
class AuthItem extends CActiveRecord
{
    /**
     * @static
     * @param string $className
     * @return AuthItem
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function init()
    {
        $this->data = 'N;';
    }

    public function rules()
    {
        return array(
            array('name', 'unique'),
            array('name', 'length', 'max' => 64, 'allowEmpty' => false),
            array('type', 'in', 'range' => array(CAuthItem::TYPE_OPERATION, CAuthItem::TYPE_ROLE, CAuthItem::TYPE_TASK)),

            array('data', 'safe'),
        );
    }

    public function relations()
    {
        return array(
//			'authItemChild' => array(self::HAS_MANY, 'AuthItemChild', 'parent'),
//			'childs' => array(self::HAS_MANY, 'AuthItem', array('child'=>'name'), 'through' => 'authItemChild'),
//			'parent' => array(self::HAS_ONE, 'AuthItem', 'AuthItemChild(parent,child)'),
        );
    }
}
