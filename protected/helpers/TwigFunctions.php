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

    /**
     * @param array $array
     * @param string|int $elementName
     * @return array
     */
    public static function _unset($array, $elementName)
    {
        unset($array[$elementName]);

        return $array;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public static function url($route, $params = array())
    {
        return Yii::app()->createUrl($route, $params);
    }

    /**
     * @param $route string
     * @param array $params
     * @return string
     */
    public static function absUrl($route, $params = array())
    {
        return Yii::app()->createAbsoluteUrl($route, $params);
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

    public static function newObject($class, $params = array())
    {
        $reflectionClass = new ReflectionClass($class);
        return $reflectionClass->newInstanceArgs($params);
    }

    /**
     * @param int $n
     * @param string $one
     * @param string $two
     * @param string $many
     * @return string
     */
    public static function plural($n, $one, $two, $many)
    {
        return $n % 10 == 1 && $n % 100 != 11 ? $one : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $two : $many);
    }
}
