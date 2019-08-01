<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use backend\modules\ezforms2\models\TbdataAll;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformTarget;
use appxq\sdii\utils\SDUtility;

/**
 * Description of EzfUiFunc
 *
 * @author appxq
 */
class EzfUiFunc {

    public static function loadUniqueRecord($model, $ezf_table, $target) {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);
            $strWhere = '';
            $params = [':target' => $target];
            if ($model->id) {
                $strWhere = 'AND id<>:id';
                $params[':id'] = $model->id;
            }
            $modelSave = $modelSave->find()->where('target=:target AND rstat not in(0,3) ' . $strWhere, $params)->one();

            if ($modelSave) {
                return $modelSave;
            }

            return false;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function loadNewRecordBySite($model, $ezf_table, $userid, $sitecode) {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);

            $modelSave = $modelSave->find()->where('user_create=:userid AND xsourcex=:xsourcex AND rstat = 0 AND DATE(create_date) <> CURDATE()', [':userid' => $userid, ':xsourcex'=>$sitecode])->one();
            if (!$modelSave) {
                return false;
            }

            //$model->attributes = $modelSave->attributes;
            
            //$model->afterFind();

            return $modelSave;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }

    public static function loadNewRecord($model, $ezf_table, $userid) {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);

            $modelSave = $modelSave->find()->where('user_create=:userid AND rstat = 0 AND DATE(create_date) <> CURDATE()', [':userid' => $userid])->one();
            if (!$modelSave) {
                return false;
            }

            //$model->attributes = $modelSave->attributes;
            
            //$model->afterFind();

            return $modelSave;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function loadLastRecord($model, $ezf_table, $target, $rstat=1) {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);

            $modelSave = $modelSave->find()->where('rstat = :rstat AND target like :target', [':target' => "$target%", ':rstat'=>$rstat])->orderBy('create_date DESC')->limit(1)->one();
            if (!$modelSave) {
                return false;
            }

            $model->attributes = $modelSave->attributes;
            //$model->afterFind();
 
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function loadLastDateRecord($model, $ezf_table, $target, $date_field='create_date', $unit_field='', $enable_field='') {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);
            $userProfile = Yii::$app->user->identity->profile;
            $unit = isset($userProfile->department)?$userProfile->department:0;
                    
            $where_str = '';
            $params = [':target' => "$target%"];
            
            if($unit_field!='') {
                $where_str .= " AND `$unit_field` = :unit";
                $params[':unit'] = $unit;
            }
            
            if($enable_field!=''){
                $where_str .= " AND `$enable_field` = 1";
            }

            $modelSave = $modelSave->find()->where('rstat not in(0,3) AND DATE(`'.$date_field.'`) = CURDATE() AND target like :target '. $where_str, $params)->orderBy('create_date DESC')->limit(1)->one();
            if (!$modelSave) {
                return false;
            }

            $model->attributes = $modelSave->attributes;
            //$model->afterFind();
 
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function loadLastDateRecordNotModel($ezf_table, $target, $date_field='create_date', $unit_field='', $enable_field='') {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);
            $userProfile = Yii::$app->user->identity->profile;
            $unit = isset($userProfile->department)?$userProfile->department:0;
                    
            $where_str = '';
            $params = [':target' => "$target%"];
            
            if($unit_field!='') {
                $where_str .= " AND `$unit_field` = :unit";
                $params[':unit'] = $unit;
            }
            
            if($enable_field!='') {
                $where_str .= " AND `$enable_field` = 1";
            }
            
            $modelSave = $modelSave->find()->where('rstat not in(0,3) AND DATE(`'.$date_field.'`) = CURDATE() AND target like :target '. $where_str, $params)->orderBy('create_date DESC')->limit(1)->one();

            return $modelSave;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function loadNewRecordByIp($model, $ezf_table, $ip, $token) {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);

            $modelSave = $modelSave->find()->where('xsourcex=:ip AND xdepartmentx=:token AND rstat <> 3', [':ip' => $ip, ':token'=>$token])->one();
            if (!$modelSave) {
                return false;
            }

            $model->attributes = $modelSave->attributes;
            //$model->afterFind();

            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }

    public static function loadTbData($ezf_table, $dataid) {
        try {
            $model = new TbdataAll();
            $model->setTableName($ezf_table);

            $model = $model->find()->where('id=:id AND rstat <> 3', [':id' => $dataid])->one();
            if (!$model) {
                return FALSE;
            }
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    public static function loadData($model, $ezf_table, $dataid = '', $special = false) {
        if ($dataid != '') {

            $modelSave = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if (!$modelSave) {
                return FALSE;
            }
            
            $model->attributes = $modelSave->attributes;
            
            
            //$model->afterFind();
        } else {

            $model->init();
        }

        return $model;
    }

    public static function saveData($model, $ezf_table, $ezf_id, $dataid = '') {
        try {
            $insert = true;
            //load
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table); //$modelEzf->ezf_table

            if ($dataid != '') {
                $modelTbData = EzfUiFunc::loadTbData($ezf_table, $dataid);
                if ($modelTbData) {
                    $modelSave = $modelTbData;
                    $insert = false;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                        'data' => $dataid,
                    ];
                    return $result;
                }
            }

            //Save()
            $model->beforeSave($insert);
            $modelSave->attributes = $model->attributes;
            //\appxq\sdii\utils\VarDumper::dump($modelSave->attributes);
            $result = $modelSave->save();

            $model->afterSave($insert, $modelSave->attributes);

            if ($result) {
                if(isset($model->target) && !empty($model->target)){
                    self::saveTarget($model, $ezf_id);
                }
                self::saveLog($model, $ezf_id);
                
                //working unit
                EzfFunc::addQueueLog($model, $ezf_id, $ezf_table);
                EzfFunc::completeProcess($model, $ezf_id, $ezf_table);
                
                $result_data = [];//int to string support id for JS
                foreach ($modelSave->attributes as $key_r => $value_r) {
                    $result_data[$key_r] = "{$value_r}";
                }
                
                $result = [
                    'status' => 'success',
                    'action' => $insert ? 'create' : 'update',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                    'data' => $result_data,
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not save the data.'),
                    'data' => $modelSave->attributes,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error') . ' ' . $e->getMessage(),
            ];
            return $result;
        }
    }

    public static function deleteDataRstat($model, $ezf_table, $ezf_id, $dataid, $reloadDiv = '') {
        try {
            $modelSave = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($modelSave) {
                $modelSave->rstat = 3;

                $model->attributes = $modelSave->attributes; //ส่งค่าให้กับ event
                $model->beforeSave(FALSE);
                $result = $modelSave->save();
                $model->afterSave(FALSE, $modelSave->attributes);

                if ($result) {
                    self::saveTarget($model, $ezf_id);
                    EzfFunc::deleteQueueLog($model, $ezf_id);
                    
                    $result_data = [];//int to string support id for JS
                    foreach ($modelSave->attributes as $key_r => $value_r) {
                        $result_data[$key_r] = "{$value_r}";
                    }

                    $result = [
                        'status' => 'success',
                        'action' => 'delete',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                        'data' => $result_data,
                        'reloadDiv' => $reloadDiv,
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                        'data' => $modelSave->attributes,
                    ];
                    return $result;
                }
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                    'data' => $dataid,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error'),
            ];
            return $result;
        }
    }

    public static function deleteData($model, $ezf_table, $ezf_id, $dataid, $reloadDiv = '') {
        try {
            $modelSave = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($modelSave) {
                $model->attributes = $modelSave->attributes; //ส่งค่าให้กับ event
                $model->beforeDelete();
                $result = $modelSave->delete();
                $model->afterDelete();

                if ($result) {
                    self::deleteTarget($model, $ezf_id);

                    $result = [
                        'status' => 'success',
                        'action' => 'delete',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                        'data' => $modelSave->attributes,
                        'reloadDiv' => $reloadDiv,
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                        'data' => $modelSave->attributes,
                    ];
                    return $result;
                }
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                    'data' => $dataid,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error'),
            ];
            return $result;
        }
    }

    public static function modelSearch($model, $ezform, $targetField, $colSearch, $params, $pageSize=50, $order_column=[], $orderby=SORT_DESC) {
        //$model = new TbdataAll();

        $query = $model->find()->where('rstat not in(0,3)'); //->where('rstat not in(0, 3)');
        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }
        $fields = EzfQuery::getFieldAll($ezform->ezf_id, $ezform->ezf_version);
        $col = \yii\helpers\ArrayHelper::getColumn($fields, 'ezf_field_name');
        $colSearch = \yii\helpers\ArrayHelper::merge($colSearch, $col);
        $colSearch = \yii\helpers\ArrayHelper::merge($colSearch, ['userby', 'sitename']);
        $model->setColFieldsAddon($colSearch);
        
        //$query->innerJoin('profile', "profile.user_id = {$ezform['ezf_table']}.user_update");
        $query->select([
            "{$ezform['ezf_table']}.*",
            "(SELECT const_hospital.`name` FROM const_hospital WHERE const_hospital.code = {$ezform['ezf_table']}.xsourcex ) AS sitename",
            "(SELECT concat(firstname, ' ', lastname) AS `name` FROM profile WHERE profile.user_id = {$ezform['ezf_table']}.user_update ) AS userby"        
        ]);
        
        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        
        $defaultOrder = [];
        if(empty($order_column)){
            $defaultOrder = [
                'create_date' => $orderby
            ];
        } else {
            foreach ($order_column as $rkey => $rvalue) {
                $defaultOrder[$rvalue] = (int)$orderby;
            }
        }
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => $defaultOrder
            ]
        ]);

        $model->load($params);

        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date({$ezform['ezf_table']}.create_date)", $sdate, $edate]);
            }
        }
        
