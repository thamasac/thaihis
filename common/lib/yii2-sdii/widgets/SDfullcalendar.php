<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace appxq\sdii\widgets;


use Yii;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
/**
 * Description of SDfullcalendar
 *
 * @author appxq
 */
class SDfullcalendar extends \yii2fullcalendar\yii2fullcalendar {
    //put your code here
    protected function registerPlugin()
    {
        $id = $this->options['id'];
        $view = $this->getView();

        /** @var \yii\web\AssetBundle $assetClass */
        $assets = \yii2fullcalendar\CoreAsset::register($view);

        //by default we load the jui theme, but if you like you can set the theme to false and nothing gets loaded....
        if($this->theme == true)
        {
            \yii2fullcalendar\ThemeAsset::register($view);
        }

        if (isset($this->options['lang']))
        {
            $assets->language = $this->options['lang'];
        }

        if ($this->googleCalendar)
        {
            $assets->googleCalendar = $this->googleCalendar;
        }

        $js = array();

        if($this->ajaxEvents != NULL){
            $this->clientOptions['events'] = $this->ajaxEvents;
        }

        if(is_array($this->header) && isset($this->clientOptions['header']))
        {
            $this->clientOptions['header'] = array_merge($this->header,$this->clientOptions['header']);
        } else {
            $this->clientOptions['header'] = $this->header;
        }

		if(isset($this->defaultView) && !isset($this->clientOptions['defaultView']))
        {
            $this->clientOptions['defaultView'] = $this->defaultView;
        }

        // clear existing calendar display before rendering new fullcalendar instance
        // this step is important when using the fullcalendar widget with pjax
        $js[] = "var loading_container = jQuery('#$id .fc-loading');"; // take backup of loading container
        $js[] = "jQuery('#$id').empty().append(loading_container);"; // remove/empty the calendar container and append loading container bakup

        $cleanOptions = $this->getClientOptions();
        $js[] = "setTimeout(function(){ jQuery('#$id').fullCalendar($cleanOptions); }, 10);";

        /**
        * Loads events separately from the calendar creation. Uncomment if you need this functionality.
        *
        * lets check if we have an event for the calendar...
            * if(count($this->events)>0)
            * {
            *    foreach($this->events AS $event)
            *    {
            *        $jsonEvent = Json::encode($event);
            *        $isSticky = $this->stickyEvents;
            *        $js[] = "jQuery('#$id').fullCalendar('renderEvent',$jsonEvent,$isSticky);";
            *    }
            * }
        */

        $view->registerJs(implode("\n", $js),View::POS_READY);
    }
}
