<?php

Yii::app()->getComponent('bootstrap');

class MAdminController extends CExtController
{
    public $layout = false;

    /**
     * @var string Name of managed model
     */
    public $modelName = '';

    /**
     * @var array Склонение должно соответствовать словам соответственно: (добавить .., редактирование .., список ..)
     */
    public $modelHumanTitle = array('модель', 'модели', 'моделей');

    public $actionsTitles = array('Добавление', 'Редактирование', 'Список');

    public $buttonTitles = array('Добавить', 'Создать', 'Сохранить', 'Не создавать', 'Отменить изменения');

    /**
     * @var string|string[] Allowed actions. Array or comma separated string. Possible values: add,view,edit,delete
     */
    public $allowedActions = 'add,edit,delete';

    /**
     * @var string One of AdminController->allowedActions
     */
    public $defaultAction = 'list';

    /**
     * @var string view alias in actionList
     */
    public $listView = 'crud/list';
    /**
     * @var string view alias in actionAdd/actionEdit
     */
    public $editView = 'crud/edit';
    /**
     * @var string view alias in actionView
     */
    public $viewView = 'crud/view';

    /**
     * @var string
     */
    public $adminLayout = 'layouts/admin.twig';

    /**
     * @var array
     */
    public $additionalViewVariables = array();

    /**
     * @var string
     */
    public $assetsUrl;

    public function init()
    {
        parent::init();

        /** @var $app CWebApplication */
        $app = Yii::app();
        $this->assetsUrl = $app->assetManager->publish(__DIR__ . '/assets');

        $app->clientScript->registerCoreScript('jquery');

        $this->viewPath = __DIR__ . '/views';
        /** @var $yiiTwigRenderer ETwigViewRenderer */
        $yiiTwigRenderer = Yii::app()->getComponent('viewRenderer');
        /** @var $twig_LoaderInterface Twig_Loader_Filesystem */
        $twig_LoaderInterface = $yiiTwigRenderer->getTwig()->getLoader();
        $twig_LoaderInterface->addPath($this->viewPath);
    }

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    protected function getAllowedActions()
    {
        if (is_array($this->allowedActions)) {
            return $this->allowedActions;
        } else {
            $res = explode(',', $this->allowedActions);
            array_walk(
                $res,
                function (&$value) {
                    $value = trim($value);
                }
            );

            return $res;
        }
    }

    public function accessRules()
    {
        $allowedActions = array_merge($this->getAllowedActions(), array('index', 'list'));
        return array(
            array(
                'allow',
                'actions' => $allowedActions,
                'roles' => array('admin')
            ),
            array(
                'deny',
                'users' => array('*')
            ),
        );
    }

    public function actionIndex()
    {
        $this->render(
            'index',
            array(
                'adminLayout' => $this->adminLayout
            )
        );
    }

    public function actionAdd()
    {
        $this->actionEdit(true);
    }

    /**
     * @param bool $createNew
     */
    public function actionEdit($createNew = false)
    {
        if ($createNew) {
            $model = new $this->modelName();
        } else {
            $model = $this->loadModel();
        }

        if (isset($_POST[$this->modelName])) {
            if (isset($_POST['ajax'])) {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }

            foreach ($_POST[$this->modelName] as &$postValue) {
                if (is_string($postValue)) {
                    $postValue = trim($postValue);
                    if ($postValue === '') {
                        $postValue = null;
                    }
                }
            }
            unset($postValue);

            $attributesSetted = $this->beforeSetAttributes($model, $_POST[$this->modelName]);
            $model->setAttributes($_POST[$this->modelName]);
            foreach ($model->relations() as $relationName => $relationAttributes) {
                if (isset($_POST[$this->modelName][$relationName])) {
                    $model->$relationName = $_POST[$this->modelName][$relationName];
                }
            }
            $validated = $model->validate();
            if ($attributesSetted && $validated && $this->beforeSave($model) && $model->save(false)) {
                $this->afterSave($model);
                $this->redirect(array('/' . $this->getUniqueId()));
            }
        }

        $this->beforeEdit($model);
        $this->render(
            $this->editView,
            array_merge_recursive(
                array(
                    'adminLayout' => $this->adminLayout,
                    'model' => $model,
                    'editFormElements' => $this->getEditFormElements($model),
                ),
                $this->additionalViewVariables
            )
        );
    }

