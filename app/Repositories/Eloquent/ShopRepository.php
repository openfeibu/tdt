<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Eloquent\ShopRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class ShopRepository extends BaseRepository implements ShopRepositoryInterface
{
    public function model()
    {
        return config('model.shop.shop.model');
    }

}