<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%m_photo}}`.
 */
class m170908_120426_create_m_photo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%m_photo}}', [
            'id' => $this->primaryKey(),
            'filename' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%m_photo}}');
    }
}
