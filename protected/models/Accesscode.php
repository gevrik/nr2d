<?php

/**
 * This is the model class for table "{{accesscode}}".
 *
 * The followings are the available columns in table '{{accesscode}}':
 * @property integer $id
 * @property integer $roomId
 * @property integer $userId
 * @property string $created
 * @property string $expires
 * @property integer $condition
 */
class Accesscode extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Accesscode the static model class
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
		return '{{accesscode}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('roomId, userId, created, expires, condition', 'required'),
			array('roomId, userId, condition', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, roomId, userId, created, expires, condition', 'safe', 'on'=>'search'),
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
			'room'=>array(self::BELONGS_TO, 'Room', 'roomId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'roomId' => 'Room',
			'userId' => 'User',
			'created' => 'Created',
			'expires' => 'Expires',
			'condition' => 'Condition',
		);
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
		$criteria->compare('roomId',$this->roomId);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('expires',$this->expires,true);
		$criteria->compare('condition',$this->condition);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}