//        if ($targetField!='') {
//            $query->andFilterWhere(['like', $targetField, $model[$targetField]]);
//        }

        $colSearch = \yii\helpers\ArrayHelper::merge($colSearch, ['id', 'sitecode', 'ptid', 'target', 'xsourcex', 'ptcode', 'hptcode', 'hsitecode', 'rstat']);
//        $query->andFilterWhere([
//            'id' => $model->id,
//        ]);

        foreach ($colSearch as $field) {
            
            if (is_array($field)) {
                if (isset($field['attribute'])) {
                    $model_field = EzfQuery::getFieldByNameVersion($ezform->ezf_id, $field['attribute'], $ezform->ezf_version);
                    if(in_array($model_field['ezf_field_type'], [63,64])){
                        $daterang = explode(' to ', $model[$field['attribute']]);
                        if (isset($daterang[1])) {
                            $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                            $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                            $query->andFilterWhere(['between', "date({$ezform['ezf_table']}.{$field['attribute']})", $sdate, $edate]);
                        }
                    } else {
                        $query->andFilterWhere(['like', $ezform['ezf_table'].'.'.$field['attribute'], $model[$field['attribute']]]);
                    }
                }
            } else {
                $model_field = EzfQuery::getFieldByNameVersion($ezform->ezf_id, $field, $ezform->ezf_version);
                if(in_array($model_field['ezf_field_type'], [63,64])){
                    $daterang = explode(' to ', $model[$field]);
                    if (isset($daterang[1])) {
                        $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                        $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                        $query->andFilterWhere(['between', "date({$ezform['ezf_table']}.{$field})", $sdate, $edate]);
                    }
                } else {
                    $query->andFilterWhere(['like', $ezform['ezf_table'].'.'.$field, $model[$field]]);
                }
                
            }
        }


        return $dataProvider;
    }

    public static function modelSearchDb2($model, $ezform, $targetField, $colSearch, $params, $pageSize=50, $order_column=[], $orderby=SORT_DESC) {
        //$model = new TbdataAll();

        $query = $model->find()->where("{$ezform['ezf_table']}.rstat not in(0,3)"); //->where('rstat not in(0, 3)');
        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }
        $model->setColFieldsAddon(['userby', 'sitename', 'id_ref']);
        //$query = new \yii\db\Query;
        $query->leftJoin($ezform['ezf_table'].'_db2 as db2', "db2.id = {$ezform['ezf_table']}.id");
        $query->select([
            "{$ezform['ezf_table']}.id AS id_ref",
            "db2.*",        
            "(SELECT const_hospital.`name` FROM const_hospital WHERE const_hospital.code = db2.xsourcex ) AS sitename",
            "(SELECT concat(firstname, ' ', lastname) AS `name` FROM profile WHERE profile.user_id = db2.user_update ) AS userby"       
        ]);
        
        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => Yii::$app->user->identity->profile->sitecode]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere("{$ezform['ezf_table']}.xdepartmentx = :unit", [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("{$ezform['ezf_table']}.user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        
        $defaultOrder = [];
        if(empty($order_column)){
            $defaultOrder = [
                'create_date' => $orderby
            ];
        } else {
            foreach ($order_column as $rkey => $rvalue) {
                $defaultOrder[$rvalue] = (int)$orderby;
            }
        }
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => $defaultOrder
            ]
        ]);

        $model->load($params);

        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date({$ezform['ezf_table']}.create_date)", $sdate, $edate]);
            }
        }
        
        if ($targetField!='') {
            $query->andFilterWhere(['like', $targetField, $model[$targetField]]);
        }

        $colSearch = \yii\helpers\ArrayHelper::merge($colSearch, ['id', 'sitecode', 'ptid', 'target', 'xsourcex', 'ptcode', 'hptcode', 'hsitecode', 'rstat']);
