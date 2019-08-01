<?php
/**
 * Created by PhpStorm.
 * User: AR9
 * Date: 24/10/2561
 * Time: 11:25
 */

namespace backend\modules\random\classes;


use yii\base\Component;
use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

class RandomWidget extends Component
{

    public $reloadDiv = 'divRandom';
    public $color = 'panel-primary';
    /**
     *
     * @param type $reloadDiv
     * @return $this
     */
    public function reloadDiv($reloadDiv)
    {
        $this->reloadDiv = $reloadDiv;
        return $this;
    }

    /**
     * @param $color
     * @return $this
     */
    public function color($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return RandomWidget|object
     * @throws \yii\base\InvalidConfigException
     */
    public static function ui()
    {
        return Yii::createObject(RandomWidget::className());
    }

    /**
     * @return string
     */
    public function buildUi(){
        $options = [];
//        $this->registerClientScript();
        $url = Url::to(['/random/randomization/setting',
            'reloadDiv' => $this->reloadDiv,
            'color' => $this->color
            ]);

//        $this->reloadDiv =  isset($this->options['reloadDiv']) ? $this->options['reloadDiv'] : 'random-'.SDUtility::getMillisecTime();
        $options['id'] = $this->reloadDiv;
        $options['data-url'] = $url;

        $view = Yii::$app->getView();
        $view->registerJs("
            getUiAjax('{$url}', '{$this->reloadDiv}');
        ");

        return Html::tag('div', '<div class="sdloader"><i class="sdloader-icon"></i></div>', $options);
    }
}