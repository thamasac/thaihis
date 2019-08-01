<?php

use  console\migrations\fid\components\Migration;

/**
 * Handles the creation of table `person`.
 */
class m170617_044429_create_person_support_fields_table extends Migration
{
    const PREFIX_TABLE_NAME = 'person_';
    const RACE_TABLE_NAME = '{{%race}}';
    const NATIONALITY_TABLE_NAME = '{{%nationality}}';
    const BLOOD_TYPE_TABLE_NAME = '{{%blood_type}}';
    const RELIGION_TABLE_NAME = '{{%religion}}';
    const GENDER_TABLE_NAME = '{{%gender}}';
    const MILITARY_STATUS_TABLE_NAME = '{{%military_status}}';
    const MARRIAGE_STATUS_TABLE_NAME = '{{%marriage_status}}';

    const TABLE_NAMES = [
        self::RACE_TABLE_NAME,
        self::NATIONALITY_TABLE_NAME,
        self::BLOOD_TYPE_TABLE_NAME,
        self::RELIGION_TABLE_NAME,
        self::GENDER_TABLE_NAME,
        self::MILITARY_STATUS_TABLE_NAME,
        self::MARRIAGE_STATUS_TABLE_NAME,
    ];

    /**
     * @inheritdoc
     */
    public function up()
    {
        foreach (self::TABLE_NAMES as $supportTableName) {
            $supportTableName = str_replace('%','%'.self::PREFIX_TABLE_NAME,$supportTableName); //add prefix table name
            $this->createTable($supportTableName, [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'weight' => $this->smallInteger()->notNull()->defaultValue(999),
                'active' => $this->boolean()->notNull()->defaultValue(true),
            ]);
            $this->addCommentOnWeightColumn($supportTableName);
            $this->addCommentOnActiveColumn($supportTableName);
            $data = [];
            $file = fopen(dirname(__FILE__) . '/csv/' . $this->removePercentSignAndCurlyBraces(str_replace(self::PREFIX_TABLE_NAME,'',$supportTableName)) . '.csv', 'r');
            while (!feof($file)) {
                $line = fgetcsv($file);
                $data[] = [$line[0], $line[1]];
            }
            $this->batchInsert($supportTableName, ['id', 'name'], $data);
            $this->insert($supportTableName, [
                'id' => 999,
                'name' => 'Other',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        foreach (self::TABLE_NAMES as $supportTableName) {
            $this->dropTable($supportTableName);
        }
    }
}
