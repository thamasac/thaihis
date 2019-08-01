<div id="chanpan_progress">
    <span class="progress_bar first" >
        <a id="step1" class="active" href="#" data-value='1'>All Modules of the Project.</a>
    </span>
    <span class="progress_bar second">
        <a id="step2"  href="#" data-value='2'>User Management</a></span>
</span>
<span class="progress_bar second">
    <a id="step3" href="#" data-value='3'>Subject Management System</a>
</span>
<div id="step-content">
    <div id="show-content"></div>
</div>
</div>
<?php
$this->registerJs("
        var step = 1;
        var selector = '#chanpan_progress span a';
        $(selector).on('click', function(){
            if(!$(this).hasClass('active')){
                $(selector).removeClass('active');
                $(this).addClass('active');
                $(this).attr('disabled', true);
                let value = $(this).attr('data-value');
                loadData(value);
            }
            
        });
        
        loadData=function(value){
            let url = '" . yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=']) . "';  
            if(value=='1'){
                url += '1520817413032262200';
            }else if(value=='2'){
                url += '1525120307083271600';
            }else if(value =='3'){
                url += '1521807350087906600&addon=0&tab=1521807381035975500';
            }
            $('#show-content').html(`<div class='sdloader'><i class='sdloader-icon'></i></div>`);
            $.get(url, function(data){
               $('#show-content').html(data);  
            });
            return false;
        }
        loadData(1);
    ");
?>
<?php
$this->registerCss("
         .progress_bar {
            background-color: #eee;
            box-sizing: border-box;
            display: table-cell;
            vertical-align: middle;

        }
        #chanpan_progress .progress_bar a {
            width: 100em;
            height: 40px;
            line-height: normal;
            padding: 0 10px;
            text-align: center;
            display: table-cell;
            color: #999;
            vertical-align: middle;
            text-decoration: none;
            border-left: 1px solid #fff;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        #chanpan_progress .progress_bar a.active {
            color: #fff;
            background-color: #1ab7ea;
            position: relative;
            width: 100em;
        }
        #chanpan_progress .progress_bar a.active::after {
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 15px solid #1ab7ea;
            bottom: -15px;
            content: '';
            height: 0;
            left: 50%;
            margin: auto;
            position: absolute;
            -webkit-transform: translateX(-50%);
            -moz-transform: translateX(-50%);
            transform: translateX(-50%);
            width: 0;
            z-index: 1;
        } 
    ");
?>