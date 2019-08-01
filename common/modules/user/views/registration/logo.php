<div class="text-center">
    <img id='logo-login' data-sizes="auto" src="https://www.ncrc.in.th/img/ncrc.png" />
</div>
<?php 
    $this->registerCss("
        .or-box{text-align:center;  
            margin-top:50px;
            margin-bottom:50px;
            position:relative;}
        .or{
            background: #fff;
            position: absolute;
            top: -30px;
            margin: 0 auto;
            right: 45%;
            padding: 9px
        }
        .social{
            position:relative;
            margin-top:35px;
        }
        
        .box-border {
            width: 90%;
            height: 2px;
            background: #868686;
            float: left;
            margin-left: 5%;
            margin-top: -12px;
        }
        
        /*tab*/
        .devise-header {
            padding: 0 20px;
        }
        .devise-menu.active {
            border: solid #307dbf;
            border-width: 0;
            color: #000000;
            border-bottom-width: 3px;
        }
        .devise-menu {
            width: 50%;
            float: left;
            border: solid #E6E8ED;
            border-width: 0;
            border-bottom-width: 1px;
            height: 60px;
            font-size: 12pt;
            line-height: 60px;
        }
        .devise-menu a{
            text-decoration:none;
        }
        #logo-login{
            width: 250px;
            margin-bottom: 20px; 
        }
        li.ligoogle{
            margin-bottom: 10px;
        }

");
?>