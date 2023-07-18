<?php
namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class Bearer extends HttpBearerAuth
{
    public function handleFailure($response)
    {
        \Yii::$app->response->setStatusCode(401);
        return \Yii::$app->response->data = [
            'message' => 'You need autorization'
        ];
    }
}

class ApiController extends Controller
{
    public $enableCsrfValidation = false;
    public $param;

    public function init()
    {
        $this->param  = Yii::$app->request->post() ?: Yii::$app->request->get();
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
                'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
                'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            ],
        ];
    }

    public function behaviors() {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'] = [
            'class' =>  Bearer::className(),
        ];

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class
        ];
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }

    public function param($name = null)
    {
        $param  = Yii::$app->request->post() ?: Yii::$app->request->get();
        if ($name) {
            return $param[$name] ?? null;
        } else {
            return $param;
        }
    }
}
