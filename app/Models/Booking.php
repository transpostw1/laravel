<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'ID';

    // 'CS_User' => $request->id,
    // 'DateOfBooking' => $now,
    // 'ContainerType' => $request->cargoSize,
    // 'TypeOfOnboarding' => 'Online',
    // 'ShippingLineName' => $request->sl_name,
    // 'POL' => $this->port_id_from_code($request->from_port),
    // 'POD' => $this->port_id_from_code($request->to_port),
    // 'BuyRate' => $request->description,
    // 'SellRate' => $request->total,
    // 'ContainerCount' => $request->commodityDetails['containerCount'],
    // 'commodity' => $request->commodityDetails['commodityName'],
    // 'weight' => $request->commodityDetails['weight'],
    // 'CustomerName'=> $this->customerIdFromEmail($request->email) 

    // protected $fillable = [
    //         'CS_User',
    //         'DateOfBooking',
    //         'ContainerType',
    //         'TypeOfOnboarding',
    //         'ShippingLineName',
    //         'POL',
    //         'POD',
    //         'BuyRate',
    //         'SellRate',
    //         'ContainerCount',
    //         'commodity',
    //         'CustomerName',
    //         'weight',
    // ];
    protected $guarded = ['ID'];


    use HasFactory;

    public function pol()
    {
        return $this->hasOne('App\Models\Port_name', 'ID', 'POL');
    }

    public function pod()
    {
        return $this->hasOne('App\Models\Port_name', 'ID', 'POD');
    }

    public function cs_status(){
        return $this->hasOne('App\Models\Cs_statu', 'ID', 'status');
    }
}
