<?php

namespace app\controllers\api;

use app\models\Service;
use app\models\Slide;
use yii\rest\Controller;

class DataController extends Controller
{
    public function actionIndex() {
        $service = Service::find()->with('price')->asArray()->all();
        $slides = Slide::find()->all();
        return ['service' => $service, 'slides' => $slides];
    }
}