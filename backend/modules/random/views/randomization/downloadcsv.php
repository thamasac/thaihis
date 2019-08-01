<?php $this->registerJsFile(yii\helpers\Url::to('@web/js/jquery.tabletoCSV.js')); ?>
 
                
<table class="table table-bordered" id="<?=$table_id?>">
    <thead>
        <tr>
            <th>No. </th>
            <th>block </th>
            <th>identifier </th>
            <th>block size </th>
            <th>sequence within block </th>
            <th>treatment</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($out as $d):
//            \appxq\sdii\utils\VarDumper::dump($out);
            ?>            
            <?php if ($d['block'] != 0 || $d['block'] != null): ?>
                <tr>
                    <td><?= $d['No.'] ?></td>
                    <td><?= $d['block'] ?></td>
                    <td><?= $d['block_size'] ?></td>
                    <td><?= $d['block_num'] ?></td>
                    <td><?= $d['group'] ?></td>
                    <td><?= $d['code'] ?></td>
                </tr>
            <?php endif; ?>
<?php endforeach; ?>
    </tbody>    
</table>
<?php
$this->registerJs("
//    if('" . $type . "' != '1'){
//        $('#myTable').tableToCSV();
//    }
")?>