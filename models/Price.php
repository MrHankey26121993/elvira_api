<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "price".
 *
 * @property int $id
 * @property string $description
 * @property float $price
 * @property string $value
 * @property int $id_service
 */
class Price extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'price', 'value', 'id_service'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['id_service'], 'integer'],
            [['value'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'price' => 'Price',
            'value' => 'Value',
            'id_service' => 'Id Service',
        ];
    }
}
