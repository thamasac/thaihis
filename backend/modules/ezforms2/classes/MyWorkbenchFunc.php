<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\classes;

use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\MyWorkbenchWidget;
use Yii;
use appxq\sdii\utils\SDUtility;

/**
 * Description of MyWorkbenchFunc
 *
 * @author Admin
 */
class MyWorkbenchFunc {

    /**
     * @inheritdoc
     * @return MyWorkbenchWidget the newly created [[MyWorkbenchWidget]] instance.
     */
    public static function workbenchUI($ezf_id) {
        $ui = MyWorkbenchWidget::ui();
        $ui->ezf_id = $ezf_id;

        return $ui;
    }

    public static function modelSearch($model, $ezform, $ezf_match, $ezf_name, $targetField, $colSearch, $params, $pageSize = 50, $order_column = [], $orderby = SORT_DESC) {
        //$model = new TbdataAll();
        $user_id = Yii::$app->user->id;
        $query = new \yii\db\Query();
//        $colSearch[] = 'ezdata_document_name.F2v1';
        $data_matching = $query->select('role_name,user_id')->from($ezf_match['ezf_table'])->where('user_id =' . $user_id)->all();
//        \appxq\sdii\utils\VarDumper::dump($data_matching);
        $role_id = [];
        foreach ($data_matching as $vMatching) {
//            $data_role = SDUtility::string2Array($vMatching['role_name']);
//            foreach ($data_role as $vRole) {
                $role_id[] = $vMatching['role_name'];
//            }
        }
//        \appxq\sdii\utils\VarDumper::dump($role_id);
        $columns = '';
        foreach ($colSearch as $value) {
            $columns .= ',' . $ezform['ezf_table'] . '.' . $value;
        }
        foreach ($role_id as $value) {
            $role_where .= " OR (".$ezform['ezf_table'] . '.final_role LIKE \'%"' . $value . '"%\' ';
        }
        $role_where .= ') ';
//        \appxq\sdii\utils\VarDumper::dump($columns);
        $query = new \yii\db\Query();
        $query->select($ezf_name['ezf_table'] . '.F2v1,' . $ezform['ezf_table'] . '.id,' . $ezform['ezf_table'] . '.user_create,'
                        . $ezform['ezf_table'] . '.ezf_version,' . $ezform['ezf_table'] . '.check_user'
                        . $columns
                )->from($ezform['ezf_table'])
                ->leftJoin($ezf_name['ezf_table'], 'zdata_document_name.id = ' . $ezform['ezf_table'] . '.target')
                ->where($ezform['ezf_table'] . '.rstat not in(0,3) '
                        . 'AND '
                        . $ezform['ezf_table'] . '. final_name LIKE \'%"' . $user_id . '"%\' '.$role_where.''
                        . ' AND '.$ezform['ezf_table'] . '.approve_status = 1 '
                        . 'AND '.$ezform['ezf_table'] . '.approve_choice = 1 '
                        . 'OR ('.$ezform['ezf_table'] . '.approve_choice = 2 '
                        . 'AND '.$ezform['ezf_table'] . '.delay_date >= CURDATE())');
        //->andWhere('assign_name LIKE \'%'.$user_id.'"%\' '); 
//                ->orWhere('assign_name LIKE \'["' . $user_id . '",%\'')//->where('rstat not in(0, 3)');
//                ->orWhere('assign_names LIKE \'%"' . $user_id . '",%\'')
//                ->orWhere('assign_name LIKE \'%,"' . $user_id . '"]\'');
        
        
//        $query->orWhere('delay_date >= CURDATE()');
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

//        $query->innerJoin('profile', "profile.user_id = {$ezform['ezf_table']}.user_update");
//        $query->select([
//            "{$ezform['ezf_table']}.*",
//            "(SELECT const_hospital.`name` FROM const_hospital WHERE const_hospital.code = {$ezform['ezf_table']}.xsourcex ) AS sitename",
//            "concat(profile.firstname, ' ', profile.lastname) AS userby"
//        ]);

        if (isset($modelFields)) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }

        if ($ezform['public_listview'] != 1) {
            $showStatus = self::showListDataEzf($ezform, Yii::$app->user->id);

//            $query->andWhere("user_create=:created_by || $showStatus", [':created_by' => Yii::$app->user->id]);
        }
        $defaultOrder = [];
        foreach ($colSearch as $value) {
            $defaultOrder[] = $value;
        }
        $defaultOrder[] = 'F2v1';
        //\appxq\sdii\utils\VarDumper::dump($defaultOrder);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                'attributes' => $defaultOrder,
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

        if ($targetField != '') {
            $query->andFilterWhere(['like', $targetField, $model[$targetField]]);
        }

        $colSearch = \yii\helpers\ArrayHelper::merge($colSearch, ['id', 'sitecode', 'ptid', 'target', 'xsourcex', 'ptcode', 'hptcode', 'hsitecode', 'rstat']);
//        $query->andFilterWhere([
//            'id' => $model->id,
//        ]);

        foreach ($colSearch as $field) {
            if (is_array($field)) {
                if (isset($field['attribute'])) {
                    $query->andFilterWhere(['like', $field['attribute'], $model[$field['attribute']]]);
                }
            } else {
                $query->andFilterWhere(['like', $field, $model[$field]]);
            }
        }


        return $dataProvider;
    }

    public static function GetUserName($userid) {
        $query = new \yii\db\Query();
        $result = $query->select('firstname,lastname')
                        ->from('profile')->where('user_id=:user_id', [':user_id' => $userid])->one();

        return $result;
    }

    public static function getRole($data) {
        $data_value = [];
        if (!empty($data)) {
            $query = new \yii\db\Query();
            $query->select('user_id')->from('zdata_matching');
            foreach ($data as $key => $value) {
                if ($key <= 0) {
                    $query->where('role_name = :role_name',[':role_name'=>$value]);
                } else {
                    $query->orWhere('role_name = :role_name',[':role_name'=>$value]);
                }
            }
            $data_role = $query->andWhere('user_id != "" OR user_id IS NOT NULL')->all();
            foreach ($data_role as $key => $value) {
                $data_id = SDUtility::string2Array($value['user_id']);
                foreach ($data_id as $vId) {
                    $data_value[] = $vId;
                }
               
            }
        }
        return $data_value;
    }

}
