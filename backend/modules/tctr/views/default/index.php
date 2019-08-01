<?php

?>
<div class="row">
    <div class="col-md-12">
        <div class="form-inline" >
            <div class="form-group">
                <input type="text" class="form-control" id="start-loop" placeholder="Enter start loop" name="start">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" id="end-loop" placeholder="Enter end loop" name="end">
            </div>
            <a href="#" id="senddata" class="btn btn-primary mb-2">ดึงข้อมูลทั้งหมด</a>
        </div>
    </div>
</div><div class="clearfix"></div><br>
<div class="row">
    <div class="col-md-12">
        <div class="form-inline" >
            <div class="form-group">
                <input type="text" class="form-control" id="api-key" placeholder="Enter api key" name="end">
            </div>
            <a href="#" id="sendkey" class="btn btn-warning mb-2">อัพเดทข้อมูล</a>
            <a href="#" id="sendnct" class="btn btn-warning mb-2">nct</a>
        </div>
    </div>
</div>
<div class="clearfix"></div><br>
<?php
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
$('#senddata').on('click', function() {
    var start = $('#start-loop').val();
    var end = $('#end-loop').val();
    loop = getdata(start,end);
});
function getdata (loop,end){
    $.ajax({
        method: 'GET',
        url: '/tctr/tctr-item/read-all-xmlfile',
        data: {loop:loop},
        datatype: 'json',
        success: function (result) {
            var loop = result.loop + 1 ;
            console.log(loop);
            if(loop == end){
                console.log('end');
            }else{
                    getdata(loop,end);
            }
        }
    });
}
$('#sendkey').on('click', function() {
    var key = $('#api-key').val();
    $.ajax({
        method: 'GET',
        data : {key:key},
        url: '/tctr/tctr-item/get-lat-lng',
        success: function (result) {
            console.log('Done');
        }
    });
});
$('#sendnct').on('click', function() {
    var data = nctdata();
});
function nctdata (){
    $.ajax({
        method: 'GET',
        url: '/tctr/tctr-item/read-all-xml-nct',
        success: function (result) {
            console.log(result);
            if(result == "end"){
                console.log('end');
            }else if(result == "pass"){
                nctdata();
            }else{
                console.log(data);
                nctdata();
            }
        }
    });
}
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
