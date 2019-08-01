<?php

use yii\db\Migration;

class m170617_044300_set_collation_db extends Migration
{
    public function safeUp()
    {
        //$dbname = explode('dbname=', $_SERVER['DB_DSN'])[1];
        $this->execute('ALTER DATABASE `fids` CHARACTER SET utf8 COLLATE utf8_unicode_ci');
    }

    public function safeDown()
    {
    }
}
