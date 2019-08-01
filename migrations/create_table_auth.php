<?php

use yii\db\Migration;
class create_table_auth extends Migration{
    public function safeUp() {
       $this->createTable('auth', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'source' => $this->string()->notNull(),
            'source_id' => $this->string()->notNull(),
        ]);

        $this->addForeignKey('fk_auth_user_id_user_id', 'auth', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }
    public function safeDown() {
        $this->dropForeignKey('fk_auth_user_id_user_id','auth');
        $this->dropTable('authyi');
    }
}
