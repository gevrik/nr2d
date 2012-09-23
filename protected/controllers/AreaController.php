<?php

class AreaController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                    'actions'=>array('stats'),
                    'users'=>array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                    'actions'=>array('browse'),
                    'users'=>array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                    'actions'=>array('admin','delete','create','update','index','view', 'createRandom', 'resetAreas'),
                    'users'=>Yii::app()->getModule('user')->getAdmins(),
            ),
            array('deny',  // deny all users
                    'users'=>array('*'),
            ),
        );
    }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionResetAreas()
	{
		$users = User::model()->findAll();

		$area = new Area;
		$area->userId = 0;
		$area->created = date( 'Y-m-d H:i:s', time());
		$area->name = "The Chatsubo";
		$area->accessCode = md5(time());
		$area->level = 1;
		$area->save();

		$room = new Room;
		$room->areaId = $area->id;
		$room->userId = 0;
		$room->created = date( 'Y-m-d H:i:s', time());
		$room->type = 'io';
		$room->level = 1;
		$room->x = 0;
		$room->y = 0;
		$room->name = 'The Chatsubo Lobby';
		$room->description = 'The lobby of the legendary Chatsubo Internet Cafe in Chiba City, Tokyo.';
		$room->save();

		foreach ($users as $model) {
			// create user area
			$area = new Area;
			$area->userId = $model->id;
			$area->created = date( 'Y-m-d H:i:s', time());
			$area->name = ucfirst(CHtml::encode($model->username)) . "'s Home System";
			$area->accessCode = md5($model->id);
			$area->level = 1;
			$area->save();

			// create io port in area
			$room = new Room;
			$room->areaId = $area->id;
			$room->userId = $model->id;
			$room->created = date( 'Y-m-d H:i:s', time());
			$room->type = 'io';
			$room->level = 1;
			$room->x = 0;
			$room->y = 0;
			$room->name = ucfirst(CHtml::encode($model->username)) . "'s IO PORT";
			$room->description = 'A standard input-output port node.';
			$room->save();

			$model->profile->location = $room->id;
			$model->profile->homenode = $room->id;
			if ($model->profile->credits < 10000) {
				$model->profile->credits = 10000;
			}
			$model->profile->save(false);
		}

	}

	public function actionCreateRandom()
	{
		$area = new Area;
		$area->userId = 0;
		$area->created = date( 'Y-m-d H:i:s', time());
		$area->accessCode = 'aaa';
		$area->level = 1;

		srand();

		$totalCorpNames = count(Area::$corpNameArray);
		$randomCorpName = rand(0, $totalCorpNames - 1);
		$corpName = Area::$corpNameArray[$randomCorpName];

		$totalCorpNames = count(Area::$areaNameArray);
		$randomCorpName = rand(0, $totalCorpNames - 1);
		$corpArea = Area::$areaNameArray[$randomCorpName];

		$totalCorpNames = count(Area::$corpDeptArray);
		$randomCorpName = rand(0, $totalCorpNames - 1);
		$corpDept = Area::$corpDeptArray[$randomCorpName];

		$area->name = $corpName . ' ' . $corpDept . ' (' . $corpArea . ')';

		$area->save();

		$room = new Room;
		$room->areaId = $area->id;
		$room->userId = 0;
		$room->created = date( 'Y-m-d H:i:s', time());
		$room->level = $area->level;
		$room->x = 0;
		$room->y = 0;
		$room->type = 'io';
		$room->name = $area->name . ' (Lobby)';
		$room->description = 'The lobby of this corporate system.';

		$currentX = 0;
		$currentY = 0;
		$currentLevel = 1;

		$room->save();

		for ($x = 1; $x <= $area->level * 8; $x++) {

			srand();
			$randDir = rand(1, 100);
			if ($randDir > 50) {
				$currentX += 1;
			}
			else {
				$currentY += 1;
			}

			$randLevel = rand(1, 100);
			if ($randLevel > 75) {
				$currentLevel += 1;
			}

			if ($x == 1) {
				$roomType = 'firewall';
				$roomName = $area->name . ' (FW)';
				$roomDesc = 'A firewall node.';
			}
			else {
				$randType = rand(1, 6);
				if ($randType == 1) {
					$roomType = 'firewall';
					$roomName = $area->name . ' (FW)';
					$roomDesc = 'A firewall node.';
				}
				else if ($randType == 2) {
					$roomType = 'database';
					$roomName = $area->name . ' (DB)';
					$roomDesc = 'A database node.';
				}
				else if ($randType == 3) {
					$roomType = 'terminal';
					$roomName = $area->name . ' (TR)';
					$roomDesc = 'A terminal node.';
				}
				else if ($randType == 4) {
					$roomType = 'coproc';
					$roomName = $area->name . ' (CP)';
					$roomDesc = 'A coproc node.';
				}
				else if ($randType == 5) {
					$roomType = 'coding';
					$roomName = $area->name . ' (CD)';
					$roomDesc = 'A coding node.';
				}
				else if ($randType == 6) {
					$roomType = 'hacking';
					$roomName = $area->name . ' (HX)';
					$roomDesc = 'A hacking node.';
				}
			}

			$room = new Room;
			$room->areaId = $area->id;
			$room->userId = 0;
			$room->created = date( 'Y-m-d H:i:s', time());
			$room->level = $currentLevel;
			$room->x = $currentX;
			$room->y = $currentY;
			$room->name = $roomName;
			$room->type = $roomType;
			$room->description = $roomDesc;
			$room->save();

		}

	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Area;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Area']))
		{
			$model->attributes=$_POST['Area'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Area']))
		{
			$model->attributes=$_POST['Area'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Area');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Area('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Area']))
			$model->attributes=$_GET['Area'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Area::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='area-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
