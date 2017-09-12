<?php
/**
 * Created by mirjalol.
 * Date: 9/8/2017
 * Time: 4:56 PM
 */

namespace murodov20\redactor\entities;


use yii\db\ActiveRecord;

/**
 * Class MPhoto
 * @property integer $id
 * @property string $filename
 * @property string $alt
 */
class MPhoto extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%m_photo}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['filename'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string Alt text for image
     * If you want to add new field for alt, extend this class and override this method
     */
    public function getAlt()
    {
        return $this->filename;
    }

}
