<?php

/*function logPrefix() {
	if (Yii::$app === null) {
		return '';
	}
	$request = Yii::$app->getRequest();
	$ip = $request instanceof Request ? $request->getUserIP() : '-';
	$uid = isset(Yii::$app->user->id) ? Yii::$app->user->id : '0';
	$name = isset(Yii::$app->user->name) ? Yii::$app->user->name : 'guest';
	$controller = Yii::$app->controller->id;
	$action = Yii::$app->controller->action->id;
    return "[$ip][$name($uid)][$controller/$action]";

}*/

$log_config = [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'app\lib\common\BaseFileTarget',
            'levels' => ['error'],
            'categories' => ['application', 'access'],
            'logFile' => '@runtime/logs/error.log',
            'enableRotation' => true,
            'logVars' => [],
            'rotateByCopy' => false,
            #'prefix' => logPrefix(),
            'maxLogFiles' => 10,
        ],
        [
            'class' => 'app\lib\common\BaseFileTarget',
            'levels' => ['warning'],
            'categories' => ['application'],
            'logFile' => '@runtime/logs/warning.log',
            'enableRotation' => true,
            'logVars' => [],
            'rotateByCopy' => false,
            #'prefix' => logPrefix(),
            'maxLogFiles' => 10,
        ],
        [
            'class' => 'app\lib\common\BaseFileTarget',
            'levels' => ['info'],
            'categories' => ['application'],
            'logFile' => '@runtime/logs/info.log',
            'enableRotation' => true,
            'logVars' => [],
            'rotateByCopy' => false,
            #'prefix' => logPrefix(),
            'maxLogFiles' => 1,
        ],
        [
            #'class' => 'yii\log\FileTarget',
            'class' => 'app\lib\common\BaseFileTarget',
            'levels' => ['info'],
            'logVars' => ['_SERVER', '_POST'],
            'categories' => ['access'],
            'logFile' => '@runtime/logs/access.log',
            'enableRotation' => true,
            'rotateByCopy' => false,
            #'prefix' => logPrefix(),
            'maxLogFiles' => 1,
		],
        [
            'class' => 'app\lib\common\BaseFileTarget',
            'levels' => ['trace'],
            'logVars' => [],
            'categories' => ['application'],
            'logFile' => '@runtime/logs/trace.log',
            'enableRotation' => true,
            'rotateByCopy' => false,
            #'prefix' => logPrefix(),
            'maxLogFiles' => 10,
        ],
        [
            'class' => 'app\lib\common\BaseFileTarget',
            'levels' => ['profile'],
            'logVars' => [],
            'categories' => ['application'],
            'logFile' => '@runtime/logs/profile.log',
            'enableRotation' => true,
            'rotateByCopy' => false,
            #'prefix' => logPrefix(),
            'maxLogFiles' => 10,
        ],
    ],
];

return $log_config;
