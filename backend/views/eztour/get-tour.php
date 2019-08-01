<?php
 

$data = [];
foreach($eztour as $k=>$v){
    $data[$k] = [
        'element'=>$v['element'],
        'title'=>$v['title'],
        'content'=>$v['content'],
        'placement'=> $v['placement'],
        'smartPlacement'=> $v['smartPlacement'],
    ];   
}
    echo \cpn\chanpan\widgets\BootstrapTourWidget::widget([
        'data'=>$data
    ]); 
    
    //\appxq\sdii\utils\VarDumper::dump($data);
?>

<?php
//   echo \cpn\chanpan\widgets\BootstrapTourWidget::widget([
//         'data'=>[
//             [
//                'element'=>'.btnT1',
//                'title'=>'ปุ่มสำหรับสร้างโครงการ',
//                'content'=>'ปุ่มสำหรับสร้างโครงการ',
//                'placement'=> 'auto',
//                'smartPlacement'=> true, 
//            ]
//         ] 
//    ]);
       
        
?>