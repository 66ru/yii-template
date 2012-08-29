<?php

class TwigFunctions
{
	/**
	 * @param string $className
	 * @param array $properties
	 * @return string
	 */
	public function widget($className, $properties = array()) {
		$c = Yii::app()->getController();
		return $c->widget($className, $properties, true);
	}

	/**
	 * @param string $class
	 * @param string $property
	 * @return mixed
	 */
	public function constGet($class, $property) {
		$c = new ReflectionClass($class);
		return $c->getConstant($property);
	}
}
