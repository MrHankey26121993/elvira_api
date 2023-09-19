<?php

namespace app\controllers\api;

use app\models\Price;
use app\models\Service;
use app\models\Slide;
use app\models\User;
use app\models\Works;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class Bearer extends HttpBearerAuth
{
    public function handleFailure($response)
    {
        $test = User::find()->all();
        \Yii::$app->response->setStatusCode(401);

        return \Yii::$app->response->data = [
            'message' => 'You need autorization',
            'message1' => \Yii::$app->request->getHeaders()
        ];
    }
}

class DataController extends Controller
{

    public $param;

    public function init()
    {
        $this->param = \Yii::$app->request->post() ?: \Yii::$app->request->get();
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            echo $action;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return true;
        }

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

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'] = [
            'class' => Bearer::className(),
        ];

        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)


        unset($behaviors['rateLimiter']);

        $behaviors['authenticator']['except'] = [
            'options',
            'login',
            'index',
            'content',
            'form',

        ];


        return $behaviors;
    }

    public function actionContent()
    {
        $service = Service::find()->with('price')->asArray()->all();
        $slides = Slide::find()->all();
        $works = Works::find()->all();
        return ['service' => $service, 'slides' => $slides, 'works' => $works];
    }

    public function actionLogin()
    {
        try {

            $data = $this->param;

            $userModel = User::find()->where(['login' => $data['username']])->one();

            if ($userModel) {
                if (\Yii::$app->security->validatePassword($data['password'], $userModel->pass)) {
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
            return $e;
        } catch (\ErrorException $e) {
            return $e;
        }
    }

    public function actionSlide()
    {

        $data = $this->param;
        if (isset($data['id'])) {
            $model = Slide::find()->where(['id' => $data['id']])->one();
        } else {
            $model = new Slide();
        }


        $model->load($data, '');

        if (strripos($data['img'], 'uploads/') === false) {
            $nameFileDesk = strtolower(trim($data['title']));
            $nameFileDesk = str_replace(' ', '_', $nameFileDesk) . ".png";
            $relativePathDesk = 'uploads/img/' . $nameFileDesk;
            $this->base64_to_jpeg($data['img'], $relativePathDesk);
            $model->img = $relativePathDesk;
        }
        if (strripos($data['img_mob'], 'uploads/') === false) {
            $nameFileMob = strtolower(trim($data['title'])) . '_mob';
            $nameFileMob = str_replace(' ', '_', $nameFileMob) . ".png";
            $relativePath = 'uploads/img/' . $nameFileMob;
            $this->base64_to_jpeg($data['img_mob'], $relativePath);
            $model->img_mob = $relativePath;
        }

        $model->save(false);

        return $model;
    }

    public function actionRemoveSlide()
    {
        $model = Slide::find()->where(['id' => $this->param['id']])->one();
        if ($model->delete()) {
            return true;
        }
        return false;
    }

    public function actionRemoveService()
    {
        $model = Service::find()->where(['id' => $this->param['id']])->one();
        $modelPrice = Price::find()->where(['id_service' => $model->id])->all();
        foreach ($modelPrice as $price) {
            $price->delete();
        }
        if ($model->delete()) {
            return true;
        }
        return false;
    }

    public function actionRemoveWork()
    {
        $model = Works::find()->where(['id' => $this->param['id']])->one();
        if ($model->delete()) {
            return true;
        }
        return false;
    }

    public function actionService()
    {
        $data = $this->param;
        if (isset($data['id'])) {
            $model = Service::find()->where(['id' => $data['id']])->one();
        } else {
            $model = new Service();
        }
        $model->load($data, '');
        $model->save(false);
        if (count($data['price']) > 0) {
            $oldPricies = Price::find()->where(['id_service' => $model->id])->all();
            foreach ($oldPricies as $oldPrice) {
                $oldPrice->delete();
            }
            foreach ($data['price'] as $price) {
                $newPrice = new Price();
                $newPrice->load($price, '');
                $newPrice->id_service = $model->id;
                $newPrice->save(false);
            }
        }
        return Service::find()->where(['id' => $model->id])->with('price')->asArray()->one();

    }

    public function actionWorks()
    {
        $data = $this->param;
        if (isset($data['id'])) {
            $model = Works::find()->where(['id' => $data['id']])->one();
        } else {
            $model = new Works();
        }


        $date = new \DateTime('now');
        $name = strtotime($date->format('Y-m-d H:m:i')) . ".png";

        if (strripos($data['img'], 'uploads/') === false) {
            $relativePathDesk = 'uploads/img/' . $name;
            $this->base64_to_jpeg($data['img'], $relativePathDesk);
            $model->img = $relativePathDesk;
        }
        if ($model->save()) {
            return $model;
        }
        return false;
    }

    public function base64_to_jpeg($base64_string, $output_file)
    {
        $ifp = fopen($output_file, "w+");
        $data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
    }

    public function actionForm()
    {

        $name = $this->param['name'];
        $number = $this->param['number'];
        $email = $this->param['email'];
        $comment = $this->param['comment'];

        $text = "<strong>Vous avez une nouvelle demande de contact!</strong><br><p>
        Nom prénom: $name
        <br>
        Téléphone: $number
        <br>
        Email: $email
        <br>
        Votre demande: $comment
        </p>";

        \Yii::$app->mailer->compose()
            ->setFrom('no-repeat@elvirabeauty.fr')
            ->setTo('elvirabeautypmu@gmail.com')
            ->setSubject('Vous avez une nouvelle demande de contact!')
            ->setHtmlBody($text)
            ->send();
        return $this->param;
    }
}