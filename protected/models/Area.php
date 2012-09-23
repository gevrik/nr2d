<?php

/**
 * This is the model class for table "{{area}}".
 *
 * The followings are the available columns in table '{{area}}':
 * @property integer $id
 * @property integer $userId
 * @property string $created
 * @property string $name
 * @property string $accessCode
 * @property integer $level
 */
class Area extends CActiveRecord
{

	static $areaNameArray = array("EC","US","AC","LUNAR","ORBIT");

	static $corpNameArray = array("Anbiotone","Apfase","Basequote","Baseron","Betataxon","Bigcity","Biocom","Blacktaxon","Canein","Cankix","Caredintex","Carenix","Citytech","Concare","Dalt-Zone","Daltfind","Dalttone","Damcan","Danovedax","Dentobam","Dentotax","Dentozap","Domdom","Doncity","Dongla","Donjoyfax","Dontechi","Dripfintam","Dripice","E-Tone","Fase-Is","Faseice","Faseis","Faxlam","Freshdom","Freshlux","Freshremis","Fun-Techno","Fundom","Ganjahow","Ganjala","Ganzfix","Ganztexon","Geocanhow","Gogodax","Gogolux","Gold-Zap","Golden-Green","Goldendax","Goldlax","Goodin","Goodron","Greentax","Hat-Dax","Haydax","Haytechno","Hothotfind","Howtam","Icephase","Incity","It-Cane","Itdax","Ittechno","Jaybetafan","Jayhow","Jayquote","Joygreen","Joylane","Joysailex","K-Is","K-Tone","K-Zoom","Kaydox","Keyfax","Konin","Konkcore","Konline","Kontech","Lablane","Lablex","Ladontam","Lexidom","Mat-Lam","Mathlamdox","Matlab","Matlane","Mattechi","Medhatdax","Movecane","Newcan","Nimfind","O-Ice","O-Quodom","O-Techno","Ono-Sendai","Opecan","Physdom","Quadtone","Quoteron","Rankcan","Rankfase","Ranking","Redfax","Redtechi","Roncane","Ronfase","Rounddexon","Roundin","Runtex","Sailgreen","Sanla","Sansailing","Scotoveex","Siliconfix","Siliconquote","Silverline","Silverzone","Singlehow","Singlela","singlela","Soloing","Solotam","Solotech","Sonbetala","Sonlax","Spantom","Stim-ex","Stimremfax","Stimtechnology","Strongdex","Sumdax","Sumtexon","Sunkayzoom","Tamlux","Tamplax","Tamplax","Techdex","Techdox","Technodom","Technohow","Tessier-Ashpool","Tin-Tam","Tinquadit","Trantechnology","Trantone","Trestex","Trippletaxon","Tripplezap","Tristechi","Tristex","Trusttexon","U-Base","Uni-Ice","villacon","Villatechnology","Villazone","Vivaline","Vollux","Volnix","X-Lam","Yearcane","Zaamcore","Zaamzunlex","Zam-Care","Zamfix","Zamlab","Zamlux","Zamron","Zath-Ice","Zathbase","Zathlam","Zenin","Zoom-Lex","Zummacare","Zumzunzone","Zuntom");

	static $corpDeptArray = array("Biotech","Cybertech","Electronics","Research","Marketing","Development","Nanotech","Legal","Commercial","Public Relations","Internal Affairs","Security","Entertainment","Media","Services");

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Area the static model class
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
		return '{{area}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, created, name, accessCode, level', 'required'),
			array('userId, level', 'numerical', 'integerOnly'=>true),
			array('name, accessCode', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userId, created, name, accessCode, level', 'safe', 'on'=>'search'),
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
			'created' => 'Created',
			'name' => 'Name',
			'accessCode' => 'Access Code',
			'level' => 'Level',
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
		$criteria->compare('created',$this->created,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('accessCode',$this->accessCode,true);
		$criteria->compare('level',$this->level);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}



}