<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['turnno', 'fname', 'lname', 'email', 'nic', 'address1', 'address2', 'city', 'tp1', 'tp2', 'license_number', 'license_date', 'joining_date', 'vehicle_number', 'status'];

    public static $status = [1 => 'Active', 2 => 'Inactive', 3 => 'Blocked', 4 => 'Deleted'];

    public static function laratablesAdditionalColumns()
    {
        return ['fname', 'lname','nic','email'];
    }

    public static function laratablesCustomName($user)
    {
        return $user->fname . ' ' . $user->lname.'<br><small>'.$user->nic.'<br>'.$user->email.'</small>';
    }

    public static function laratablesStatus($driver)
    {
        return '<span class="badge badge-' . (new Colors)->getColor($driver['status']) . '">' . self::$status[$driver['status']] . '</span>';
    }

    public static function laratablesCustomAction($driver)
    {
        $action='';
        
        if(Session::has('routes') &&  in_array('//drivers/block',Session::get('routes')) ){
            $action.='<i onclick="doBlock(' . $driver['id'] . ')" title="Block / Unblock" class="la la-recycle ml-1 text-primary"></i>';
        }
        if(Session::has('routes') &&  in_array('//drivers/get',Session::get('routes')) ){
            $action.='<i onclick="doEdit(' . $driver['id'] . ')" class="la la-edit ml-1 text-warning"></i>';
        }
        if(Session::has('routes') &&  in_array('//drivers/delete',Session::get('routes')) ){
            $action.='<i onclick="doDelete(' . $driver['id'] . ')" class="la la-trash ml-1 text-danger"></i>';
        }
        
        return $action;
    }

    public static function laratablesQueryConditions($query)
    {
        return $query->whereIn('status', [1, 2,3]);
    }
}
