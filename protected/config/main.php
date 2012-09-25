<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'NETRUNNERS',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.user.models.*',
        'application.modules.user.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'temp123',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
		'user'=>array(
            # encrypting method (php hash function)
            'hash' => 'md5',
 
            # send activation email
            'sendActivationMail' => true,
 
            # allow access for non-activated users
            'loginNotActiv' => false,
 
            # activate user on registration (only sendActivationMail = false)
            'activeAfterRegister' => false,
 
            # automatically login from registration
            'autoLogin' => true,
 
            # registration path
            'registrationUrl' => array('/user/registration'),
 
            # recovery password path
            'recoveryUrl' => array('/user/recovery'),
 
            # login form path
            'loginUrl' => array('/user/login'),
 
            # page after login
            'returnUrl' => array('/site/index'),
 
            # page after logout
            'returnLogoutUrl' => array('/user/login'),
        ),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'class' => 'WebUser',
            'allowAutoLogin'=>true,
            'loginUrl' => array('/user/login'),
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'p'=>'room/play',
				'l'=>'user/login',
				'r'=>'user/registration',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'db'=>array(
			//'connectionString' => 'mysql:host=localhost;dbname=nr2d',
			'connectionString' => 'mysql:host=localhost;port=8889;dbname=nr2d',
			'emulatePrepare' => true,
			'username' => 'nr2d',
			'password' => 'ARtUVQBy7QKWCvHX',
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		),

		'facebook'=>array(
		    'class' => 'ext.yii-facebook-opengraph.SFacebook',
		    'appId'=>'117253538343456', // needed for JS SDK, Social Plugins and PHP SDK
		    'secret'=>'9ce3164e32aef6f7720637d750432580', // needed for the PHP SDK 
		    //'locale'=>'en_US', // override locale setting (defaults to en_US)
		    //'jsSdk'=>true, // don't include JS SDK
		    //'async'=>true, // load JS SDK asynchronously
		    //'jsCallback'=>false, // declare if you are going to be inserting any JS callbacks to the async JS SDK loader
		    //'status'=>true, // JS SDK - check login status
		    //'cookie'=>true, // JS SDK - enable cookies to allow the server to access the session
		    //'oauth'=>true,  // JS SDK - enable OAuth 2.0
		    //'xfbml'=>true,  // JS SDK - parse XFBML / html5 Social Plugins
		    //'frictionlessRequests'=>true, // JS SDK - enable frictionless requests for request dialogs
		    //'html5'=>true,  // use html5 Social Plugins instead of XFBML
		    //'ogTags'=>array(  // set default OG tags
		        //'title'=>'MY_WEBSITE_NAME',
		        //'description'=>'MY_WEBSITE_DESCRIPTION',
		        //'image'=>'URL_TO_WEBSITE_LOGO',
		    //),
		  ),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'admin@totalmadownage.com',
	),
);