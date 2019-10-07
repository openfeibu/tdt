<?php

namespace App\Repositories\Presenter;

use League\Fractal\TransformerAbstract;
use Hashids;

class RegionUserTransformer extends TransformerAbstract
{
    public function transform(\App\Models\RegionUser $user)
    {
        return [
            //'id'                => $user->getRouteKey(),
            'id' => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'api_token'         => $user->api_token,
            'remember_token'    => $user->remember_token,
            'phone'             => $user->phone,
            'photo'             => $user->photo,
            'permissions'       => $user->permissions,
            'status'            => $user->status,
            'region_name'     => $user->region->name,
            'roles'             => $user->roles,
            'role_names' => implode('，',$user->roles->pluck('name')->all()),
            'created_at'        => format_date($user->created_at),
            'updated_at'        => format_date($user->updated_at),
        ];
    }
}