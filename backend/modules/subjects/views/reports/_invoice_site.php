<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<html>
    <head>
        <style>
            .printarea{
                padding: 50px 35px;
            }
                .header{
                    font-weight : bold;
                    text-align: center;
                    font-size:18px;
                }
                .info-table{
                    margin-top: 50px;
                    font-size:18px;
                }
                .invoice-address{
                    font-size:18px;
                    margin-bottom: 30px; 
                }
                
                .border-table{
                    font-size:18px;
                    width:100%;
                    border-collapse: collapse;
              
                } 
                .border-table tr td, .border-table tr th{
                     border: 1px solid #000;
                     padding:5px;
                }
                .noborder{
                    border: 0 ;
                }
              
                .text-bold{
                    font-weight:bold;
                }
                .remit-text{
                    font-size:18px;
                }
            </style>
    </head>
    <body>
        <div class="printarea">
            <p class="header">Division xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx<br />
            department XXXXXXXXXXXXXXXXxxx</p>

            <table class="info-table" width="100%">
                <tr>
                    <td width="50%"></td>
                    <td width="50%"><b>Invoice #KKU </b> <br />
                    Date xxx <br />
                    trail # Geccica <br />
                    site #
                    </td>
                </tr>
            </table>

            <p class="invoice-address">
                <b>To.</b><br>
                ชื่อ-ที่อยู่

            </p>
            <table class="border-table">
                <tr>
                    <th width="15%">Serial #</th>
                    <th width="55%">Paticular</th>
                    <th width="15%">Currency</th>
                    <th width="15%">Amount</th>
                </tr>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td >2 advance payment</td>
                    <td style="text-align:center;">THB</td>
                    <td style="text-align:right;">64,000</td>
                </tr>
                <tr>
                    <td style="text-align:center;">2</td>
                    <td>20% institutional </td>
                    <td style="text-align:center;">THB</td>
                    <td style="text-align:right;">16,000</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right;">Grand Total</td>
                    <td style="text-align:center;">THB</td>
                    <td style="text-align:right;">80,000</td>
                </tr>
            </table>
            <p class="remit-text">Please remit the payment to: <i>ระบุรายละเอียดบัญชีโอนเงินเข้าตามที่ระบุใน CTA</i></p>

            <table class="border-table">
                <tr>
                    <td width="35%">Payee Name : </td>
                    <td width="65%"></td>
                </tr>
                <tr>
                    <td>Bank Accout No. </td>
                    <td></td>
                </tr>
                <tr>
                    <td>Bank Name </td>
                    <td></td>
                </tr>
                <tr>
                    <td>SWIFT Code: </td>
                    <td></td>
                </tr>
            </table>

            <p class="remit-text">Please provide us detail  on remittance. <br /><br />
                Thank you,<br /><br /><br /><br />

                ________________________________________<br />
                <i>(NAME)</i> <br />
                <b>Pricipal Investigator </b>

            </p>
        </div>
        <script>
    setTimeout(function(){
        window.print();
    }, 1000);
    
    </script>
    </body>    
</html>   
