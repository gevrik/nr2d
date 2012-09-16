<?php

/**
 * This is the model class for table "{{room}}".
 *
 * The followings are the available columns in table '{{room}}':
 * @property integer $id
 * @property integer $areaId
 * @property integer $userId
 * @property string $created
 * @property string $type
 * @property integer $level
 * @property integer $x
 * @property integer $y
 * @property string $name
 * @property string $description
 */
class Room extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Room the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{room}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('areaId, userId, created, type, level, x, y, name, description', 'required'),
			array('areaId, userId, level, x, y', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>8),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, areaId, userId, created, type, level, x, y, name, description', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user'=>array(self::BELONGS_TO, 'User', 'userId'),
			'entityAmount'=>array(self::STAT, 'Entity', 'roomId'),
			'entities'=>array(self::HAS_MANY, 'Entity', 'roomId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'areaId' => 'Area',
			'userId' => 'User',
			'created' => 'Created',
			'type' => 'Type',
			'level' => 'Level',
			'x' => 'X',
			'y' => 'Y',
			'name' => 'Name',
			'description' => 'Description',
		);
	}

	public function getExit($direction)
	{
		if ($direction == 'north') {
			$targetY = $this->y + 1;
			$targetX = $this->x;
		}

		if ($direction == 'east') {
			$targetY = $this->y;
			$targetX = $this->x + 1;
		}

		if ($direction == 'south') {
			$targetY = $this->y - 1;
			$targetX = $this->x;
		}

		if ($direction == 'west') {
			$targetY = $this->y;
			$targetX = $this->x - 1;
		}

		$criteria = new CDbCriteria;
		$criteria->condition = 'x = :x AND y = :y AND areaId = :areaId';
		$criteria->params = array(':x' => $targetX, ':y' => $targetY, ':areaId' => $this->areaId);

		$targetRoom = Room::model()->find($criteria);

		return ($targetRoom) ? $targetRoom->id : 0;

	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('areaId',$this->areaId);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('x',$this->x);
		$criteria->compare('y',$this->y);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}