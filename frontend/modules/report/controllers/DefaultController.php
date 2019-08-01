<?php

namespace app\modules\report\controllers;

use appxq\sdii\utils\VarDumper;
use yii\web\Controller;
use Yii;
/**
 * Default controller for the `report` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
        $url = isset($url) ? $url : '/tctr/default/index';
        $array = \backend\modules\core\classes\CoreFunc::getParams('tab_report', 'url');
        $array = json_decode(stripslashes($array),true);
        if(!$array){
            $item = [];
        }else {
            foreach ($array['tab'] as $value) {
                $item[] = [
                    'label' => $value['label'],
                    'url' => '/report/default/index?tab=' . $value['variable'],
                    'active' => $tab == $value['variable'],
                ];
                if ($tab == $value['variable']) {
                    $url = $value['url'];
                }
            }
        }
        return $this->render('index',[
            'url' => $url,
            'item' => $item
        ]);
    }
}