//        $query->andFilterWhere([
//            'id' => $model->id,
//        ]);

        foreach ($colSearch as $field) {
            if (is_array($field)) {
                if (isset($field['attribute'])) {
                    $query->andFilterWhere(['like', 'db2.'.$field['attribute'], $model[$field['attribute']]]);
                }
            } else {
                $query->andFilterWhere(['like', 'db2.'.$field, $model[$field]]);
            }
        }


        return $dataProvider;
    }
    
    public static function modelSearchCompare($model, $ezform, $targetField, $colSearch, $params, $pageSize=50, $order_column=[], $orderby=SORT_DESC) {
        //$model = new TbdataAll();

        $query = $model->find()->where("{$ezform['ezf_table']}.rstat not in(0,3)"); //->where('rstat not in(0, 3)');
        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }
        
        //$query = new \yii\db\Query;
        $fieldsList = EzfQuery::getFieldsList($ezform->ezf_id);
        
        $query->leftJoin($ezform['ezf_table'].'_db2 as db2', "db2.id = {$ezform['ezf_table']}.id");
        $select = [
            "{$ezform['ezf_table']}.*",
            "db2.user_update AS user_update2", 
            "db2.create_date AS create_date2",        
            "db2.rstat AS rstat2",        
                    
            "(SELECT concat(firstname, ' ', lastname) AS `name` FROM profile WHERE profile.user_id = {$ezform['ezf_table']}.user_update ) AS userby",        
            "(SELECT concat(firstname, ' ', lastname) AS `name` FROM profile WHERE profile.user_id = db2.user_update ) AS userby2"       
        ];
        $colFieldsAddon = ['userby', 'userby2', 'user_update2', 'rstat2', 'create_date2'];
        
        if(isset($fieldsList) && !empty($fieldsList)){
            foreach ($fieldsList as $keyF => $valueF) {
                $select[] = "db2.{$valueF['ezf_field_name']} AS {$valueF['ezf_field_name']}2";
                $colFieldsAddon[] = "{$valueF['ezf_field_name']}2";
            }
        }
        
        $model->setColFieldsAddon($colFieldsAddon);
        
        $query->select($select);
        
        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => Yii::$app->user->identity->profile->sitecode]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere("{$ezform['ezf_table']}.xdepartmentx = :unit", [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("{$ezform['ezf_table']}.user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        
        $defaultOrder = [];
        if(empty($order_column)){
            $defaultOrder = [
                'create_date' => $orderby
            ];
        } else {
            foreach ($order_column as $rkey => $rvalue) {
                $defaultOrder[$rvalue] = (int)$orderby;
            }
        }
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => $defaultOrder
            ]
        ]);

        $model->load($params);

        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date({$ezform['ezf_table']}.create_date)", $sdate, $edate]);
            }
        }
        
        if (isset($model['create_date2']) && !empty($model['create_date2'])) {
            $daterang = explode(' to ', $model['create_date2']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date(db2.create_date)", $sdate, $edate]);
            }
        }
        
        if ($targetField!='') {
            $query->andFilterWhere(['like', $targetField, $model[$targetField]]);
        }

        $colSearch = \yii\helpers\ArrayHelper::merge($colSearch, ['id', 'sitecode', 'ptid', 'target', 'xsourcex', 'ptcode', 'hptcode', 'hsitecode', 'rstat']);
