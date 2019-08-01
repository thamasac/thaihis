<?php

use  console\migrations\fid\components\Migration;

/**
 * Handles the creation of table `person`.
 */
class m170617_044427_create_person_support_fields_table extends Migration
{
    // Address
    const SUBDISTRICT_TABLE_NAME = '{{%addr_subdistrict}}';
    const DISTRICT_TABLE_NAME = '{{%addr_district}}';
    const PROVINCE_TABLE_NAME = '{{%addr_province}}';
    const COUNTRY_TABLE_NAME = '{{%addr_country}}';

    public function createCountryTable()
    {
        $this->createTable(self::COUNTRY_TABLE_NAME, [
            'id' => $this->primaryKey(),
            'code' => $this->string(),
            'name' => $this->string()->notNull(),
        ]);
        $data = [];
        $file = fopen(dirname(__FILE__) . '/csv/country.csv', 'r');
        while (!feof($file)) {
            $line = fgetcsv($file);
            $data[] = [$line[0], $line[1], $line[2]];
        }
        fclose($file);
        $this->batchInsert(self::COUNTRY_TABLE_NAME, ['id', 'code', 'name'], $data);
        $this->insert(self::COUNTRY_TABLE_NAME, [
            'id' => 999,
            'name' => 'Other',
        ]);
    }

    public function createProvinceTable()
    {
        $this->createTable(self::PROVINCE_TABLE_NAME, [
            'id' => $this->primaryKey(),
            'code' => $this->string(),
            'name' => $this->string()->notNull(),
        ]);
        $data = [];
        $file = fopen(dirname(__FILE__) . '/csv/province.csv', 'r');
        while (!feof($file)) {
            $line = fgetcsv($file);
            $data[] = [$line[0], $line[3], $line[2]];
        }
        fclose($file);
        $this->batchInsert(self::PROVINCE_TABLE_NAME, ['id', 'code', 'name'], $data);
        $this->insert(self::PROVINCE_TABLE_NAME, [
            'id' => 999,
            'name' => 'Other',
        ]);
    }

    public function createDistrictTable()
    {
        $this->createTable(self::DISTRICT_TABLE_NAME, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'provinceId' => [
                'type' => $this->integer(),
                'fk' => self::PROVINCE_TABLE_NAME,
            ],
        ]);
        $data = [];
        $file = fopen(dirname(__FILE__) . '/csv/district.csv', 'r');
        while (!feof($file)) {
            $line = fgetcsv($file);
            $data[] = [$line[0], $line[2], $line[4]];
        }
        fclose($file);
        $this->batchInsert(self::DISTRICT_TABLE_NAME, ['id', 'name', 'provinceId'], $data);
        $this->insert(self::DISTRICT_TABLE_NAME, [
            'id' => 999,
            'name' => 'Other',
        ]);
    }

    public function createSubdistrictTable()
    {
        $this->createTable(self::SUBDISTRICT_TABLE_NAME, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'districtId' => [
                'type' => $this->integer(),
                'fk' => self::DISTRICT_TABLE_NAME,
            ]
        ]);
        $data = [];
        $file = fopen(dirname(__FILE__) . '/csv/subdistrict.csv', 'r');
        while (!feof($file)) {
            $line = fgetcsv($file);
            $data[] = [$line[0], $line[2], $line[3]];
        }
        $this->batchInsert(self::SUBDISTRICT_TABLE_NAME, ['id', 'name', 'districtId'], $data);
        fclose($file);
        $this->insert(self::SUBDISTRICT_TABLE_NAME, [
            'id' => 9999,
            'name' => 'Other',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createCountryTable();
        $this->createProvinceTable();
        $this->createDistrictTable();
        $this->createSubdistrictTable();
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(self::SUBDISTRICT_TABLE_NAME);
        $this->dropTable(self::DISTRICT_TABLE_NAME);
        $this->dropTable(self::PROVINCE_TABLE_NAME);
        $this->dropTable(self::COUNTRY_TABLE_NAME);
    }
}
