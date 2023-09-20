<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contacts".
 *
 * @property string|null $number
 * @property string|null $address
 * @property string|null $fc_link
 * @property string|null $inst_link
 */
class Contacts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'string', 'max' => 25],
            [['address', 'fc_link', 'inst_link'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'number' => 'Number',
            'address' => 'Address',
            'fc_link' => 'Fc Link',
            'inst_link' => 'Inst Link',
        ];
    }
}
