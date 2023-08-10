<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $login
 * @property string|null $pass
 * @property string|null $token
 */
class User extends \yii\db\ActiveRecord  implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['login', 'pass', 'token'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->where(['token' => $token])
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'pass' => 'Pass',
            'token' => 'Token',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }
}
