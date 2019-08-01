<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\modules\tctr\classes;

use Yii;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfUiFunc;
use ZipArchive;
use backend\modules\tctr\classes\TctrValue;
use yii\helpers\Url;
class TctrFunction {

    public static function QueryData($dataid, $table, $fieldkey) {
        $sql = "select * FROM `$table` WHERE `$fieldkey` = :id AND rstat not in(0,3)";
        return Yii ::$app->db->createCommand($sql, [':id' => $dataid])->queryAll();
    }

    public static function QuerysectionB($dataid, $table, $fieldkey) {
        $sql = "select site_country FROM `$table` WHERE `$fieldkey` = :id AND rstat not in(0,3)";
        return Yii ::$app->db->createCommand($sql, [':id' => $dataid])->queryAll();
    }
    public static function getAllChoice($ezf_id,$field_name) {
        $sql = "select 
            ezf_choicevalue as `value`,
            ezf_choicelabel 
            from ezform_fields fid
            INNER JOIN ezform_choice cho on cho.ezf_field_id = fid.ezf_field_id
            where fid.ezf_id=:ezf_id
            and fid.ezf_field_name=:field_name ";
        return Yii::$app->db->createCommand($sql, [
            ':ezf_id' => $ezf_id,
            ':field_name' => $field_name]
        )->queryAll();
     }
    public static function getOneChoice($ezf_id,$field_name,$choicevalue) {
        if(isset($choicevalue)){
            $sql = "select 
                cho.ezf_choicelabel 
                from ezform_fields fid
                INNER JOIN ezform_choice cho on cho.ezf_field_id = fid.ezf_field_id
                where fid.ezf_id=:ezf_id
                and fid.ezf_field_name=:field_name
                and cho.ezf_choicevalue=:ezf_choicevalue";
            return Yii::$app->db->createCommand($sql, [
                ':ezf_id' => $ezf_id,
                ':field_name'=> $field_name,
                ':ezf_choicevalue' => $choicevalue,
            ])->queryScalar();
        }else{
            return '';
        }
    }
    public static function QueryAllMarkmap($where) {
        $sql = "select id,trial_id,city,public_title,map_lat,map_lng from
                (
                    select * from 
                        (
                            select contact_target,city,map_lat,map_lng FROM `zdata_tctr_part_sectionc`
                            WHERE rstat not in(0,3)
                            UNION
                            select contact_target,city,map_lat,map_lng FROM `zdata_tctr_part_sectiond` 
                            WHERE rstat not in(0,3)
                        ) as sys 
                    GROUP BY contact_target,map_lat,map_lng
                ) as sys
                INNER JOIN zdata_tctr_main main on sys.contact_target = main.`id`
                 $where
                where map_lat is not null and map_lng is not null ";
        $data = Yii ::$app->db->createCommand($sql)->queryAll();
        $changedata = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $changedata[$key][0] = "Trial_id:".$value['trial_id']."<br>"
                                     . "Public_Title:<a class='marker' href=".Url::to(['/tctr/tctr-data/ezform-view',
                                        'ezf_id' => '1520776142078903600',
                                        'dataid' => $value['id'],
                                        'modal' => 'modal-view',
                                         'reloadDiv' => 'modal-view',
                                        'type' => 'map',
                                    ])." "
                                     . ">".$value['public_title']."</a>";
                $changedata[$key][1] = $value['map_lat'];
                $changedata[$key][2] = $value['map_lng'];
            }
        }
        return json_encode($changedata);
    }
}