    /**
     * @param CActiveRecord $model
     * @param $attributes
     */
    protected function setSearchAttributes($model, $attributes)
    {
        $modelName = get_class($model);
        if (array_key_exists($modelName, $attributes)) {
            $model->setAttributes($attributes[$modelName]);
            unset($attributes[$modelName]);
        }

        if (!empty($attributes)) {
            foreach ($model->relations() as $relationName => $relationAttributes) {
                $relationModelName = $relationAttributes[1];
                if (array_key_exists($relationModelName, $attributes)) {
                    $model->$relationName = new $relationModelName();
                    $model->$relationName->setAttributes($attributes[$relationModelName]);
                    unset($attributes[$relationModelName]);
                }
            }
        }
        if (!empty($attributes)) {
            foreach ($model->relations() as $relationName => $relationAttributes) {
                if (!empty($model->$relationName) && $model->$relationName instanceof CActiveRecord) {
                    $this->setSearchAttributes($model->$relationName, $attributes);
                }
            }
        }
    }

    public function actionView()
    {
        $model = $this->loadModel();

        $this->render(
            $this->viewView,
            array_merge_recursive(
                array(
                    'adminLayout' => $this->adminLayout,
                    'model' => $model,
                    'editFormElements' => $this->getEditFormElements($model),
                ),
                $this->additionalViewVariables
            )
        );
    }

    public function actionList()
    {
        Yii::import('ext.*');

        /** @var $model CActiveRecord */
        $model = new $this->modelName('search');

        $this->beforeList($model, $_GET[$this->modelName]);
        if (is_null($_GET[$this->modelName])) {
            unset($_GET[$this->modelName]);
        }
        $this->setSearchAttributes($model, $_GET);

        $this->render(
            $this->listView,
            array_replace_recursive(
                array(
                    'adminLayout' => $this->adminLayout,
                    'model' => $model,
                    'dataProvider' => $model->search(),
                    'advancedFilters' => $this->getAdvancedFilters(),
                    'columns' => $this->getTableColumns($model),
                    'canAdd' => in_array('add', explode(',', $this->allowedActions)) && $this->isActionAllowed('add'),
                ),
                $this->additionalViewVariables
            )
        );
    }

