<?php
$options['title'] ="World Clinical Trials Registry";
$options['ezf_id'] ="1520776142078903600";
$options['fields'] =[
    0 => 'trial_id',
    1 => 'public_title',
    2 => 'acronym',
    3 => 'recruitment_status',
];
$options['zoom'] = 4;
$title = $options['title'];
$ModalView = '<div id="modal-view" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" integrity="sha512-07I2e+7D8p6he1SIM+1twR5TIrhUQn9+I6yjqD53JQjFiMf8EtC93ty0/5vJTZGF8aAocvHYNEDJajGdNx1IsQ==" crossorigin="" />
<script src="/leaflet.markercluster/leaflet-src.js" ></script>
<link rel="stylesheet" href="/leaflet.markercluster/screen.css" />
<link rel="stylesheet" href="/leaflet.markercluster/MarkerCluster.css" />
<link rel="stylesheet" href="/leaflet.markercluster/MarkerCluster.Default.css" />
<script src="/leaflet.markercluster/leaflet.markercluster-src.js"></script>

<div class="panel panel-primary" >
    <div class="panel-heading">
        <h3 class="panel-title"><?= $title ?></h3>
    </div>
    <div class="panel-body" id="show-map"></div>
</div>
<?=$ModalView?>
<?php
$this->registerCSS("
.marker{
    cursor: pointer;
    text-decoration: underline;
    color:#0095ff;
}
#Filter {
    position: absolute;
    background-color:white;
    margin-top: 150px;
    margin-left: 25px;
    float: right;
    left: 10px;
    top: 50px;  
    width: 250px;
    z-index: 1000;
}
.mycluster {
    z-index: 174;  
    text-align: center;
    background-color: rgba(110, 204, 57, 0.75);
    border-radius: 20px;
    padding-top: 10px;
}

");
?>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var options =<?= json_encode($options); ?>;
    $.ajax({
        method: 'POST',
        url: '/tctr/tctr-data/show-map',
        dataType: 'HTML',
        data: options,
        success: function (result) {
            $('#show-map').html(result);
        }
    });
    
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
