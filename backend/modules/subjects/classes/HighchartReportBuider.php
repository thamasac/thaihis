<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\subjects\classes;

use \yii\base\Component;
use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Description of HighchartReportBuider
 *
 * @author Admin
 */

/**
 * Description of HighchartReportBuider
 *
 */
class HighchartReportBuider extends Component {

    public $title;
    public $type;
    public $renderDiv;
    public $categories;
    public $graphheight;
    public $plotOptions;
    public $series;
    public $displayDiv = "display_highchart";
    public $dataChart = [];

    /**
     * @param string $dataChart dataChart
     * @return $this the query object itself
     */
    public function dataChart($dataChart = []) {
        $this->dataChart = $dataChart;
        return $this;
    }

    /**
     * @param string $renderDiv renderDiv
     * @return $this the query object itself
     */
    public function renderDiv($renderDiv = null) {
        $this->renderDiv = $renderDiv;
        return $this;
    }
    
        /**
     * @param string $categories categories
     * @return $this the query object itself
     */
    public function graphheight($graphheight = null) {
        $this->graphheight = $graphheight;
        return $this;
    }
    
            /**
     * @param string $categories categories
     * @return $this the query object itself
     */
    public function categories($categories = []) {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @param string $title title
     * @return $this the query object itself
     */
    public function title($title = null) {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $type type
     * @return $this the query object itself
     */
    public function type($type = null) {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritdoc
     * @return HighchartReportBuider the newly created [[HighchartReportBuider]] instance.
     */
    public static function ui() {
        return Yii::createObject(HighchartReportBuider::className()); //, [get_called_class()]
    }

    public function buildHighchart() {

        $view = \Yii::$app->getView();

        $url = Url::to(['/subjects/reports/highchart-report',
                    'title' => $this->title,
                    'plotOptions' => $this->plotOptions,
                    'series' => $this->series,
                    'displayDiv' => $this->displayDiv,
                    'title' => $this->title,
                    'type' => $this->type,
                    'renderDiv' => $this->renderDiv,
                    'categories' => $this->categories,
                    'graphheight' => $this->graphheight,
                    'dataChart' => $this->dataChart,
        ]);
        $options = [];

        if (is_array($options)) {
            $options["id"] = $this->displayDiv;
            $options['data-url'] = $url;
        }

        $view->registerJs("
            getUiAjax('{$url}', '{$this->displayDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }

}
