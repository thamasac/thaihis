<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\db\Query;

/**
 * Description of RandomizationFunc
 *
 * @author AR9
 */
class RandomizationFunc {

    //put your code here

    public static function authAdmin() {
        if (Yii::$app->user->can("administrator") || Yii::$app->user->can("adminsite")) {
            return TRUE; //admin ใหญ่
        }
    }

    public static function authUser($random_id = '') {
        if ($random_id != '') {
            $query = new Query();
            $user_create = $query->select('id')->from('random_code')->where(['user_create' => Yii::$app->user->id, 'id' => $random_id])->one();
            if ($user_create) {
                return true;
            }
        } else {
            return false;
        }
    }

    public static function randomString($length = 6, $seed) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('A', 'Z'));
        $num = array_merge(range('0', '9'), range('0', '9'));
        $func = function (){return mt_rand();};
        mt_srand($seed);
        $order = array_map($func, range(1, count($characters)));
        array_multisort($order, $characters);


        $order2 = array_map($func, range(1, count($num)));
        array_multisort($order2, $num);

        return $characters[0] . $characters[1] . $num[0];
    }

    public static function getRandomBlock($options, $status) {
        ob_start();
        $seed = (int) $options['seed']; //seed
        $list_length = (int) $options['list_length']; //size
        $block_size = explode(',', $options['block_size']); //block size
        $group = explode(',', $options['treatment']); //group a , b         

        $blocksizeinit = $block_size; //array(4, 6, 8);

        $rs = 0;
        $a = 0;
        $b = 0;
        while ($rs <= $list_length) {
            $seed++;
            $bs = $blocksizeinit;
            $func = function (){return mt_rand();};
            mt_srand($seed);
            $order = array_map($func, range(1, count($blocksizeinit)));
            array_multisort($order, $bs);
            $roundarr[] = $bs[0];
            $rs = $rs + $bs[0];
        }
//        var_dump($roundarr);
//        echo $rs; //จำนวนรวม
//        exit;
//$roundarr=array(6,4,4,6,8,8,4,6,8);
        $html = "No., block ,identifier ,block size ,sequence within block ,treatment \n";
        $no = 1;
        foreach ($roundarr as $round => $blocksize) {
            //$round=1;
            //$blocksize=6;
            //ต้องสร้าง arr ตามblocksize
            foreach ($group as $k => $v) {
                for ($i = 0; $i < $blocksize; $i++) {
                    $arr[] = $k;
                }
            }
            //$arr = array(0,0,0,0,0,0,1,1,1,1,1,1);
            //var_dump($arr);
            $func = function (){return mt_rand();};
//            $html .= ($round+1).", ";
            mt_srand($seed++);
            $order = array_map($func, range(1, count($arr)));
            array_multisort($order, $arr);
            for ($i = 0; $i < $blocksize; $i++) {
                $allrand[] = $arr[$i];
                $a = $a + ($arr[$i] == 0 ? 1 : 0);
                $b = $b + ($arr[$i] == 1 ? 1 : 0);
                //$c=$c+($arr[$i]==2 ? 1:0);
                if ($status == 1) {
                    $html .= ($no++).", ".($round + 1) . ", {$blocksize}, " . ( $i + 1) . ", " . $group[$arr[$i]] . ", " . RandomizationFunc::randomString(3, $seed++) . "\n";
                } else {
                    $html .= ($no++).", ".($round + 1) . ", {$blocksize}, " . ($i + 1) . ", " . $group[$arr[$i]] . ", " . RandomizationFunc::randomString(3, $seed++) . "|\n";
                }
            }
            unset($order);
            unset($arr);
        }
        echo $html;

//        ob_end_flush();
        // echo "All random = ", count($allrand)," [$b,$b,$c]<br>";
    }

}
