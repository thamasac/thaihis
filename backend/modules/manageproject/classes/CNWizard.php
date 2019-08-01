<?php

namespace backend\modules\manageproject\classes;

class CNWizard {

    /**
     * 
     * @param type array $stepArr  ['step'=>'#step-1', 'name'=>'Step 1<br /><small>Step 1 description</small>']
     * @return wizard step
     */
    public static function getDynamicWizard($stepArr, $defaultStep = 0, $finishAjaxUrl='', $project_name='') {

        $wizard = "<div id='toTop'>";
        $wizard .= "<link rel='stylesheet' href='" . \yii\helpers\Url::to('@web/wizard/css/bootstrap.min.css') . "'>";
//        $wizard .= "<link rel='stylesheet' href='" . \yii\helpers\Url::to('@web/wizard/css/bootstrap-theme.min.css') . "'>";
        $wizard .= "<link rel='stylesheet' href='" . \yii\helpers\Url::to('@web/wizard/css/smart_wizard.min.css') . "'>";
//        $wizard .= "<link rel='stylesheet' href='" . \yii\helpers\Url::to('@web/wizard/css/smart_wizard_theme_circles.min.css') . "'>";
        $wizard .= "<link rel='stylesheet' href='" . \yii\helpers\Url::to('@web/wizard/css/smart_wizard_theme_arrows.min.css') . "'>";
        $wizard .= "<link rel='stylesheet' href='" . \yii\helpers\Url::to('@web/wizard/css/smart_wizard_theme_dots.min.css') . "'>";

        $wizard .= "<script src='" . \yii\helpers\Url::to('@web/wizard/js/jquery.min.js') . "'></script>";
        $wizard .= "<script src='" . \yii\helpers\Url::to('@web/wizard/js/jquery.smartWizard.min.js') . "'></script>";
        


        $wizard .= " <div class='container' >           
            <div  id='smartwizard'>
                <ul>";
        foreach ($stepArr as $st) {
            $wizard .= "<li><a href='#" . $st['step'] . "'>" . $st['name'] . "</a></li>";
        }

        $wizard .= "</ul><div>";
        foreach ($stepArr as $st) {
            $wizard .= "<div id='" . $st['step'] . "' class=''>{$st['data']}</div>";
        }


        $wizard .= "</div>
            </div>
            </div></div>
        ";

        $wizard .= "
          <script>
                
               
                $('#smartwizard').smartWizard({
                    selected: {$defaultStep},
                    theme: 'default',
                    transitionEffect: 'slide',
                    toolbarSettings: {toolbarPosition: 'both',
                        toolbarExtraButtons: [
                            {label: 'Finish', css: 'btn-success', onClick: function () {
                                    Finish();
                                }},
 
                        ]
                    }
                });
                //
                 

            $('#smartwizard').smartWizard('theme', 'arrows');
            
            //finish
            Finish = function(){
              location.href = '".$finishAjaxUrl."';
            }
             
            scrollTopNav = function(){
              $('html, body').animate({scrollTop:0}, 'slow');
		return false;
            }
            scrollTopNav();
          </script>
        ";
        $wizard .= "<style>
            .navbar-inverse {
                background: #428bca;
                border-color: #357ebd;
            }
            .navbar-inverse .navbar-nav > li > a {
                color: #EEE;
            }
            .navbar-inverse .navbar-brand {
                margin-left: 15px;
                margin-right: 15px;
                font-weight: bold;
                color: #EEE;
                text-shadow: 0 1px 0 rgba(255,255,255,.1), 0 0 30px rgba(255,255,255,.125);
                -webkit-transition: all .2s linear;
                -moz-transition: all .2s linear;
                transition: all .2s linear;
            }
            .navbar-inverse .navbar-nav>.open>a, .navbar-inverse .navbar-nav>.open>a:focus, .navbar-inverse .navbar-nav>.open>a:hover{
                    background: #357ebd;
            }
            .sw-theme-arrows .sw-container {
                min-height: 1000px;
                /* height: auto; */
            }
            .sw-main .sw-toolbar {
                margin-left: 0;
                -webkit-box-shadow: none;
                -moz-box-shadow: 0px 1px 3px 0px rgba(0,0,0,0.3);
                box-shadow: none;
                background: transparent;
                border: none;
                z-index: 0 !important;
            }
            .sw-theme-arrows>ul.step-anchor {
    
            }
            .sw-theme-arrows>ul.step-anchor>li>a {
                color: #666;
                text-decoration: none;
                padding: 10px 0 10px 45px;
                position: relative;
                display: block;
                float: left;
                border-radius: 0;
                outline-style: none;
                background: #ddd;
            }
            .sw-theme-arrows>ul.step-anchor>li>a:hover {
                color: #fff;
                text-decoration: none;
                outline-style: none;
                background: #ffc107;
                border-color: #ffc107;
            }
        
        </style>
        ";
        $wizard .= "<script src='" . \yii\helpers\Url::to('@web/wizard/js/CNWizard.js') . "'></script>";
        return $wizard;
    }

}