//        $query->andFilterWhere([
//            'id' => $model->id,
//        ]);

        foreach ($colSearch as $field) {
            if (is_array($field)) {
                if (isset($field['attribute'])) {
                    $query->andFilterWhere(['like', "{$ezform['ezf_table']}.".$field['attribute'], $model[$field['attribute']]]);
                }
            } else {
                $query->andFilterWhere(['like', "{$ezform['ezf_table']}.".$field, $model[$field]]);
            }
        }


        return $dataProvider;
    }
    
    public static function modelEvalutionSearch($model, $target, $category, $orderby, $params) {
        //$model = new EzformTarget();

        $query = $model->find()->where('ezform_target.rstat not in(0,3)'); //->where('rstat not in(0, 3)');

        $query->innerJoin('profile', 'profile.user_id = ezform_target.user_update');
        $query->innerJoin('ezform', 'ezform.ezf_id = ezform_target.ezf_id');

        $query->select([
            'ezform_target.*',
            'ezform.ezf_name',
            'ezform.ezf_table',
            'ezform.ezf_detail',
            'ezform.category_id',
            'ezform.co_dev',
            'ezform.assign',
            'ezform.public_listview',
            'ezform.public_edit',
            'ezform.public_delete',
            "(SELECT IFNULL(field_detail,'') AS field_detail FROM ezform ezf WHERE ezf.ezf_id = ezform_target.ezf_id ) AS ezf_detail",
            "(SELECT const_hospital.`name` FROM const_hospital WHERE const_hospital.code = ezform_target.xsourcex ) AS sitename",//ezform_target.xsourcex
            "concat(profile.firstname, ' ', profile.lastname) AS userby"
        ]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => ['create_date'=>$orderby]
            ]
        ]);

        $model->load($params);
        
        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date(ezform_target.create_date)", $sdate, $edate]);
            }
        }
        
        if (!empty($category)) {
            $query->andWhere('ezform.category_id = :category', [':category' => $category]);
        }
        
        $query->andFilterWhere(['like', 'ezform_target.ezf_id', $model->ezf_id]);
        $query->andFilterWhere(['like', 'ezform_target.user_update', $model->user_update]);
        $query->andFilterWhere(['like', 'ezform_target.xsourcex', $model->xsourcex]);
        $query->andFilterWhere(['like', 'ezform_target.rstat', $model->rstat]);
        $query->andFilterWhere(['like', 'ezform_target.target_id', $model->target_id]);
        //\appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        return $dataProvider;
    }
    
    public static function modelEmrSearch($model, $target, $ezf_id, $params, $showall = 0) {
        //$model = new EzformTarget();

        $query = $model->find()->where('ezform_target.rstat not in(0,3)'); //->where('rstat not in(0, 3)');

        $query->innerJoin('profile', 'profile.user_id = ezform_target.user_update');
        $query->innerJoin('ezform', 'ezform.ezf_id = ezform_target.ezf_id');

        $query->select([
            'ezform_target.*',
            'ezform.ezf_name',
            'ezform.ezf_table',
            'ezform.co_dev',
            'ezform.assign',
            'ezform.public_listview',
            'ezform.public_edit',
            'ezform.public_delete',
            "(SELECT IFNULL(field_detail,'') AS field_detail FROM ezform ezf WHERE ezf.ezf_id = ezform_target.ezf_id ) AS ezf_detail",
            "(SELECT const_hospital.`name` FROM const_hospital WHERE const_hospital.code = ezform_target.xsourcex ) AS sitename",//ezform_target.xsourcex
            "concat(profile.firstname, ' ', profile.lastname) AS userby"
        ]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
//            'sort' => [
//                'route' => '/ezforms2/fileinput/grid-update',
//            ]
        ]);

        $model->load($params);
        
        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', "date(ezform_target.create_date)", $sdate, $edate]);
            }
        }
        
        if (!$showall && empty($model->target_id)) {
            $query->andWhere('ezform_target.ezf_id = :ezf_id', [':ezf_id' => $ezf_id]);
        } else if($showall) {
            $modelTarget = EzformTarget::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->one();
            if($modelTarget){
                $query->andWhere('ezform_target.ptid = :ptid', [':ptid' => $modelTarget['ptid']]);
            }
        }
        
        $query->andFilterWhere(['like', 'ezform_target.ezf_id', $model->ezf_id]);
        $query->andFilterWhere(['like', 'ezform_target.user_update', $model->user_update]);
        $query->andFilterWhere(['like', 'ezform_target.xsourcex', $model->xsourcex]);
        $query->andFilterWhere(['like', 'ezform_target.rstat', $model->rstat]);
        $query->andFilterWhere(['like', 'ezform_target.target_id', $model->target_id]);
        //\appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        return $dataProvider;
    }
    
    public static function modelSearchSelect2($model, $ezform, $targetField, $colSearch, $q) {
        //$model = new TbdataAll();

        $query = $model->find()->where('rstat not in(0,3)'); //->where('rstat not in(0, 3)');
        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }
        $model->setColFieldsAddon(['userby', 'sitename']);
        
        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'defaultOrder' => [
                    'create_date' => SORT_DESC
                ]
            ]
        ]);
        
        $whereField = [];
        foreach ($colSearch as $field) {
            try {
                $model->$field;
                $whereField[] = "`$field`";
            } catch (\yii\base\Exception $e) {
                
            }
        }
        $whereStr = 'id LIKE :q';
        if(!empty($whereField)){
            //$whereStr = 'CONCAT('.implode(',', $whereField).')';
            $whereStr = '(';
            $whereOr = '';
            foreach ($whereField as $key => $value) {
                $whereStr .= $whereOr." $value LIKE :q ";
                $whereOr = 'OR';
            }
            $whereStr .= ')';
            
        } 
        //\appxq\sdii\utils\VarDumper::dump($whereStr);
        $query->andWhere($whereStr, [':q'=>"%$q%"]);
        
        //\appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql,1,0);
        return $dataProvider;
    }

    public static function setSystemProperty($model, $target, $dataTarget, $tableForm, $fieldTarget, $fieldSpecial, $special, $userProfile, $modelTarget, $rstat) {
        $userid = $userProfile['user_id'];
        $hsitecode = $userProfile['sitecode'];
        $xsourcex = $userProfile['sitecode'];
        $department = $userProfile['department'];
        $id = $model->id;
        
        if($model->rstat!=0){
            $hptcode = $model->hptcode;
            $hsitecode = $model->hsitecode;
            $xsourcex = $model->xsourcex;
            $department = $model->xdepartmentx;
        }
        
        $insert = true;
        if (isset($id) && !empty($id)) {
            $insert = false;
        } else {
            $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        }
        
        $ptid = $id;
        $sitecode = $hsitecode;
        $hptcode = '';
        $ptcode = $hptcode;
        
        if ($target != '' && !empty($dataTarget)) {
            $hptcode = $dataTarget['hptcode'];
            $hsitecode = $dataTarget['hsitecode'];
            //ถ้าลบออกไซต์อื่นแก้ไข จะมีค่าที่ต่างกัน
            //$xsourcex = $dataTarget['xsourcex'];
            //$department = $dataTarget['xdepartmentx'];

        } elseif (($target == '') && $insert) {
            $hptcode = EzfQuery::getMaxCodeBySitecode($tableForm, $hsitecode);
             
            $ptcode = $hptcode;
            if (isset($model->ptid) && !empty($model->ptid)) {
                $ptid = $model->ptid;
                $sitecode = $model->sitecode;
                $ptcode = $model->ptcode;
                $ptcodefull = $model->ptcodefull;
            }
        } elseif (($target == '' && !empty($dataTarget)) && !$insert) {
            $ptid = $model->ptid;
            $sitecode = $model->sitecode;
            $ptcode = $model->ptcode;
            $ptcodefull = $model->ptcodefull;
            //ถ้าลบออกไซต์อื่นแก้ไข จะมีค่าที่ต่างกัน
            $hptcode = $model->hptcode;
            $hsitecode = $model->hsitecode;
            $xsourcex = $model->xsourcex;
            $department = $model->xdepartmentx;
        }
        
        $ptcodefull = $sitecode.$ptcode;

        $modelSystem = [
            'id' => $id,
            'ptid' => isset($dataTarget['ptid']) ? $dataTarget['ptid'] : $ptid,
            'sitecode' => isset($dataTarget['sitecode']) ? $dataTarget['sitecode'] : $sitecode,
            'ptcode' => isset($dataTarget['ptcode']) ? $dataTarget['ptcode'] : $ptcode,
            'ptcodefull' => isset($dataTarget['ptcodefull']) ? $dataTarget['ptcodefull'] : $ptcodefull,
            'target' => ($target != '' && !empty($dataTarget)) ? $target : $id,
            'hptcode' => $hptcode,
            'hsitecode' => $hsitecode,
            'xsourcex' => $xsourcex,
            'xdepartmentx' => $department,
            //'user_update' => $userid,
            //'update_date' => new \yii\db\Expression('NOW()'),
        ];

        if (isset($fieldTarget) && $fieldTarget != '') {
            $modelSystem[$fieldTarget] = ($target != '' && !empty($dataTarget)) ? $target : $id;
        }

        if (isset($fieldSpecial) && $fieldSpecial != '') {
            $modelSystem[$fieldSpecial] = $model[$fieldSpecial];
        }
        
        if ($target != '' && isset($dataTarget) && !empty($dataTarget)) {
            
            $refForm = \appxq\sdii\utils\SDUtility::string2Array($modelTarget['ref_form']);
            if(!empty($refForm)){
                $fieldTarget = 'load';
                foreach ($refForm as $key => $value) {
                    $nameArry = explode('_', $value);
                    if ($nameArry[0] == 'my') {
                        $modelSystem[$value] = $dataTarget['target'];
                    } else {
                        if($fieldTarget=='load'){
                            $fieldTarget = EzfQuery::getTargetOne($modelTarget['ref_ezf_id']);
                        }
                        
                        if(isset($fieldTarget)){
                            $refFormTarget = \appxq\sdii\utils\SDUtility::string2Array($fieldTarget['ref_form']);
                            if(!empty($refFormTarget) && isset($refFormTarget[$key])){
                                $modelSystem[$value] = $dataTarget[$refFormTarget[$key]];
                            }
                        }
                    }
                }
            }
        }
        
        try {
            
            if ($insert) {
                $form_field = array_keys($model->attributes);
                $system_field = array_keys($modelSystem);
                foreach ($form_field as $value) {
                    if(isset($model[$value]) && !in_array($value, $system_field)){
                        $modelSystem[$value] = $model[$value];
                    }
                }
                
                $modelSystem['error'] = NULL;
                $modelSystem['rstat'] = $rstat;
                $modelSystem['user_create'] = $userid;
                $modelSystem['create_date'] = new \yii\db\Expression('NOW()');
                $r = Yii::$app->db->createCommand()->insert($tableForm, $modelSystem)->execute();
            } else {
                $r = Yii::$app->db->createCommand()->update($tableForm, $modelSystem, 'id=:id', [':id' => $id])->execute();
            }
        } catch (\yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $modelSystem;
    }

    public static function saveTarget($model, $ezf_id) {
        $modelTarget = EzformTarget::find()->where('ezf_id=:ezf_id AND data_id=:data_id AND target_id=:target_id', [':ezf_id' => $ezf_id, ':data_id' => $model->id, ':target_id' => $model->target])->one();
        if (!$modelTarget) {
            $modelTarget = new EzformTarget();
        }
        $modelTarget->ezf_id = $ezf_id;
        $modelTarget->data_id = $model->id;
        $modelTarget->target_id = $model->target;
        $modelTarget->ptid = $model->ptid;
        $modelTarget->user_create = $model->user_create;
        $modelTarget->create_date = $model->create_date;
        $modelTarget->user_update = $model->user_update;
        $modelTarget->update_date = $model->update_date;
        $modelTarget->rstat = $model->rstat;
        $modelTarget->xsourcex = $model->xsourcex;

        return $modelTarget->save();
    }

    public static function saveLog($model, $ezf_id) {
        $modelLog = new \backend\modules\ezforms2\models\EzformLog();

        $modelLog->id = SDUtility::getMillisecTime();
        $modelLog->ezf_id = $ezf_id;
        $modelLog->data_id = $model->id;
        $modelLog->user_id = $model->user_update;
        $modelLog->create_date = $model->update_date;
        $modelLog->sql_log = SDUtility::array2String($model->attributes);
        $modelLog->rstat = $model->rstat;
        $modelLog->xsourcex = $model->xsourcex;

        return $modelLog->save();
    }

    public static function deleteTarget($model, $ezf_id) {
        $modelTarget = EzformTarget::find()->where('ezf_id=:ezf_id AND data_id=:data_id', [':ezf_id' => $ezf_id, ':data_id' => $model->id])->one();
        if ($modelTarget) {
            return $modelTarget->delete();
        }
        return false;
    }

    public static function showListDataEzf($ezform, $user_id) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        $ezform_by = isset($ezform['created_by'])?$ezform['created_by']:'';
        return ($ezform_by == $user_id || $ezform['public_listview'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    public static function showViewDataEzf($ezform, $user_id, $created_by) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        $ezform_by = isset($ezform['created_by'])?$ezform['created_by']:'';
        return ($ezform_by == $user_id || $created_by == $user_id || $ezform['public_listview'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    public static function showDeleteDataEzf($ezform, $user_id, $created_by) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        $ezform_by = isset($ezform['created_by'])?$ezform['created_by']:'';
        return ($ezform_by == $user_id || $created_by == $user_id || $ezform['public_delete'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    public static function showEditDataEzf($ezform, $user_id, $created_by) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        $ezform_by = isset($ezform['created_by'])?$ezform['created_by']:'';
        return ($ezform_by == $user_id || $created_by == $user_id || $ezform['public_edit'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    /**
     * function backgroundInsert
     *
     * @param string @ezf_id ezf_id
     * @param string $dataid dataid
     * @param string $target target
     * @param array $initdata $data['visit_no']
     * @return \appxq\sdii\models\SDDynamicModel|array|bool
     */
    public static function backgroundInsert($ezf_id, $dataid, $target, $initdata = [], $post = null) {
        //$dataid = '';
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        if($modelEzf){
            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
            
            return self::backgroundInsertEzform($modelEzf, $modelFields, $dataid, $target, $initdata, $post);
        } else {
            return FALSE;
        }
    }
    
    public static function backgroundInsertEzform($modelEzf, $modelFields, $dataid, $target, $initdata = [], $post = null) {
        $version = $modelEzf->ezf_version;
        $userProfile = Yii::$app->user->identity->profile;
        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $modelLastRecord;
        
        if($target!='' && $dataid=='' && isset($modelEzf->unique_record) && $modelEzf->unique_record==2){
            $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target);
            if($modelLastRecord){
                return false;
            }
        } elseif ($target!='' && $dataid=='' && isset ($modelEzf->unique_record) && $modelEzf->unique_record==4) {
            $options = SDUtility::string2Array($modelEzf->ezf_options);
            $create_date_field = isset($options['create_date_field']) && !empty($options['create_date_field'])?$options['create_date_field']:'create_date';
            $modelLastRecord = EzfUiFunc::loadLastDateRecord($model, $modelEzf->ezf_table, $target, $create_date_field);
            if($modelLastRecord){
                return false;
            }
        } elseif ($target!='' && $dataid=='' && isset ($modelEzf->unique_record) && $modelEzf->unique_record==3) {
            $options = SDUtility::string2Array($modelEzf->ezf_options);
            $modelLastRecord = EzfUiFunc::loadLastRecord($model, $modelEzf->ezf_table, $target, 2);
            if($modelLastRecord){
                return false;
            }
        }
        
        $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
        
        if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไมคิดรวมถ้าส่ง '' มา
            return false;
        }
        
        $targetReset = false;
        if (!isset($model->id)) {// ถ้ามี new record ที่คนUserนั้นสร้างไว้ ให้ใช้ record นั้น
            $modelNewRecord = EzfUiFunc::loadNewRecordBySite($model, $modelEzf->ezf_table, $userProfile->user_id, $userProfile->sitecode);
            
            if ($modelNewRecord) {
                $targetReset = true;
                $model->ptid = $modelNewRecord->ptid;
                $model->xsourcex = $modelNewRecord->xsourcex;
                $model->xdepartmentx = $userProfile->department;
                $model->rstat = $modelNewRecord->rstat;
                $model->sitecode = $modelNewRecord->sitecode;
                $model->ptcode = $modelNewRecord->ptcode;
                $model->ptcodefull = $modelNewRecord->ptcodefull;
                $model->hptcode = $modelNewRecord->hptcode;
                $model->hsitecode = $modelNewRecord->hsitecode;
                $model->user_create = $modelNewRecord->user_create;
                $model->create_date = $modelNewRecord->create_date;
                $model->user_update = $modelNewRecord->user_update;
                $model->update_date = $modelNewRecord->update_date;
                $model->target = $target;
                $model->sys_lat = $modelNewRecord->sys_lat;
                $model->sys_lng = $modelNewRecord->sys_lng;
                $model->id = $modelNewRecord->id;
            }
            $model->ezf_version = $version;
        }
        
        if (!empty($initdata)) {
            $model->attributes = $initdata;
        }

        //ขั้นตอนกรอกข้อมูลสำคัญ
        $evenFields = EzfFunc::getEvenField($modelFields);
        $special = isset($evenFields['special']) && !empty($evenFields['special']);

        if (isset($evenFields['target']) && !empty($evenFields['target'])) { //มีเป้าหมาย
            if ($targetReset) {
                $model[$evenFields['target']['ezf_field_name']] = '';
            }

            $modelEzfTarget = EzfQuery::getEzformOne($evenFields['target']['ref_ezf_id']);
            $target = ($target == '') ? $model[$evenFields['target']['ezf_field_name']] : $target;

            $dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);
            if ($dataTarget) {//เลือกเป้าหมายแล้ว
                if (isset($modelEzf['unique_record']) && $modelEzf['unique_record'] == 2) {
                    $unique = EzfUiFunc::loadUniqueRecord($model, $modelEzf->ezf_table, $target);
                    //\appxq\sdii\utils\VarDumper::dump($unique);
                    if ($unique) {
                        return false;
                    }
                }

                //เพิ่มและแก้ไขข้อมูล system
                $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, $evenFields['target']['ezf_field_name'], '', $special, $userProfile, $evenFields['target'], 0);
                EzfFunc::inProcess($model, $modelEzfTarget->ezf_id, $modelEzf->ezf_table);
                $model->afterFind();
                
            } else { //ฟอร์มค้นหาเป้าหมาย
                return false;
            }
        } else {// ไม่มีเป้าหมาย
            $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset);

            if ($model->id) {
                $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
            } else {
                $dataTarget = [];
            }

            //เพิ่มและแก้ไขข้อมูล system
            $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, NULL, 0);
            $model->afterFind();
        }
 
        if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
            if ($post) {
                $model->load($post);
            }
            
            $rstat_old = isset($model->rstat)?$model->rstat:0;
            
            
            
            if (isset($initdata['rstat'])) {
                $model->rstat = $initdata['rstat'];
            } else {
                $model->rstat = 1;
            }
            
            if($rstat_old==0){
                $model->user_create = $userProfile->user_id;
                $model->create_date = new \yii\db\Expression('NOW()');
            }
                
            $model->user_update = $userProfile->user_id;
            $model->update_date = new \yii\db\Expression('NOW()');
            
            $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
            
            //Sql validate
            $sql_validate = SDUtility::string2Array($modelEzf->ezf_sql);
            $error_validate = [];
            if(isset($sql_validate) && !empty($sql_validate)){
                $update_error = false;
                foreach ($sql_validate as $key_sql => $value_sql) {
                    $data_validate;
                    try {
                        $model_sql = EzfUiFunc::modelSqlBuilder($value_sql);
                        $params_v = [':id'=>$model->id, ':target'=>$target];
                         if($model_sql){
                             $sql_builder = SDUtility::string2Array($model_sql->sql_builder);
                             $query_ex = EzfUiFunc::queryBuilder($sql_builder, $params_v);
                             if($query_ex){
                                 if($model_sql->sql_load == 2){
                                     $data_validate = $query_ex->createCommand()->queryOne();
                                 } else {
                                     $data_validate = $query_ex->createCommand()->queryAll();
                                 }
                             }
                         }
                     } catch (\yii\base\Exception $e) {
                         $error_validate[$value_sql] = $e->getMessage();
                     }

                    if($data_validate){
                        $error_validate[$value_sql] = $model_sql->sql_name;
                        $update_error = true;
                    }
                }

                $model->error = SDUtility::array2String($error_validate);
                //end sql validate
                $model->rstat = 1;
                $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);
            }
           
            if($result['status']=='success'){
                return $model;
            } else {
                return false;
            }
            
        }

        return $model;
    }

    public static function getValueEzform($dataInput, $modelField, $data, $reloadDiv='') {
        $varname = $modelField['ezf_field_name'];
        
        if ($dataInput && !empty($dataInput['system_class'])) {
            $inputWidget = Yii::createObject($dataInput['system_class']);   
            
            $share_options = SDUtility::string2Array($modelField['share_options']);
            $showInput = false;

            if(isset($share_options['type']) && $share_options['type']>0){
                if($share_options['type']==1){
                    $user_share = isset($share_options['user'])?$share_options['user']:[];
                    if(in_array(Yii::$app->user->id, $user_share) || $data['user_create'] == Yii::$app->user->id){
                        $showInput = TRUE;
                    }
                } else if($share_options['type']==2){
                    $user_share = isset($share_options['user'])?$share_options['user']:[];
                    if(!in_array(Yii::$app->user->id, $user_share) || $data['user_create'] == Yii::$app->user->id){
                        $showInput = TRUE;
                    }
                } else if($share_options['type']==3){
                    if(isset($data[$share_options['field']]) && $data[$share_options['field']]!=''){
                        if(($data[$share_options['field']] == Yii::$app->user->id) || $data['user_create'] == Yii::$app->user->id){
                            $showInput = TRUE;
                        }
                    } else {
                        $showInput = TRUE;
                    }
                } else if($share_options['type']==4){
                    if((isset(Yii::$app->session["ezpw_{$modelField['ezf_id']}_{$modelField['ezf_field_id']}"]) && Yii::$app->session["ezpw_{$modelField['ezf_id']}_{$modelField['ezf_field_id']}"]==$share_options['pw']) || $data['user_create'] == Yii::$app->user->id ){
                        $showInput = TRUE;
                    } 
                }
            } else {
                $showInput = TRUE;
            }
            
            if($showInput){
                $value = $inputWidget->getValue($modelField, $data);
            } else {
                $value = '<div><div><div class="label label-warning">'.Yii::t('ezform', 'For authorized person only').'</div> '.(($share_options['type']==2)? \yii\helpers\Html::a('<i class="glyphicon glyphicon-lock"></i>', '#', ['id'=>"btnpw-{$modelField['ezf_id']}-{$modelField['ezf_field_id']}-{$data['id']}",'class'=>'btn btn-default btn-sm btn-pw']):'').'</div></div>';
            }
            
            $view = Yii::$app->getView();
            $view->registerJs("
                $('#btnpw-{$modelField['ezf_id']}-{$modelField['ezf_field_id']}-{$data['id']}').click(function(){
                    var btn = $(this);

                    $.ajax({
                        method: 'POST',
                        url:'". \yii\helpers\Url::to(['/ezforms2/ezform/form-oauthe', 'ezf_id'=>$modelField['ezf_id'], 'v'=>$modelField['ezf_version'], 'field'=>$modelField['ezf_field_id'], 'dataid'=> $data['id'], 'reloadDiv'=>$reloadDiv])."',
                        dataType: 'JSON',
                        success: function(result, textStatus) {
                            if(result.status == 'success') {
                                btn.parent().html(result.html);
                            } else {
                                ".\appxq\sdii\helpers\SDNoty::show('result.message', 'result.status')."
                            }
                        }
                    });

                    return false;
                });
            ");
//            \appxq\sdii\utils\VarDumper::dump($inputWidget);
            return $value;
        } else {
            if(isset($modelField['ezf_field_ref']) && !empty($modelField['ezf_field_ref']) && isset($data[$varname])){
                $p_field = EzfQuery::getFieldById($modelField['ezf_field_ref']);
                if($p_field){
                    $ezf_field_data = SDUtility::string2Array($p_field['ezf_field_data']);
                    $items;
                    $header;
                    $type;
                    if (isset($ezf_field_data['fields']) && !empty($ezf_field_data['fields'])) {
                        foreach ($ezf_field_data['fields'] as $key => $value) {
                            if(!isset($header) && isset($value['header'])){
                                $header = \yii\helpers\ArrayHelper::map($value['header'], 'col', 'label');
                                $type = \yii\helpers\ArrayHelper::map($value['header'], 'col', 'type');
                            }
                            
                            if($value['attribute']==$varname){
                                
                                if($p_field['ezf_field_type']==76){
                                    if($data[$varname]==1){
                                        $otherValue = '';
                                        if(isset($value['other'])){
                                            $varOther = $value['other']['attribute'];
                                            if($value['other']['type']=='text'){
                                                if(!empty($data[$varOther])){
                                                    $otherValue = ', '.Yii::t('ezform', 'Other:').$data[$varOther].' '.$value['other']['suffix'];
                                                }
                                            } elseif ($value['other']['type']=='select') {
                                                $dataEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($value['other']['ezf_id']);
                                                $modelFields = \backend\modules\ezforms2\classes\EzfQuery::findSpecialOne($value['other']['ezf_id']);

                                                if ($dataEzf) {
                                                    $table = $dataEzf['ezf_table'];
                                                    $ref_id = $value['other']['ref_field'];
                                                    $desc = \appxq\sdii\utils\SDUtility::array2String($value['other']['desc_field']);
                                                    $nameConcat = \backend\modules\ezforms2\classes\EzfFunc::array2ConcatStr($desc);

                                                    if ($nameConcat) {
                                                        $query = new \yii\db\Query();
                                                        $query->select(["`$ref_id` AS id", "$nameConcat AS`name`"]);
                                                        $query->from("`$table`");
                                                        $query->where("rstat not in(0, 3)");
                                                        $query->limit(50);

                                                        if($modelFields){
                                                           $query->andWhere('xsourcex = :site', [':site'=>Yii::$app->user->identity->profile->sitecode]);
                                                        }

                                                        $dataObj = $query->createCommand()->queryAll();

                                                        $dataItems = \yii\helpers\ArrayHelper::map($dataObj, 'id', 'name');
                                                    }
                                                }
                                                $otherValue = ' '.Yii::t('ezform', 'Other:').$dataItems[$data[$varOther]];
                                            }
                                            
                                        }

                                        return 'Yes' . $otherValue;
                                    } else {
                                        return 'No';
                                    }
                                } elseif($p_field['ezf_field_type']==75){
                                    $labelName = '';
                                    $q = $data[$varname];
                                    $dataAddr;
                                    $labelAddr = '';

                                    if($value['label']=='province'){
                                        $sql = "SELECT `PROVINCE_ID` AS id, `PROVINCE_CODE` AS code,`PROVINCE_NAME` AS name FROM `const_province` WHERE `PROVINCE_CODE` = :q";
                                        $dataAddr = Yii::$app->db->createCommand($sql, [':q'=>$q])->queryOne();

                                        $labelAddr = Yii::t('ezform', 'Province');
                                    } else if($value['label']=='amphur'){
                                        $sql = "SELECT `AMPHUR_CODE` AS id,`AMPHUR_NAME` AS name FROM `const_amphur` WHERE `AMPHUR_CODE` = :q";
                                        $dataAddr = Yii::$app->db->createCommand($sql, [':q'=>$q])->queryOne();
                                        $labelAddr = Yii::t('ezform', 'Amphur');
                                    } else if($value['label']=='tumbon'){
                                        $sql = "SELECT `DISTRICT_CODE` as id,`DISTRICT_NAME` as name FROM `const_district` WHERE `DISTRICT_CODE` = :q";
                                        $dataAddr = Yii::$app->db->createCommand($sql, [':q'=>$q])->queryOne();
                                        $labelAddr = Yii::t('ezform', 'Tumbon');
                                    }

                                    if($dataAddr){
                                        //$labelName =  $labelAddr . ':<span class="label label-info" style="font-size: 12px;">'.$dataAddr['name'].'</span>';
                                        $strTrim = trim($dataAddr['name']);
                                        $labelName = "$strTrim";
                                    }

                                    return $labelName;
                                } elseif($p_field['ezf_field_type']==74){
                                    return $data[$varname];
                                } elseif($p_field['ezf_field_type']==77){
                                    if(!isset($items) && isset($value['data'])){
                                        $items = \yii\helpers\ArrayHelper::map($value['data'], 'value', 'label');
                                    }
                                    return isset($data[$varname])?(isset($items[$data[$varname]])?$items[$data[$varname]]:$data[$varname]):NULL;
                                } elseif($p_field['ezf_field_type']==78){
                                    
                                    $keyCol = explode('_', $key);
                                    
                                    $valueItem='';
                                    if($type[$keyCol[1]] == 'textinput'){
                                        $valueItem = $data[$varname];
                                    } elseif ($type[$keyCol[1]] == 'textarea') {
                                        $valueItem = $data[$varname];
                                    } elseif ($type[$keyCol[1]] == 'datetime') {
                                        $valueItem = \appxq\sdii\utils\SDdate::mysql2phpDate($data[$varname]);
                                    } elseif ($type[$keyCol[1]] == 'checkbox') {
                                        $valueItem = $data[$varname]>0?'True':'False';
                                    }
                                    
                                    return $valueItem;
                                } elseif($p_field['ezf_field_type']==915){
                                    return $data[$varname];
                                }
                                
                                
                            }
                        }
                    }
                }
            }
        }
        
        return $data[$varname];
        
    }
    
    public static function getValueEzformTemplate($dataInput, $modelField, $data) {
        $label = $modelField['ezf_field_label'];
        $template = '<span><strong>{label}:</strong><code>{value}</code><span>';
        $path = [
            '{label}'=>$label,
            '{value}'=> self::getValueEzform($dataInput, $modelField, $data),
        ];

        return strtr($template, $path);
    }
    
    public static function getDefaultOptions($dataInput, $modelField) {
        if ($dataInput && !empty($dataInput['system_class'])) {
            $inputWidget = Yii::createObject($dataInput['system_class']);
            $value = $inputWidget->defaultOptions($dataInput, $modelField);
            
            return $value;
        }
        
        return [];
    }
    
    public static function getDefaultFields($dataInput, $modelField) {
        if ($dataInput && !empty($dataInput['system_class'])) {
            $inputWidget = Yii::createObject($dataInput['system_class']);
            $value = $inputWidget->defaultFields($dataInput, $modelField);
            
            return $value;
        }
        
        return [];
    }
    
    public static function getAutoNumber($id){
        $data = \backend\modules\ezforms2\models\EzformAutonum::find()->where('id=:id', [':id'=>$id])->one();
        
        $num = str_pad($data['count'], $data['digit'], '0', STR_PAD_LEFT);
        return $data['prefix'].$num.$data['suffix'];
    }
    
    public static function saveAutoNumber($id){
        $modelAuto = \backend\modules\ezforms2\models\EzformAutonum::find()->where('id=:id', [':id'=>$id])->one();
        if($modelAuto){
            $modelAuto->count = $modelAuto->count+1;
            $modelAuto->save();
            return $modelAuto;
        }
    }

    public static function renderEzform($modelFields, $ezf_input, $form, $model, $modelEzf, $view, $inputDisable=[], $inputVisible=[]){
        $html = '';
        
        foreach ($modelFields as $field) {
            if ($field['ezf_field_type'] > 0 && !in_array($field['ezf_field_type'], $inputVisible)) {
                
                if (isset($field['ezf_field_ref']) && $field['ezf_field_ref'] > 0) {
                    $cloneRefField = EzfFunc::cloneRefField($field);
                    $field = $cloneRefField['field'];
                    $disabled = $cloneRefField['disabled'];
                    if($disabled){
                        $inputDisable[$field['ezf_field_name']]=$disabled;
                    }
                }

                $dataInput;
                if (isset($ezf_input)) {
                    $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], $ezf_input);
                }

                $disabled = isset($inputDisable[$field['ezf_field_name']]) ? 2 : 0;
                
                $user_id = SDUtility::getMillisecTime();
                if(!Yii::$app->user->isGuest){
                    $user_id = Yii::$app->user->id;
                }
                
                $share_options = SDUtility::string2Array($field['share_options']);
                $showInput = false;
                
                if(isset($share_options['type']) && $share_options['type']>0 && isset($share_options['view']) && $share_options['view'] == 1 && $model->user_create == $user_id){
                    $showInput = TRUE;
                } else {
                    if(isset($share_options['type']) && $share_options['type']>0){
                        if($share_options['type']==1){
                            $user_share = isset($share_options['user'])?$share_options['user']:[];
                            if(in_array($user_id, $user_share) ){
                                $showInput = TRUE;
                            } else {
                                $html .= EzfFunc::hideInput($form, $model, $field, $modelEzf->ezf_version,1);
                            }
                        } else if($share_options['type']==2){
                            $user_share = isset($share_options['user'])?$share_options['user']:[];
                            if(!in_array($user_id, $user_share) ){
                                $showInput = TRUE;
                            } else {
                                $html .= EzfFunc::hideInput($form, $model, $field, $modelEzf->ezf_version,1);
                            }
                        } else if($share_options['type']==3){
                            if(isset($model[$share_options['field']]) && $model[$share_options['field']]!=''){
                                if(($model[$share_options['field']] == $user_id) ){
                                    $showInput = TRUE;
                                } else {
                                    $html .= EzfFunc::hideInput($form, $model, $field, $modelEzf->ezf_version,1);
                                }
                            } else {
                                $showInput = TRUE;
                            }
                        } else if($share_options['type']==4){
                            if((isset(Yii::$app->session["ezpw_{$field['ezf_id']}_{$field['ezf_field_id']}"]) && Yii::$app->session["ezpw_{$field['ezf_id']}_{$field['ezf_field_id']}"]==$share_options['pw']) ){
                                $showInput = TRUE;
                            } else {
                                $html .= EzfFunc::hideInput($form, $model, $field, $modelEzf->ezf_version, 2);
                            }
                        } 
                    } else {
                        $showInput = TRUE;
                    }
                }
                
                if($showInput){
                    $html .= EzfFunc::generateInput($form, $model, $field, $dataInput, $disabled, $modelEzf);
                }
                
                if ($field['ezf_condition'] == 1) {
                    if($field['table_field_type']=='field'){
                        $childFields = EzfQuery::getFieldChildrenById($field['ezf_id'], $field['ezf_field_id']);
                        if($childFields){
                            foreach ($childFields as $key_item => $value_item) {
                                $value_item['ezf_field_options'] = $field['ezf_field_options'];
                                EzfFunc::generateCondition($model, $value_item, $modelEzf, $view, $dataInput);
                            }
                        }
                    } else {
                        EzfFunc::generateCondition($model, $field, $modelEzf, $view, $dataInput);
                    }
                            

                    
                }

                if (isset($field['ezf_field_cal']) && $field['ezf_field_cal'] != '') {
                    $cut = preg_match_all("%{(.*?)}%is", $field['ezf_field_cal'], $matches);
                    if ($cut) {
                        $varArry = $matches[1];

                        $createEvent = EzfFunc::genJs($varArry, $model, $field);                        
                        $view->registerJs($createEvent);
                    }  else {
                        $createEvent = EzfFunc::genJs([], $model, $field);
                        $view->registerJs($createEvent);
                    }
                }
            }
        }
        $view->registerJs("$('[data-toggle=\"popover\"]').popover()");
        
        
        $html .= \yii\helpers\Html::activeHiddenInput($model, 'id');
        
        return $html;
    }
    
    public static function getEzformIcon($model, $size=32, $options=[]){
        if(isset($options['class'])){
            $options['class'] .= ' img-rounded';
        } else {
            $options['class'] = 'img-rounded';
        }
        $options['width'] = $size;
        $options['height'] = $size;
        
        return \yii\helpers\Html::img((isset($model['ezf_icon']) && !empty($model['ezf_icon']))?$model['ezf_icon']:Yii::getAlias('@storageUrl/ezform/img/icon_empty_ezform.png'), $options);
    }
    
    public static function queryBuilder($sql_builder, $params=[]){
        $query = new \yii\db\Query();
        
        if(isset($sql_builder['from']) && !empty($sql_builder['from'])){
            $query->from($sql_builder['from']);
        } else {
            return FALSE;
        }
        
        if(isset($sql_builder['join']) && !empty($sql_builder['join'])){
            foreach ($sql_builder['join'] as $key => $value) {
                $query->join($value['type'], $value['table'], $value['on']);
            }
        }
        
        if(isset($sql_builder['select']) && !empty($sql_builder['select'])){
            $query->select($sql_builder['select']);
        }
        
        if(isset($sql_builder['group']) && !empty($sql_builder['group'])){
            $query->groupBy($sql_builder['group']);
        }
        
        if(isset($sql_builder['order']) && !empty($sql_builder['order'])){
            $order = [];
            foreach ($sql_builder['order'] as $key => $value) {
                $value = strtolower($value);
                $sort = $value == 'desc'?SORT_DESC:SORT_ASC;
                $order[$key] = $sort;
            }
            $query->orderBy($order);
        }
        
        if(isset($sql_builder['limit']) && !empty($sql_builder['limit'])){
            $query->limit($sql_builder['limit']);
        }
        
        if(isset($sql_builder['where']) && !empty($sql_builder['where'])){
            $params_sql = [];
            if(isset($sql_builder['params']) && !empty($sql_builder['params'])){
                
                foreach ($sql_builder['params'] as $key => $value) {
                    if(isset($params[$value])){
                        $params_sql[$value] = $params[$value];
                    } else {
                        $params_sql[$value] = '';
                    }
                }
            }
            
            if(isset($sql_builder['system']) && !empty($sql_builder['system'])){
                $user = Yii::$app->user->identity->profile;
                foreach ($sql_builder['system'] as $key => $value) {
                    if($value == ':xsourcex'){
                        $params_sql[$value] = $user->sitecode;
                    } elseif ($value == ':xdepartmentx') {
                        $params_sql[$value] = $user->department;
                    } elseif ($value == ':user_id') {
                        $params_sql[$value] = $user->user_id;
                    } 
                }
            }
            
            $query->where($sql_builder['where'], $params_sql);
        }
        
        return $query;
    }
    
    public static function queryBuilderExecute($sql_id, $params=[]){
        try {
           $model = self::modelSqlBuilder($sql_id);
            if($model){
                $sql_builder = SDUtility::string2Array($model->sql_builder);
                $query = self::queryBuilder($sql_builder, $params);
                if($query){
                    if($model->sql_load == 2){
                        return $query->createCommand()->queryOne();
                    } else {
                        return $query->createCommand()->queryAll();
                    }
                }
            }
            
            return false;
        } catch (\yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function sqlExecute($sql_id, $params=[]){
        try {
           $model = self::modelSqlBuilder($sql_id);
            if($model){
                $sql_builder = SDUtility::string2Array($model->sql_builder);
                
                $params_sql = [];
                if(isset($sql_builder['params']) && !empty($sql_builder['params'])){

                    foreach ($sql_builder['params'] as $key => $value) {
                        if(isset($params[$value])){
                            $params_sql[$value] = $params[$value];
                        } else {
                            $params_sql[$value] = '';
                        }
                    }
                }
                
                if(isset($sql_builder['system']) && !empty($sql_builder['system'])){
                    $user = Yii::$app->user->identity->profile;
                    foreach ($sql_builder['system'] as $key => $value) {
                        if($value == ':xsourcex'){
                            $params[$value] = $user->sitecode;
                        } elseif ($value == ':xdepartmentx') {
                            $params[$value] = $user->department;
                        } elseif ($value == ':user_id') {
                            $params[$value] = $user->user_id;
                        } 
                    }
                }
                
                if($model->sql_load == 2){
                    return Yii::$app->db->createCommand($model->sql_raw, $params_sql)->queryOne();
                } else {
                    return Yii::$app->db->createCommand($model->sql_raw, $params_sql)->queryAll();
                }
            }
            
            return false;
        } catch (\yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function queryBuilderById($sql_id, $params=[]){
        try {
           $model = self::modelSqlBuilder($sql_id);
          
            if($model){
                $sql_builder = SDUtility::string2Array($model->sql_builder);
                $query = self::queryBuilder($sql_builder, $params);
                 
                if($query){
                    return $query;
                }
            }
            
            return false;
        } catch (\yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }
    
    public static function modelSqlBuilder($sql_id){
        $model = new \backend\modules\ezforms2\models\TbdataAll();
        $model->setTableName('zdata_ezsql');

        $model = $model->find()->where('id=:id AND rstat <> 3 AND sql_success = 1', [':id' => $sql_id])->one();
        if($model){
            return $model;
        }
        
        return false;
    }
}