    public function actionDelete()
    {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel()->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax'])) {
                $this->redirect(array($this->getId()));
            }
        } else {
            throw new CHttpException(400);
        }
    }

    /**
     * internal
     * @return CActiveRecord
     * @throws CHttpException
     */
    public function loadModel()
    {
        $model = null;
        if (isset($_GET['id'])) {
            $model = CActiveRecord::model($this->modelName)->findbyPk($_GET['id']);
        }
        if ($model === null) {
            throw new CHttpException(404);
        } else {
            return $model;
        }
    }

    /**
     * Example:
     * <code>
     * return array(
     *     'attributeName1',
     *     array(
     *         'class' => 'alias.to.children.of.CGridColumn',
     *         'property1' => 'value',
     *         'property2' => 'value',
     *     ),
     *     array(
     *         'name' => 'attributeName2',
     *         'filter' => array(1 => 'Value 1', 2 => 'Value 2'),
     *         'sortable' => false,
     *         'value' => '$data->getHumanAttributeText()',
     *     ),
     * );
     * </code>
     * @see CGridView::$columns
     * @param CActiveRecord $model
     * @return array
     */
    public function getTableColumns($model)
    {
        $attributes = $model->getAttributes();
        unset($attributes[$model->metaData->tableSchema->primaryKey]);
        $columns = array_keys($attributes);

        $columns[] = $this->getButtonsColumn();

        return $columns;
    }

    /**
     * internal
     * returns columns with buttons such as edit, view, delete
     * @return array
     */
    public function getButtonsColumn()
    {
        $allowedActions = explode(',', $this->allowedActions);
        $allowDelete = in_array('delete', $allowedActions) && $this->isActionAllowed('delete');
        $allowView = in_array('view', $allowedActions) && $this->isActionAllowed('view');
        $allowEdit = in_array('edit', $allowedActions) && $this->isActionAllowed('edit');

        $template = '';
        if (!$allowEdit && $allowView) {
            $template = '{view}';
        } elseif ($allowEdit) {
            $template = '{update}';
        }
        if ($allowDelete) {
            if (!empty($template)) {
                $template .= '{delete}';
            } else {
                $template = '{delete}';
            }
        }

        return array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => $template,
            'updateButtonUrl' => 'Yii::app()->controller->createUrl("edit",array("id"=>$data->primaryKey))',
            'deleteConfirmation' => "Вы действительно хотите удалить {$this->modelHumanTitle[0]}?",
        );
    }

    public function isActionAllowed($mAdminActionName)
    {
        if (method_exists(Yii::app()->controller, 'accessRules')) {
            /** @var CHttpRequest $request */
            $request = Yii::app()->request;
            $verb = $request->getRequestType();
            $ip = $request->getUserHostAddress();
            $action = new CInlineAction(Yii::app()->controller, $mAdminActionName);

            foreach (Yii::app()->controller->accessRules() as $rule) {
                if (is_array($rule) && isset($rule[0])) {
                    $r = new CAccessRule;
                    $r->allow = $rule[0] === 'allow';
                    foreach (array_slice($rule, 1) as $name => $value) {
                        if ($name === 'expression' || $name === 'roles') {
                            $r->$name = $value;
                        } else {
                            $r->$name = array_map('strtolower', $value);
                        }
                    }

                    $allow = $r->isUserAllowed(Yii::app()->user, Yii::app()->controller, $action, $ip, $verb);
                    if ($allow > 0) {
                        return true;
                    } elseif ($allow < 0) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @return array Format according to CForm $config
     * @see CForm::__construct
     */
    public function getAdvancedFilters()
    {
        return array();
    }

    /**
     * Example:
     * <code>
     * return array(
     *      'attributeName1' => array(
     *          'type' => 'textField', // One of methods in TbActiveForm::*Row
     *      ),
     *      array(
     *          'class' => 'yii.class.alias', // i.e. application.extensions.DependedInputWidget
     *          'attribute1' => 'value1',
     *          'attribute2' => 'value2',
     *      ),
     *      '<h1>Raw html</h1>',
     *      'attributeName2' => array(
     *          'class' => 'yii.class.alias2',
     *      ),
     *      'attributeName3' => array(
     *          'type' => 'dropDownList', // Will be called TbActiveForm::DropDownListRow
     *          'data' => CHtml::listData(Client::model()->findAll(), 'id', 'name'),
     *          'htmlOptions' => array(
     *              'empty' => 'Empty',
     *          ),
     *      ),
     *      'relationName' => array(
     *          'rows' => array( same structure as root array ),
     *      ),
     *  );
     * </code>
     *
     * @param CActiveRecord $model
     * @return array
     */
    public function getEditFormElements($model)
    {
        return array();
    }

    /**
     * @param CActiveRecord $model
     * @param array $attributes
     * @return bool
     */
    public function beforeSetAttributes($model, &$attributes)
    {
        return true;
    }

    /**
     * @param CActiveRecord $model
     * @param array $attributes
     */
    public function beforeList($model, &$attributes)
    {
    }

    /**
     * @param CActiveRecord $model
     * @return bool
     */
    public function beforeSave($model)
    {
        return true;
    }

    /**
     * @param CActiveRecord $model
     */
    public function afterSave($model)
    {
    }

    /**
     * @param CActiveRecord $model
     */
    public function beforeEdit($model)
    {
    }
}
