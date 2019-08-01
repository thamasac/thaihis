<?php
//$theme = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData('zdata_themes', '');
$theme = Yii::$app->params['themes'];
if(isset($theme) && !empty($theme)){
        $this->registerCss("
            /*navbar*/
            .navbar-inverse{
                background-color:".$theme['bg_navbar']."; /*#428bca;*/
                border-color: ".$theme['bg_navbar'].";/*#357ebd;*/
            }
            .navbar-inverse .navbar-nav > .active > a {
                background-color:".$theme['active_navbar']."; /*#357ebd;*/
            }
            .navbar-inverse .navbar-nav > li > a:hover, .navbar-inverse .navbar-nav > li > a:focus {
                color: ".$theme['font_color'].";
                background-color: ".$theme['active_navbar'].";/*#3276b1;*/
            }
            .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus {
                color: ".$theme['font_color'].";
                background-color: ".$theme['active_navbar'].";/*#3276b1*/;
            }
            /*site map*/
            .sidebar-user{
                background:".$theme['bg_sitemap'].";
                height: 51px; /*background-color: #4dadf7;  */  
                border-bottom: 0px !important;
            }
            /*mobile*/
            ul.page-sidebar-menu > li.active > a {
                background: ".$theme['active_navbar']." !important; /*#428bca*/
                border-top-color: transparent !important;
                color: rgba(255, 255, 255, 0.85);
            }
            .navbar-inverse .navbar-toggle:hover, .navbar-inverse .navbar-toggle:focus {
                background-color: ".$theme['active_navbar'].";
                box-shadow: 0 0 0 1px rgba(255,255,255,0.1);
            }
            .navbar-inverse .navbar-toggle {
                border-color: ".$theme['active_navbar'].";
                box-shadow: 0 0 0 1px rgba(255,255,255,0.1);
            }


            /*Open profile*/
            .navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus {
                color:".$theme['font_color'].";
                background-color: ".$theme['active_navbar'].";#357ebd;
            }
        ");
}else{
    $this->registerCss("            
            /*site map*/
            .sidebar-user{
                background:#4dadf7;
                height: 51px; /*background-color: #4dadf7;  */  
                border-bottom: 0px !important;
            }            
        ");
}
?>


<?php 
$this->registerJs("
    $('#btnEditThemes').on('click', function(){
        let url = $(this).attr('href');
        $('#modal-themes').modal('show');
        $('#modal-themes .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');

        $.get(url, function(data){
             $('#modal-themes .modal-content').html(data); 
        });
        return false;
    });
");
?>