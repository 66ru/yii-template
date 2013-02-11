<?php

Yii::app()->getComponent('bootstrap')->register();

class AdminController extends Controller
{
    /**
     * @var string Name of managed model
     */
    public $modelName = '';
    /**
     * @var array Склонение должно соответствовать словам соответственно: (добавить .., редактирование .., список ..)
     */
    public $modelHumanTitle = array('модель', 'модели', 'моделей');

    /**
     * @var string Allowed actions. Separate by comma, without spaces. Possible values: add,view,edit,delete
     */
    public $allowedActions = 'add,edit,delete';

    /**
     * @var string One of AdminController->allowedActions
     */
    public $defaultAction = 'list';

    public function init()
    {
        parent::init();

        if (get_called_class() == __CLASS__) {
            $this->defaultAction = 'index';
        }
    }

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    public function accessRules()
    {
        $allowedActions = array_merge(explode(',', $this->allowedActions), array('index', 'list'));
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
        $this->render('/index');
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
            foreach ($_POST[$this->modelName] as &$postValue) {
                if (is_string($postValue)) {
                    $postValue = trim($postValue);
                    if ($postValue === '') {
                        $postValue = null;
                    }
                }
            }

            $this->beforeSetAttributes($model, $_POST[$this->modelName]);
            $model->setAttributes($_POST[$this->modelName]);
            foreach ($model->relations() as $relationName => $relationAttributes) {
                if (isset($_POST[$this->modelName][$relationName])) {
                    $model->$relationName = $_POST[$this->modelName][$relationName];
                }
            }
            $this->beforeSave($model);
            if ($model->save()) {
                $this->afterSave($model);
                $this->redirect(array($this->getId()));
            }
        }

        $this->beforeEdit($model);
        $this->render(
            '/crud/' . ($createNew ? 'add' : 'edit'),
            array(
                'model' => $model,
                'editFormElements' => $this->getEditFormElements($model),
            )
        );
    }

    public function actionView()
    {
        $model = $this->loadModel();

        $this->render(
            '/crud/view',
            array(
                'model' => $model,
                'editFormElements' => $this->getEditFormElements($model),
            )
        );
    }

    public function actionList()
    {
        /** @var $model CActiveRecord */
        $model = new $this->modelName('search');

        $this->beforeList($model, $_GET[$this->modelName]);
        if (isset($_GET[$this->modelName])) {
            $model->attributes = $_GET[$this->modelName];
        }

        $this->render(
            '/crud/list',
            array(
                'model' => $model,
                'columns' => $this->getTableColumns(),
                'canAdd' => in_array('add', explode(',', $this->allowedActions)),
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
     *     'attributeName2',
     * );
     * </code>
     * @return array
     */
    public function getTableColumns()
    {
        $model = CActiveRecord::model($this->modelName);
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
        $allowDelete = in_array('delete', $allowedActions);
        $allowView = in_array('view', $allowedActions);
        $allowEdit = in_array('edit', $allowedActions);

        $template = '';
        if (!$allowEdit && $allowView) {
            $template = '{view}';
        } elseif ($allowEdit) {
            $template = '{update}';
        }
        if ($allowDelete) {
            if (!empty($template)) {
                $template .= '&nbsp;&nbsp;&nbsp;{delete}';
            } else {
                $template = '{delete}';
            }
        }

        return array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => $template,
            'updateButtonUrl' => 'Yii::app()->controller->createUrl("edit",array("id"=>$data->primaryKey))'
        );
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
     */
    public function beforeSetAttributes($model, &$attributes)
    {
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
     */
    public function beforeSave($model)
    {
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
