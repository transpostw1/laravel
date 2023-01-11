<html>
<head>
    <title>Quotation</title>
<style>

    td{
        width: 120px;
        height: 40px;
        font-size: 1em;
    }

    #liner{
        padding-left:30px;
        padding-right: 20px;
        border-bottom: 2px solid black;
    }
    #charges{
        padding-top: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid black;
    }

    #charges td{
        width:auto;
        font-size: 12px;
        padding-left:10px;
        padding-right:10px;
    }

    #total td{
        width: 360px;
    }
    </style>
</head>
<body>

    <table>
        <tr>
            <td>
                <?php if(isset($customer['sl_logo'])){ ?>
                <img style="width:150px;" src="{{public_path("/image/yourlogo.png")}}" alt=" Your Company Logo">
                <?php
                }
                else{
                ?>
                <img style="width:150px;" src="{{public_path("/image/yourlogo.png")}}" alt=" Your Company Logo">
                <?php }
                ?>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
                <?php echo date("d-M-Y");?></p>
            </td>


        </tr>
       </table>

    <div class="pt-2"><h4><b>Liner Option: 1</b></h4></div>

        <table id="liner">
            <tbody>
                <tr>
                    <td>
                        <b>Carrier Name</b>
                    </td>
                    <td>
                        <?php if(isset($customer['sl_name'])){
                            print($customer['sl_name']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td>
                        <b>Origin</b>
                    </td>
                    <td>
                        <?php if(isset($customer['from_port'])){
                            print($customer['from_port']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td>
                        <b>Destination</b>
                    </td>
                    <td>
                        <?php if(isset($customer['to_port'])){
                            print($customer['to_port']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                     </td>
                </tr>
                <tr>
                    <td>
                        <b>Cargo Type</b>
                    </td>
                    <td>
                        <?php if(isset($customer['cargo_size'])){
                            print($customer['cargo_size']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td>
                        <b>Service Type</b>
                    </td>
                    <td>
                        <?php if(isset($customer['service_mode'])){
                            print($customer['service_mode']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td>
                        <b>Transit Time</b>
                    </td>
                    <td>
                        <?php if(isset($customer['transit_time'])){
                            print($customer['transit_time']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Free Days</b>
                    </td>
                    <td>
                        <?php if(isset($customer['free_days'])){
                            print($customer['free_days']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td>
                        <b>Sailing Date</b>
                    </td>
                    <td>
                        <?php if(isset($customer['sailing_date'])){
                            print($customer['sailing_date']);
                        }
                        else{
                            print('N/A');
                        }
                        ?>
                    </td>
                    <td>
                        <b>Expiry Date</b>
                    </td>
                    <td>
                        <?php $d = $customer['expiry_date'];
                        $newDate = date("d-m-Y", strtotime($d));
                        echo $newDate;
                        ?>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>

        <table id="charges" cellspacing="0">
            <th>
                <tr style="background-color:#01a77e;color:black;border-radius: 50px;">
                    <td style="color:white;width:150px;font-size:14px;">
                        <b>Charges</b>
                    </td>
                    <td style="color:white;width:100px;text-align:center;font-size:14px;">
                        <b>Basis</b>
                    </td>
                    <td style="color:white;width:80px;text-align:center;font-size:14px;">
                        <b>Container Type</b>
                    </td>
                    <td style="color:white;width:80px;text-align:center;font-size:14px;">
                        <b>Currency</b>
                    </td>
                    <td style="color:white;width:120px;text-align:center;font-size:14px;">
                        <b>Unit Price</b>
                    </td>
                    <td style="color:white;width:60px;text-align:center;font-size:14px;">
                        <b>Quantity</b>
                    </td>
                    <td style="color:white;width:130px;text-align:right;font-size:14px;">
                        <b>Amount</b>
                    </td>
                </tr>
            </th>
            <tbody>
                <tr style="background-color:gainsboro;">
                    <td>
                    ::Main Freight::
                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:right;">
                        Total:{{$customer['base_rate']}} USD
                    </td>
                </tr>
                <?php
                if(!empty($customer['freightCharges'])){
                 ?>
                 <?php
                 foreach($customer['freightCharges'] as $freight){ ?>
                <tr>
                    <td>
                       <?php print($freight['chargeName']); ?>
                    </td>
                    <td style="text-align:center;">
                        <?php print($freight['chargeType']); ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if(isset($customer['cargo_size'])){
                            print($customer['cargo_size']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if(isset($freight['chargeCurrency'])){
                            print($freight['chargeCurrency']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td style="text-align:center;">
                        {{$freight['chargeAmount']}}
                    </td>
                    <td style="text-align:center;">
                        {{$freight['quantity']}}
                    </td>
                    <td style="text-align:right;">
                        {{$freight['chargeAmount']}} USD
                    </td>
                </tr>
                <?php }} ?>
                <?php $sum = 0;
                if(!empty($customer['originCharges'])){ ?>
                    <tr style="background-color:gainsboro;">
                    <td>
                    ::Origin Charges::
                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:right;">
                        <?php
                        foreach($customer['originCharges'] as $origin){
                                if(isset($origin['quantity'])){
                                    $sum += $origin['totalAmount'] * $origin['quantity'] ;
                                }
                                else{
                                    $sum += $origin['totalAmount'] * 1 ;
                                }
                            }
                            print("Total:".$sum." USD");


                        ?>
                    </td>
                </tr>

                 <?php }?>
                <?php
                if(!empty($customer['originCharges'])){
                 ?>
                 <?php
                 foreach($customer['originCharges'] as $origin){ ?>
                <tr>
                    <td>
                       <?php print($origin['chargeName']); ?>
                    </td>
                    <td style="text-align:center;">
                        <?php print($origin['chargeType']); ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if(isset($customer['cargo_size'])){
                            print($customer['cargo_size']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if(isset($origin['chargeCurrency'])){
                            print($origin['chargeCurrency']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td style="text-align:center;">
                        {{$origin['chargeAmount']}}
                    </td>
                    <td style="text-align:center;">
                        {{$origin['quantity']}}
                    </td>
                    <td style="text-align:right;">
                        {{$origin['chargeAmount']}} USD
                    </td>
                </tr>
                <?php }} ?>
                <?php $sum = 0;
                if(!empty($customer['destinationCharges'])){ ?>
                    <tr style="background-color:gainsboro;">
                    <td>
                    ::Destination Charges::
                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:right;">
                        <?php
                        foreach($customer['destinationCharges'] as $destination){
                                if(isset($destination['quantity'])){
                                    $sum += $destination['totalAmount'] * $destination['quantity'] ;
                                }
                                else{
                                    $sum += $destination['totalAmount'] * 1 ;
                                }
                            }
                            print("Total:".$sum." USD");


                        ?>
                    </td>
                </tr>

                 <?php }?>
                 <?php
                if(!empty($customer['destinationCharges'])){
                 ?>
                 <?php
                 foreach($customer['destinationCharges'] as $destination){ ?>
                <tr>
                    <td>
                       <?php print($destination['chargeName']); ?>
                    </td>
                    <td style="text-align:center;">
                        <?php print($destination['chargeType']); ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if(isset($customer['cargo_size'])){
                            print($customer['cargo_size']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if(isset($destination['chargeCurrency'])){
                            print($destination['chargeCurrency']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td style="text-align:center;">
                        {{$destination['chargeAmount']}}
                    </td>
                    <td style="text-align:center;">
                        {{$destination['quantity']}}
                    </td>
                    <td style="text-align:right;">
                        {{$destination['chargeAmount']}} USD
                    </td>
                </tr>
                <?php }} ?>

              <!--  <?php $sum = 0;
                if(!empty($customer['additionalCosts'])){

                 ?> -->
              <!--  <tr style="background-color:gainsboro;">
                    <td>
                    ::Origin Charges::
                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:center;">

                    </td>
                    <td style="text-align:right;">
                        <?php
                        foreach($customer['additionalCosts'] as $cust){
                                if (isset($cust['sellRate'])) {
                                    $sum += $cust['sellRate'];
                                }
                                else{
                                    $sum += $cust['totalAmount'];
                                }
                                if(isset($cust['quantity'])){
                                    $sum = $sum * $cust['quantity'];
                                    print("Total:".$sum." USD");
                                }
                                else{
                                    print("Total:".$sum." USD");
                                }
                            }


                        ?>
                    </td>
                </tr> -->
               <!-- <?php } ?> -->
               <!-- <?php if(isset($customer['additionalCosts'])){ ?>
                @foreach ($customer['additionalCosts'] as $cust)
                <tr style="border-radius: 100px;">
                    <td>
                        <?php if (isset($cust['chargeName'])) {
                            print($cust['chargeName']);
                        }
                        else{
                            print($cust['name']);
                        } ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if (isset($cust['basis'])) {
                            print($cust['basis']);
                        }
                        else{
                            print('-');
                        } ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if(isset($customer['cargo_size'])){
                            print($customer['cargo_size']);
                        }
                        else{
                            print('-');
                        }
                        ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if (isset($cust['currency'])) {
                            print($cust['currency']);
                        }
                        else{
                            print('USD');
                        } ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if (isset($cust['sellRate'])) {
                            print($cust['sellRate']);
                        }
                        else{
                            print($cust['totalAmount']);
                        } ?>
                    </td>
                    <td style="text-align:center;">
                        <?php if (isset($cust['quantity'])) {
                            print($cust['quantity']);
                        }
                        else{
                            print(1);
                        } ?>
                    </td>
                    <td style="text-align:right;">
                        <?php if (isset($cust['sellRate']) and isset($cust['quantity']) ) {
                            $var = $cust['sellRate']*$cust['quantity'];
                            print($var." USD");
                        }
                        else{
                            $var = $cust['totalAmount']*1;
                            print($var." USD");
                        } ?>
                    </td>
                </tr>
                @endforeach
                <?php } ?> -->
            </tbody>
        </table>
        <table id="total">
            <tbody>
                <tr>
                    <td>
                        <p style="font-size:10px;">Note: Taxes and Foreign conversion charges are additional</p>
                    </td>
                    <td style="text-align:right;">
                        <b>Total Cost - {{$customer['sum_sell']}} USD</b>
                        <p style="font-size:10px;"> *May include Additional Locals & Taxes</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <div>
        <p style="font-size:12px;">
            <?php if(!empty($customer['remarks'])) {
                ?>
                @foreach ($customer['remarks']['blocks'] as $remarks)
                <h4><b>Remarks:</b></h4>
                <p style="font-size:12px;">{{$remarks['text']}}</p>
                @endforeach
                <?php } ?>

        </p>
        </div>
        <div style="text-align: center;">
            <h3><b>Thank you for your business</b></h3>
        </div>
</body>
</html>
