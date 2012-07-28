<?php

Yii::app()->getComponent('bootstrap');

class AdminController extends Controller
{
	public $modelName = null;
	/**
	 * @var array Склонение должно соответствовать словам соответственно: (добавить .., редактирование .., список ..)
	 */
	public $modelHumanTitle = array('модель','модели','моделей');

	public $defaultAction = 'list';

	public function filters()
	{
		return array(
			'accessControl'
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'roles'=>array('admin')
			),
			array('deny',
				'users'=>array('*')
			),
		);
	}

	public function actionAdd() {
		$this->actionEdit(true);
	}

	public function actionEdit($createNew = null) {
		if ($createNew) {
			$model = new $this->modelName();
		} else {
			$model = $this->loadModel();
		}
		$model->scenario = 'edit';

		if(isset($_POST[$this->modelName]))
		{
			$model->attributes=$_POST[$this->modelName];
			$model->scenario = 'save';
			$this->beforeSave($model);
			if($model->save())
				$this->redirect(array($this->getId()));
		}

		$this->beforeEdit($model);
		$this->render('//admin/crud/'.($createNew ? 'add' : 'edit'), array(
			'model' => $model,
			'editFormElements' => $this->getEditFormElements($model),
		));
	}

	public function loadModel() {
		$model = null;
		if (isset($_GET['id']))
			$model = CActiveRecord::model($this->modelName)->findbyPk($_GET['id']);
		if ($model === null)
			throw new CHttpException(404);
		return $model;
	}

	public function actionIndex() {
		$this->render('//admin/index');
	}

	public function actionList() {
		/** @var $model CActiveRecord */
		$model=new $this->modelName('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->modelName]))
			$model->attributes=$_GET[$this->modelName];

		$this->render('//admin/crud/list', array(
			'model' => $model,
			'columns' => $this->getTableColumns(),
		));
	}

	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array($this->getId()));
		}
		else
			throw new CHttpException(400);
	}

	public function getTableColumns() {
		$model = CActiveRecord::model($this->modelName);
		$attributes = $model->getAttributes();
		unset($attributes[ $model->metaData->tableSchema->primaryKey ]);
		$columns = array_keys($attributes);

		$columns[] = $this->getButtonsColumn();

		return $columns;
	}

	public function getButtonsColumn() {
		return array(
			'class' => 'bootstrap.widgets.BootButtonColumn',
			'template' => '{update}&nbsp;&nbsp;&nbsp;{delete}',
			'updateButtonUrl' => 'Yii::app()->controller->createUrl("edit",array("id"=>$data->primaryKey))'
		);
	}

	/**
	 * @param CActiveRecord $model
	 * @return array
	 */
	public function getEditFormElements($model) {
		return array();
	}

	/**
	 * @param CActiveRecord $model
	 */
	public function beforeSave($model) {}
	/**
	 * @param CActiveRecord $model
	 */
	public function afterSave($model) {}
	/**
	 * @param CActiveRecord $model
	 */
	public function beforeEdit($model) {}
}
