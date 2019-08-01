/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function addinput(){
    var fid = new Date();
    fid= fid.getTime();
    console.log("vvv");
    var inputData= ""
             inputData +="<div class='input-div'><input type='hidden' name='data["+fid + "][fid]' value='"+fid +"'></input>";

         inputData +="<input class='input-form form-control' type='text' name='data["+fid + "][name]'></input>";

         inputData +="<input class='input-form form-control' type='text' name='data["+fid + "][lastname]'></input>";
         inputData +="<input class='input-form form-control' type='text' name='data["+fid + "][tel]'></input>";
     inputData +="<button class='binput input-form btn btn-danger'>ลบ</button></div>";
    inputData +="</div>";
   $('#inputPanel').append( inputData);
   //  console.log("ccc");
 
}
$(document).ready(function () {

     $("#inputPanel").on('click','.binput',function(){
       console.log("test");
  $( this ).parent().remove();
    
});
});


function addAjaxInput(){
    $.ajax({
  url:  $("#inputPanel").attr('data-url'),
  cache: false
})
  .done(function( html ) {
   $('#inputPanel').append( html);
  });
    
}