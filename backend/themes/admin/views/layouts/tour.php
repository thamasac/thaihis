<?php 
        $data = [
          [
              'element'=>'#btnSettingProject',
              'title'=>'Setting Project',
              'content'=>'Setting Project',
              
          ],
          [
              'element'=>'#btnCloneProjects',
              'title'=>'Clone Project',
              'content'=>'Clone Project',
          ],
          [
              'element'=>'#btnEditThemes',
              'title'=>'Themes Project',
              'content'=>'Themes Project',
          ]  
        ]; 

echo \cpn\chanpan\widgets\BootstrapTourWidget::widget([
    'data'=>$data
]);