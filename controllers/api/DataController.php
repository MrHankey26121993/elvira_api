<?php

namespace app\controllers\api;

use app\models\Service;
use app\models\Slide;
use app\models\User;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
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

class DataController extends Controller
{

    public $param;

    public function init()
    {
        $this->param  = \Yii::$app->request->post() ?: \Yii::$app->request->get();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'] = [
            'class' => Bearer::className(),
        ];

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class
        ];
        unset($behaviors['rateLimiter']);

        $behaviors['authenticator']['except'] = [
            'login',
            'index',
            'content'
        ];
        return $behaviors;
    }


    public function actionContent()
    {
        $service = Service::find()->with('price')->asArray()->all();
        $slides = Slide::find()->all();
        return ['service' => $service, 'slides' => $slides];
    }

    public function actionLogin()
    {
        try {

            $data = $this->param;

            $userModel = User::find()->where(['login' => $data['username']])->one();

            if($userModel) {
                if(\Yii::$app->security->validatePassword($data['password'], $userModel->pass)) {
                    return ArrayHelper::merge(
                        ['response' => ['access_token' => $userModel->token]],
                        ['message' => 'Авторизация успешна'],
                        ['error' => false]
                    );
                } else {
                    return ArrayHelper::merge(
                        ['response' => []],
                        ['message' => 'Неверный логин или пароль'],
                        ['error' => true]
                    );
                }
            } else {
                $newUser = new User();
                $newUser->login = $data['username'];
                $newUser->pass = \Yii::$app->security->generatePasswordHash($data['password']);
                $newUser->token = \Yii::$app->security->generateRandomString();

                $newUser->save(false);

                return ArrayHelper::merge(
                    ['response' => ['access_token' => $newUser->token]],
                    ['message' => 'Авторизация успешна'],
                    ['error' => false]
                );
            }



        } catch (yii\web\HttpException $e) {
            return Util::returnInfo($e, 'authorization_error', true);
        } catch (\ErrorException $e) {
            return Util::returnInfo([], 'authorization_error', true);
        }
    }

    public function actionSlide() {
        return $this->param;
    }
}