<?php

/**
 * This is the model class for table "{{entity}}".
 *
 * The followings are the available columns in table '{{entity}}':
 * @property integer $id
 * @property integer $userId
 * @property integer $roomId
 * @property string $type
 * @property string $created
 * @property integer $attack
 * @property integer $defend
 * @property integer $stealth
 * @property integer $detect
 * @property integer $eeg
 * @property integer $x
 * @property integer $y
 * @property integer $credits
 */
class Entity extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Entity the static model class
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
		return '{{entity}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, roomId, type, created, attack, defend, stealth, detect, eeg, x, y, credits', 'required'),
			array('userId, roomId, attack, defend, stealth, detect, eeg, x, y, credits', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userId, roomId, type, created, attack, defend, stealth, detect, eeg, x, y, credits', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'userId' => 'User',
			'roomId' => 'Room',
			'type' => 'Type',
			'created' => 'Created',
			'attack' => 'Attack',
			'defend' => 'Defend',
			'stealth' => 'Stealth',
			'detect' => 'Detect',
			'eeg' => 'Eeg',
			'x' => 'X',
			'y' => 'Y',
			'credits' => 'Credits',
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
		$criteria->compare('userId',$this->userId);
		$criteria->compare('roomId',$this->roomId);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('attack',$this->attack);
		$criteria->compare('defend',$this->defend);
		$criteria->compare('stealth',$this->stealth);
		$criteria->compare('detect',$this->detect);
		$criteria->compare('eeg',$this->eeg);
		$criteria->compare('x',$this->x);
		$criteria->compare('y',$this->y);
		$criteria->compare('credits',$this->credits);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}