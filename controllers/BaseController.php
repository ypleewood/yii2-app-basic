<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class BaseController extends Controller
{
	public $layout = "main";


	public function beforeAction($action) {
		Yii::beginProfile('REQUEST_BLOCK');
		$request_id = strval(time()) . strval(rand(10000, 99999));
		defined('BASE_REQUEST_ID') or define('BASE_REQUEST_ID', $request_id); 

		Yii::info('--------------------- request id:'.$request_id.' ----------------------', "access");
		if(!parent::beforeAction($action)) return false;
		return parent::beforeAction($action);
	}

	public function afterAction($action, $result) {
		$result = parent::afterAction($action, $result);

		#Yii::info('request result'.json_encode($result,  JSON_UNESCAPED_UNICODE), 'access');
		Yii::endProfile('REQUEST_BLOCK');

		return $result;
	}

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

}
