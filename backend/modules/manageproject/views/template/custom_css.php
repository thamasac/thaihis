<?php \appxq\sdii\widgets\CSSRegister::begin(); ?>
<style>
    /*form create*/
    #form-create{
        margin-bottom:50px;
    }
    #preview_icon{width:100px;height:100px;}
    /*file uploads*/
    #div-upload-file{position:relative;} 
    .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
        font-size: 16pt;
    }
    #upload-input {
        font-size: 16pt;
        cursor: pointer;
        position: absolute;
        left: -75px;
        top: -4px;
        opacity: 0;
        margin-top: 5px;
    }
    dt {
        line-height: 25px;
    }
    .croppie-container{
        height:auto;
    }
    #upload-action{margin-bottom:10px;}
    .upload-msg{
        margin: 10px auto;
        overflow: hidden;
        text-align: center;
        width: 115px;
        height: 115px;
        background: #f1efef57;
        padding: 8px;
        border-radius: 5px;
        box-shadow: 1px 1px 1px 1px #d8d8d8;
    }
    
    /*input url*/
    .input-url{
        display:flex;margin-bottom: 5px;
    }
    .input-url-div1{flex-grow: 1;    flex: 0 0 22%;}
    .input-url-div2{flex-grow: 1; align-self: center; font-size: 11pt; margin-top: -10px;    padding-left: 10px;}
    .breadcrumb{
       z-index:99999;
    }
    .project-hide{
       
    }
    
</style>
<?php \appxq\sdii\widgets\CSSRegister::end(); ?>