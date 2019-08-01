<?php

 
namespace common\modules\user\classes;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\authclient\ClientInterface; 
class CNAuthChoice extends Widget{
 
 
    /**
     * @var string name of the auth client collection application component.
     * This component will be used to fetch services value if it is not set.
     */
    public $clientCollection = 'authClientCollection';
    /**
     * @var string name of the GET param , which should be used to passed auth client id to URL
     * defined by [[baseAuthUrl]].
     */
    public $clientIdGetParamName = 'authclient';
    /**
     * @var array the HTML attributes that should be rendered in the div HTML tag representing the container element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var array additional options to be passed to the underlying JS plugin.
     */
    public $clientOptions = [];
    /**
     * @var bool indicates if popup window should be used instead of direct links.
     */
    public $popupMode = true;
    /**
     * @var bool indicates if widget content, should be rendered automatically.
     * Note: this value automatically set to 'false' at the first call of [[createClientUrl()]]
     */
    public $autoRender = true;

    /**
     * @var array configuration for the external clients base authentication URL.
     */
    private $_baseAuthUrl;
    /**
     * @var ClientInterface[] auth providers list.
     */
    private $_clients;


    /**
     * @param ClientInterface[] $clients auth providers
     */
    public function setClients(array $clients)
    {
        $this->_clients = $clients;
    }

    /**
     * @return ClientInterface[] auth providers
     */
    public function getClients()
    {
        if ($this->_clients === null) {
            $this->_clients = $this->defaultClients();
        }

        return $this->_clients;
    }

    /**
     * @param array $baseAuthUrl base auth URL configuration.
     */
    public function setBaseAuthUrl(array $baseAuthUrl)
    {
        $this->_baseAuthUrl = $baseAuthUrl;
    }

    /**
     * @return array base auth URL configuration.
     */
    public function getBaseAuthUrl()
    {
        if (!is_array($this->_baseAuthUrl)) {
            $this->_baseAuthUrl = $this->defaultBaseAuthUrl();
        }

        return $this->_baseAuthUrl;
    }

    /**
     * Returns default auth clients list.
     * @return ClientInterface[] auth clients list.
     */
    protected function defaultClients()
    {
        /* @var $collection \yii\authclient\Collection */
        $collection = Yii::$app->get($this->clientCollection);

        return $collection->getClients();
    }

    /**
     * Composes default base auth URL configuration.
     * @return array base auth URL configuration.
     */
    protected function defaultBaseAuthUrl()
    {
        $baseAuthUrl = [
            Yii::$app->controller->getRoute()
        ];
        $params = Yii::$app->getRequest()->getQueryParams();
        unset($params[$this->clientIdGetParamName]);
        $baseAuthUrl = array_merge($baseAuthUrl, $params);

        return $baseAuthUrl;
    }

    /**
     * Outputs client auth link.
     * @param ClientInterface $client external auth client instance.
     * @param string $text link text, if not set - default value will be generated.
     * @param array $htmlOptions link HTML options.
     * @return string generated HTML.
     * @throws InvalidConfigException on wrong configuration.
     */
    public function clientLink($client, $text = null, array $htmlOptions = [])
    {
        $viewOptions = $client->getViewOptions();

        if (empty($viewOptions['widget'])) {
            if ($text === null) {
                $text = Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]);
            }
            if (!isset($htmlOptions['class'])) {
                $htmlOptions['class'] = $client->getName();
            }
            if (!isset($htmlOptions['title'])) {
                
                $htmlOptions['title'] = \Yii::t('chanpan',"Login with ".strtolower($client->getTitle()));
            }
            Html::addCssClass($htmlOptions, ['widget' => 'auth-link']);

            if ($this->popupMode) {
                if (isset($viewOptions['popupWidth'])) {
                    $htmlOptions['data-popup-width'] = $viewOptions['popupWidth'];
                }
                if (isset($viewOptions['popupHeight'])) {
                    $htmlOptions['data-popup-height'] = $viewOptions['popupHeight'];
                }
            }
            $htmlOptions['class']='btn btn-block';
            return Html::a($text.$htmlOptions['title'], $this->createClientUrl($client), $htmlOptions);
        }

        $widgetConfig = $viewOptions['widget'];
        if (!isset($widgetConfig['class'])) {
            throw new InvalidConfigException('Widget config "class" parameter is missing');
        }
        /* @var $widgetClass Widget */
        $widgetClass = $widgetConfig['class'];
        if (!(is_subclass_of($widgetClass, AuthChoiceItem::className()))) {
            throw new InvalidConfigException('Item widget class must be subclass of "' . AuthChoiceItem::className() . '"');
        }
        unset($widgetConfig['class']);
        $widgetConfig['client'] = $client;
        $widgetConfig['authChoice'] = $this;
        return $widgetClass::widget($widgetConfig);
    }

    /**
     * Composes client auth URL.
     * @param ClientInterface $client external auth client instance.
     * @return string auth URL.
     */
    public function createClientUrl($client)
    {
        $this->autoRender = false;
        $url = $this->getBaseAuthUrl();
        $url[$this->clientIdGetParamName] = $client->getId();

        return Url::to($url);
    }

    /**
     * Renders the main content, which includes all external services links.
     * @return string generated HTML.
     */
    protected function renderMainContent()
    {
        $items = [];
        foreach ($this->getClients() as $externalService) { 
            $items[] = Html::tag('li', $this->clientLink($externalService),['class'=>'li'.$externalService->getId()]);
        }
        return Html::tag('ul', implode('', $items), ['class' => 'auth-clients']);
    }

    /**
     * Initializes the widget.
     */
    public function init()
    {
        $view = Yii::$app->getView();
        if ($this->popupMode) {
            AuthChoiceAsset::register($view);
            if (empty($this->clientOptions)) {
                $options = '';
            } else {
                $options = Json::htmlEncode($this->clientOptions);
            }
            $view->registerJs("jQuery('#" . $this->getId() . "').authchoice({$options});");
        } else {
            \yii\authclient\widgets\AuthChoiceStyleAsset::register($view);
            $view->registerCss("
                .auth-clients li {
                    float: left;
                    display: block;
                    margin: 0 1em 0 0;
                    text-align: left;
                    width: 100%;
                    
                }
                
                .auth-icon {
                    float: left;
                    margin: 0;
                }
                li.ligoogle {
                    background: #df4f3f;
                    color: #FFF;
                    margin-left: -19px;
                    border-radius: 3px;
                     margin-top: 5px;
                }
                li.ligoogle a {
                    color: #fff;
                   line-height: 32px;
                   font-weight: bold;
                   font-size: 12pt;
                   font-family: serif;
                   text-align: center;
                  
               }
               .auth-clients {
                    display: block;
                    margin: 0 0 1em;
                    list-style: none;
                    overflow: hidden;
                }
                li.lifacebook {
                    background: #3a5897;
                    color: #FFF;
                    margin-left: -19px;
                    
                    border-radius: 3px;
                }
                li.lifacebook a {
                    color: #fff;
                   line-height: 32px;
                   font-weight: bold;
                   font-size: 12pt;
                   font-family: serif;
                   text-align: center;
                   
               }
               .btn:hover, .btn:focus, .btn.focus {
                    color: #f5ebeb;
                    text-decoration: none;
                }

            ");
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }

    /**
     * Runs the widget.
     * @return string rendered HTML.
     */
    public function run()
    {
        $content = '';
        if ($this->autoRender) {
            $content .= $this->renderMainContent();
        }
        $content .= Html::endTag('div');
        return $content;
    }
 
}
