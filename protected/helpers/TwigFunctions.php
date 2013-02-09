<?php

class TwigFunctions
{
    /**
     * @param string $class
     * @param array $properties
     * @return string
     */
    public static function widget($class, $properties = array())
    {
        $c = Yii::app()->getController();
        return $c->widget($class, $properties, true);
    }

    /**
     * @param string $class
     * @param string $property
     * @return mixed
     */
    public static function constGet($class, $property)
    {
        $c = new ReflectionClass($class);
        return $c->getConstant($property);
    }

    public static function _unset($array, $elementName)
    {
        unset($array[$elementName]);

        return $array;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public static function staticCall($class, $method, $params = array())
    {
        return call_user_func_array($class . '::' . $method, $params);
    }
}
