<?php

class ExtendedHtml
{
	/**
	 * Generates a valid HTML ID based for a model attribute.
	 * Note, the attribute name may be modified after calling this method if the name
	 * contains square brackets (mainly used in tabular input) before the real attribute name.
	 * @param CModel $model the data model
	 * @param string $attribute the attribute
	 * @return string the ID generated based on name.
	 */
	public static function resolveId($model, $attribute) {
		return CHtml::getIdByName(Chtml::resolveName($model, $attribute));
	}
}
