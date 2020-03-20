<?php

use yii\db\Migration;

/**
 * Class m200314_161944_media
 */
class m200314_161944_media extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%media}}', [

            'id' => $this->bigPrimaryKey(),
            'cat_id' => $this->integer(11)->defaultValue(1),

            'name' => $this->string(128)->notNull(),
            'alias' => $this->string(128)->notNull(),

            'path' => $this->string(255)->notNull(),

            'title' => $this->string(255),
            'caption' => $this->string(550),
            'alt' => $this->string(255),
            'description' => $this->text(),

            'mime_type' => $this->string(128)->null(),
            'size' => $this->integer(19),

            'params' => $this->text(),
            'reference' => $this->string(255)->null(),

            'status' => $this->tinyInteger(1)->null()->defaultValue(1),

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11)->null(),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11)->null(),

        ], $tableOptions);

        $this->createIndex('{{%idx-media}}', '{{%media}}', ['name', 'path', 'cat_id', 'reference']);
        $this->createIndex('{{%idx-media-alias}}', '{{%media}}', ['name', 'alias']);
        $this->createIndex('{{%idx-media-status}}', '{{%media}}', ['alias', 'status']);

        $this->addForeignKey(
            'fk_media_to_cats',
            '{{%media}}',
            'cat_id',
            '{{%media_cats}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        // If exist module `Users` set foreign key `created_by`, `updated_by` to `users.id`
        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx-media-created}}','{{%media}}', ['created_by'],false);
            $this->createIndex('{{%idx-media-updated}}','{{%media}}', ['updated_by'],false);
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_media_to_users1',
                '{{%media}}',
                'created_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
            $this->addForeignKey(
                'fk_media_to_users2',
                '{{%media}}',
                'updated_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-media}}', '{{%media}}');
        $this->dropIndex('{{%idx-media-alias}}', '{{%media}}');
        $this->dropIndex('{{%idx-media-status}}', '{{%media}}');

        $this->dropForeignKey(
            'fk_media_to_cats',
            '{{%media}}'
        );

        if (class_exists('\wdmg\users\models\Users')) {
            $this->dropIndex('{{%idx-media-created}}', '{{%media}}');
            $this->dropIndex('{{%idx-media-updated}}', '{{%media}}');
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_media_to_users1',
                    '{{%media}}'
                );
                $this->dropForeignKey(
                    'fk_media_to_users2',
                    '{{%media}}'
                );
            }
        }

        $this->truncateTable('{{%media}}');
        $this->dropTable('{{%media}}');
    }

}
