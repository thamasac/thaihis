<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezforms2\controllers;
use Yii;
use yii\web\Controller;
/**
 * Description of AssignRoleController
 *
 * @author damasac
 */
class AssignRoleController extends Controller{
    //put your code here
    public function actionIndex(){
        $ezf_id = isset($_GET['ezf_id']) ? $_GET["ezf_id"] : "";
        return $this->renderAjax("index",["ezf_id"=>$ezf_id]);
       
    }
}
