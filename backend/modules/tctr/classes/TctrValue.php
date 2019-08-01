<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\tctr\classes;
use Yii;

class TctrValue {
    public static function FormatDayEzf($data){
        if($data<>''){
            $f1 =  strtotime(str_replace('/', '-', $data));
            return date('Y-m-d',$f1);
        }else{
            return '';
        }
    }
    public static function GetRecruitment_status($data){
        $changedata="";
        if(!self::getValue($data)){
            return "";
        }
        switch ($data) {
            case "Pending (Not yet recruiting)":
                $changedata=1;
                break;
            case "Recruiting":
                $changedata= 2;
                break;
            case "Active, not recruiting":
                $changedata= 3;
                break;
            case "Enrolling by invitation":
                $changedata= 4;
                break;
            case "Completed":
                $changedata= 5;
                break;
            case "Suspended":
                $changedata= 6;
                break;
            case "Terminated (Halted Prematurely)":
                $changedata = 7;
                break;
            case "Withdrawn":
                $changedata = 8;
                break;
        }
        return $changedata;
    }
    public static function GetStudy_type($data){
        $changedata="";
        switch ($data) {
            case "Interventional":
                $changedata= 1;
                break;
            case "Observational":
                $changedata= 2;
                break;
        }
        return $changedata;
    }
    public static function GetStudy_design($data){
        $changedata="";
        switch ($data) {
            case "N/A":
                $changedata= 1;
                break;
            case "Randomized controlled trial":
                $changedata= 2;
                break;
            case "Non-randomized":
                $changedata= 3;
                break;
        }
        return $changedata;
    }

    public static function GetPhase($data){
        $changedata="";
        switch ($data) {
            case "N/A":
                $changedata= 1;
                break;
            case "Phase 0":
                $changedata= 2;
                break;
            case "Phase 1":
                $changedata= 3;
                break;
            case "Phase 1/Phase 2":
                $changedata= 4;
                break;
            case "Phase 2":
                $changedata= 5;
                break;
            case "Phase 2/Phase 3":
                $changedata= 6;
                break;
            case "Phase 3":
                $changedata= 7;
                break;
            case "Phase 4":
                $changedata= 8;
                break;
        }
        return $changedata;
    }

    public static function Getcondition($data){
        if($data=="1"){
            return "Yes";
        }elseif($data=="2"){
            return "No";
        }else{
            return "";
        }
    }
    public static function getValue($val){
        if(isset($val) and !empty($val)){
            return $val;
        }else{
            return '';
        }
    }
}
