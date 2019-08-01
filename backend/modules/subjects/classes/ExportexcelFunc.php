<?php

namespace backend\modules\subjects\classes;

use Yii;
use appxq\sdii\utils\SDUtility;

class ExportexcelFunc {

    public static function ExportExcelTbdata($fileName, $titleSheet, $headerData, $dataRow) {
        $fileName = $titleSheet . '_' . SDUtility::getMillisecTime() . '.xlsx';
        $columns = [];
        $headers = [];
        foreach ($headerData as $key => $value) {
            $columns[] = $key;
            $headers[$key] = $key;
        }

        $export = \appxq\sdii\widgets\SDExcel::export([
                    'fileName' => $fileName,
                    'savePath' => Yii::getAlias('@backend/web/print'),
                    'format' => 'Xlsx',
                    'asAttachment' => false,
                    //'isMultipleSheet' => true,
                    'models' => $dataRow,
                    'columns' => $columns,
                    'headers' => $headers,
        ]);

        return $fileName;
    }

    public static function ExportExcelMultiSh($fileName, $headerData, $dataArray) {
        $fileName = $fileName . '_' . SDUtility::getMillisecTime() . '.xlsx';
        $columns = [];
        $headers = [];
        $models = [];

        foreach ($headerData as $key => $value) {
            $cols = [];
            $heads = [];
            foreach ($value as $k => $val) {
                $cols[] = $k;
                $heads[$k] = $k;
            }
            
            $headers[$key] = $heads;
            $columns[$key] = $cols;
            
        }

        $export = \appxq\sdii\widgets\SDExcel::export([
                    'isMultipleSheet' => true,
                    'fileName' => $fileName,
                    'savePath' => Yii::getAlias('@backend/web/print'),
                    'format' => 'Xlsx',
                    'asAttachment' => false,
                    'models' => $dataArray,
                    'columns' => $columns,
                    'headers' => $headers,
        ]);

        return $fileName;
    }

}
