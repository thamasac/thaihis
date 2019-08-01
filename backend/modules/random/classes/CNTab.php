<?php
 
namespace backend\modules\random\classes;
 
class CNTab {
    /**
     * 
     * @param type $var 
     * @param type $exit data type integer 1 or 0
     * @return vardump
     */
    public static function varDump($var , $exit){
        \yii\helpers\VarDumper::dump($var, 10, true);
        if($exit == 1){
            exit();
        }
    }

    /**
     * 
     * @param type $option array 
     * $demo = [
              ['icon'=>'fa fa-cut','title'=>'test', 'url'=> yii\helpers\Url::to(['/random/randomization/test']),'active'=>true],
              ['icon'=>'fa fa-floppy-o','title'=>'test2', 'url'=> yii\helpers\Url::to(['/random/randomization/test2'])],
              ['icon'=>'','title'=>'test3', 'url'=> yii\helpers\Url::to(['/random/randomization/test3'])],
              ['icon'=>'','title'=>'test4', 'url'=> yii\helpers\Url::to(['/random/randomization/test4', 'id'=>'12345678'])]
           ];
     * icon exampl fa fa-user
     * @return html tab
     */
    public static function getTab($option, $id=''){
        $id = isset($id) ? $id : 'chanpan';
        $html = "";
        
        $html .= "<div><ul class='nav nav-tabs tabs-up' id='".$id."'>";
        foreach($option as $key=>$o){
            
            if(isset($o['active']) ? $o['active'] : '' == true){
                $html .= "
                    <li class='active'>
                        <a href='".$o['url']."' 
                            data-target='#contacts' 
                            class='media_node active span' 
                            id='contacts_tab' 
                            data-toggle='tabajax' 
                            rel='tooltip'> <i class='".$o['icon']."'></i> ".$o['title']."
                        </a>
                    </li>
                ";
            }else{
                $html .= "
                    <li>
                        <a href='".$o['url']."' 
                            data-target='#contacts' 
                            class='media_node active span' 
                            id='contacts_tab' 
                            data-toggle='tabajax' 
                            rel='tooltip'> <i class='".$o['icon']."'></i> ".$o['title']."
                        </a>
                    </li>
                ";
            }
         }    
        $html .= "</ul>";
            $html .= "<div class='tab-content' style='margin-top:10px;'>";
                $html .= "<div class='tab-pane active' id='contacts'>";
                $html .= "</div>";
                $html .= "<div class='tab-pane' id='friends_list'>";
                $html .= "</div>";
                $html .= "<div class='tab-pane  urlbox span8' id='awaiting_request'>";
                $html .= "</div>";
            $html .= "</div>";
        $html .= "</div>";
        $html .= "
           <script src='".\yii\helpers\Url::to('@web/js/jquery.min.js')."'></script>
            <script>             
                if($('li').hasClass('active')){
                   let \$this =   $('li.active [data-toggle=\'tabajax\']');
                   let loadurl = \$this.attr('href');
                   let targ = \$this.attr('data-target');                   
                   //alert(loadurl);
                   getData(loadurl,targ);             
                }
                $('li [data-toggle=\'tabajax\']').click(function(e) {
                    let \$this = $(this);  
                    let loadurl = \$this.attr('href');
                    let targ = \$this.attr('data-target');
                    getData(loadurl,targ);             
                    \$this.tab('show');
                    return false;
                });

                function getData(loadurl,targ){
                    $(targ).html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
                    $.get(loadurl, function(data) {
                        $(targ).html(data);
                    });
                }
            
            </script>
        ";
        
        
        return $html;
    }
}
