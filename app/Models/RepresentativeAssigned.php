<?php

namespace App\Models;

class RepresentativeAssigned
{
    public static function laratablesAdditionalColumns()
    {
        return ['first_name', 'last_name'];
    }

    public static function laratablesFirstName($data)
    {
        return $data->first_name . ' ' . $data->last_name;
    }

    public static function laratablesSearchableColumns()
    {
        return ['first_name', 'first_name'];
    }

    public static function laratablesQueryConditions($query)
    {
        return $query->where('status', 1)->where('isassigned',1);
    }

    public static function laratablesCustomAction($data)
    {
        return '<i onclick="doDelete(' . $data['id'] . ')" class="la la-trash ml-1 text-danger"></i>';
    }
}
