<dl class="dl-horizontal">
  <dt style="width: 35px;">Dx:</dt>
  <dd style="margin-left: 40px;">
    <?= $model['di_txt']; ?><br>   
    <?php
    if ($model['di_icd10']) {
        echo '<code style="font-size:100%;">' . \backend\modules\patient\classes\PatientQuery::getIcd10Fulltxt($model['di_icd10']) . '</code>';
        ?>
        <strong>Primary diagnosis : </strong>
        <?php
    }
    if ($dataDiagComo) {
        ?>
        <strong>Comorbidity : </strong> 
        <?php
        foreach ($dataDiagComo as $value) {
            echo '<code style="font-size:100%;">' . $value['di_icd10_code'] . '</code> : ' . $value['di_icd10_name'] . '<br>';
        }
    }
    if ($dataDiagComp) {
        ?>
        <strong>Complication : </strong>
        <?php
        foreach ($dataDiagComp as $value) {
            echo '<code style="font-size:100%;">' . $value['di_icd10_code'] . '</code> : ' . $value['di_icd10_name'] . '<br>';
        }
    }
    if ($dataOperat) {
        ?>
        <strong>Operation : </strong>
        <?php
        foreach ($dataOperat as $value) {
            echo '<code style="font-size:100%;">' . $value['di_icd9_code'] . '</code> : ' . $value['di_icd9_name'] . '<br>';
        }
    }
    ?>
  </dd>
</dl>