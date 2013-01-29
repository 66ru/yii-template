<?php

class TreeHelper
{
    public static function makeTree($root, $inputs)
    {
        // обращаем отношение: из Child->Parent делаем Parent->*Child

        // лес деревьев для каждого элемента
        $nodes = array(); // id => array(...)

        // сперва каждому элементу присваиваем пустой лес
        foreach ($inputs as $child => $parent) {
            $nodes[$child] = array();
            $nodes[$parent] = array();
        }

        // затем добавляем пару (элемент => ссылка на его лес) в лес родителя
        foreach ($inputs as $child => $parent) {
            $nodes[$parent][$child] = & $nodes[$child];
        }

        // и, наконец, создаём дерево - корневой элемент и его лес или если не можем построить корректное дерево
        // вернем пустой результат
        $tree = isset($nodes[$root]) ? array($root => $nodes[$root]) : array($root => array());

        return $tree;
    }

    private static function internalGetTreeForDropDownBox($tree, $categoriesArray, $canSelectRoots, &$disabledOptionsArray, $nestingLevel = 0)
    {
        $result = array();
        foreach ($tree as $id => $childrens) {
            if ($canSelectRoots)
                $result[$id] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel) . CHtml::encode($categoriesArray[$id]);
            else if (!$canSelectRoots && $nestingLevel == 1)
                $result[$id] = CHtml::encode($categoriesArray[$id]);
            else if (!$canSelectRoots && $nestingLevel > 1) {
                $result[$id] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nestingLevel - 2) . CHtml::encode($categoriesArray[$id]);
                $disabledOptionsArray[] = $id;
            }

            if (is_array($childrens) && !empty($childrens)) {
                $childResult = self::internalGetTreeForDropDownBox($childrens, $categoriesArray, $canSelectRoots, $disabledOptionsArray, $nestingLevel + 1);
                if ($canSelectRoots || $nestingLevel > 1)
                    $result = $result + $childResult;
                else
                    $result[CHtml::encode($categoriesArray[$id])] = $childResult;
            }
        }

        return $result;
    }

    public static function getTreeForDropDownBox($models, $canSelectRoots = false, $fieldNames = array('id' => 'id', 'name' => 'name', 'parentId' => 'parentId'))
    {
        $categoriesArray = CHtml::listData($models, $fieldNames['id'], $fieldNames['name']);
        $tree = TreeHelper::makeTree(null, CHtml::listData($models, $fieldNames['id'], $fieldNames['parentId']));

        $disabledOptionsArray = array();
        $result = self::internalGetTreeForDropDownBox($tree[null], $categoriesArray, $canSelectRoots, $disabledOptionsArray);

        if ($canSelectRoots)
            return $result;
        else
            return array($result, $disabledOptionsArray);
    }
}
