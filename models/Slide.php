<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "slide".
 *
 * @property int $id
 * @property int $order
 * @property string $description
 * @property string $img
 */
class Slide extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'slide';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order', 'description', 'img'], 'required'],
            [['order'], 'integer'],
            [['description'], 'string'],
            [['img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'description' => 'Description',
            'img' => 'Img',
        ];
    }
}
