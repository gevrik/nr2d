<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{

	/* SEO Vars */
    public $pageTitle = 'Netrunners';
    public $pageDesc = 'Netrunners is a 2D multiplayer-game set in the near future where you play a hacker in Cyberspace. Heavily influenced by many works of the Cyberpunk genre.';
    public $pageRobotsIndex = true;

    public $pageOgTitle = 'Netrunners';
    public $pageOgDesc = 'Netrunners is a 2D multiplayer-game set in the near future where you play a hacker in Cyberspace. Heavily influenced by many works of the Cyberpunk genre.';
    public $pageOgImage = 'http://www.totalmadownage.com/images/tmocom_og.png';

	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	public $information=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function display_seo()
    {
        // STANDARD TAGS
        // -------------------------
        // Title/Desc
        echo "\t".''.PHP_EOL;
        echo "\t".'<meta name="description" content="',CHtml::encode($this->pageDesc),'">'.PHP_EOL;

        // Option for NoIndex
        if ( $this->pageRobotsIndex == false ) {
            echo '<meta name="robots" content="noindex">'.PHP_EOL;
        }

        // OPEN GRAPH(FACEBOOK) META
        // -------------------------
        if ( !empty($this->pageOgTitle) ) {
            echo "\t".'<meta property="og:title" content="',CHtml::encode($this->pageOgTitle),'">'.PHP_EOL;
        }
        if ( !empty($this->pageOgDesc) ) {
            echo "\t".'<meta property="og:description" content="',CHtml::encode($this->pageOgDesc),'">'.PHP_EOL;
        }
        if ( !empty($this->pageOgImage) ) {
            echo "\t".'<meta property="og:image" content="',$this->pageOgImage,'">'.PHP_EOL;
        }
    }

    protected function afterRender($view, &$output)
    {
        parent::afterRender($view,$output);
        //Yii::app()->facebook->addJsCallback($js); // use this if you are registering any $js code you want to run asyc
        Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
        Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags
        return true;
    }
    
}