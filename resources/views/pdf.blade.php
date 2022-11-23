<html>
<head>
<style>

    td{
        width: 102px;
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
        width: 340px;
    }

    </style>
</head>
<body>

    <table>
        <tr>
            <td>
                <img style="width:100px;" src="{{public_path("/image/tf.png")}}" alt="Company Logo">
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
                        <b>Linear Name</b>
                    </td>
                    <td>
                        {{$customer['sl_name']}}
                    </td>
                    <td>
                        <b>Origin</b>
                    </td>
                    <td>
                        {{$customer['from_port']}}
                    </td>
                    <td>
                        <b>Destination</b>
                    </td>
                    <td>
                        {{$customer['to_port']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Cargo Type</b>
                    </td>
                    <td>
                        {{$customer['cargo_size']}}
                    </td>
                    <td>
                        <b>Service Type</b>
                    </td>
                    <td>
                        {{$customer['service_mode']}}
                    </td>
                    <td>
                        <b>Transit Time</b>
                    </td>
                    <td>
                        {{$customer['transit_time']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Free Days</b>
                    </td>
                    <td>

                    </td>
                    <td>
                        <b>Sailing Date</b>
                    </td>
                    <td>
                        N/A
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
                <tr style="background-color:black;color:black;border-radius: 4em;">
                    <td style="color:white;width:120px;">
                        <b>Charges</b>
                    </td>
                    <td style="color:white;width:90px;text-align:center;">
                        <b>Basis</b>
                    </td>
                    <td style="color:white;width:50px;text-align:center;">
                        <b>Container Type</b>
                    </td>
                    <td style="color:white;width:50px;text-align:center;">
                        <b>Currency</b>
                    </td>
                    <td style="color:white;width:80px;text-align:center;">
                        <b>Unit Price</b>
                    </td>
                    <td style="color:white;width:50px;text-align:center;">
                        <b>Quantity</b>
                    </td>
                    <td style="color:white;width:120px;text-align:right;">
                        <b>Amount</b>
                    </td>
                </tr>
            </th>
            <tbody>
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
                            $sum = 0;
                            foreach($customer['additionalCosts'] as $cust){
                                $sum += $cust['amount'];
                            }
                            print("Total:".$sum." USD");
                        ?>
                    </td>
                </tr>
                @foreach ($customer['additionalCosts'] as $cust)
                <tr style="border-radius: 100px;">
                    <td>
                    {{$cust['chargeName']}}
                    </td>
                    <td style="text-align:center;">
                        {{$cust['basis']}}
                    </td>
                    <td style="text-align:center;">
                        {{$customer['cargo_size']}}
                    </td>
                    <td style="text-align:center;">
                        {{$cust['currency']}}
                    </td>
                    <td style="text-align:center;">
                        {{$cust['sellRate']}}
                    </td>
                    <td style="text-align:center;">
                        {{$cust['quantity']}}
                    </td>
                    <td style="text-align:right;">
                        {{$cust['amount']}} USD
                    </td>
                </tr>
                @endforeach
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
                <tr>
                    <td>
                        Basic Ocean Freight
                    </td>
                    <td style="text-align:center;">
                        per equipment
                    </td>
                    <td style="text-align:center;">
                        {{$customer['cargo_size']}}
                    </td>
                    <td style="text-align:center;">
                        {{$cust['currency']}}
                    </td>
                    <td style="text-align:center;">
                        {{$customer['base_rate']}}
                    </td>
                    <td style="text-align:center;">
                        1
                    </td>
                    <td style="text-align:right;">
                        {{$customer['base_rate']}} USD
                    </td>
                </tr>
            </tbody>
        </table>
        <table id="total">
            <tbody>
                <tr>
                    <td>
                        <p style="font-size:10px;">Note: Taxes and Foreign conversion charges are additional</p>
                    </td>
                    <td style="text-align:right;">
                        <b>Total Cost - <?php $total = $customer['base_rate'] + $sum; print($total); ?> USD</b>
                        <p style="font-size:10px;"> *May include Additional Locals & Taxes</p>
                    </td>
                </tr>
            </tbody>
        </table>


</body>
</html>
