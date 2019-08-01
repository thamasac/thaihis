<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace appxq\sdii\widgets;

/**
 * Description of SDExcel
 *
 * @author appxq
 */
class SDExcel extends \moonland\phpexcel\Excel {

    public function writeFile($sheet) {
        if (!isset($this->format))
            $this->format = 'Xlsx';
        $objectwriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($sheet, $this->format);
        $path = 'php://output';
        if (isset($this->savePath) && $this->savePath != null) {
            $path = $this->savePath . '/' . $this->getFileName();
        }
        $objectwriter->save($path);
    }

    /**
     * Setting data from models
     */
    public function executeColumns(&$activeSheet = null, $models, $columns = [], $headers = []) {
        if ($activeSheet == null) {
            $activeSheet = $this->activeSheet;
        }
        $hasHeader = false;
        $row = 1;
        $char = 26;
        foreach ($models as $model) {
            if (empty($columns)) {
                $columns = $model->attributes();
            }
            if ($this->setFirstTitle && !$hasHeader) {
                $isPlus = false;
                $colplus = 0;
                $colnum = 1;
                foreach ($columns as $key => $column) {
                    $col = '';
                    if ($colnum > $char) {
                        $colplus += 1;
                        $colnum = 1;
                        $isPlus = true;
                    }
                    if ($isPlus) {
                        $col .= chr(64 + $colplus);
                    }
                    $col .= chr(64 + $colnum);
                    $header = '';
                    if (is_array($column)) {
                        if (isset($column['header'])) {
                            $header = $column['header'];
                        } elseif (isset($column['attribute']) && isset($headers[$column['attribute']])) {
                            $header = $headers[$column['attribute']];
                        } elseif (isset($column['attribute'])) {
                            $header = $model->getAttributeLabel($column['attribute']);
                        }
                    } else {
                        $header = $model->getAttributeLabel($column);
                    }
                    $activeSheet->setCellValue($col . $row, $header);
                    
                    $colnum++;
                }
                $hasHeader = true;
                $row++;
            }
            $isPlus = false;
            $colplus = 0;
            $colnum = 1;
            foreach ($columns as $key => $column) {
                $col = '';
                if ($colnum > $char) {
                    $colplus++;
                    $colnum = 1;
                    $isPlus = true;
                }
                if ($isPlus) {
                    $col .= chr(64 + $colplus);
                }
                $col .= chr(64 + $colnum);
                if (is_array($column)) {
                    $column_value = $this->executeGetColumnData($model, $column);
                } else {
                    $column_value = $this->executeGetColumnData($model, ['attribute' => $column]);
                }
                $activeSheet->setCellValue($col . $row, $column_value);
                $activeSheet->getCell($col . $row)->setValueExplicit($column_value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
                $colnum++;
            }
            $row++;
        }
    }

}
