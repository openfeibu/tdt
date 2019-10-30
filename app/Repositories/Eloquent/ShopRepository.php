<?php

namespace App\Repositories\Eloquent;

use App\Models\Shop;
use App\Repositories\Eloquent\ShopRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class ShopRepository extends BaseRepository implements ShopRepositoryInterface
{
    public function model()
    {
        return config('model.shop.shop.model');
    }
    public function getRangeShopCount($longitude,$latitude,$km)
    {
        $count = Shop::whereRaw("(acos(sin((".$latitude."*3.1415)/180) * sin((latitude*3.1415)/180) + cos((".$latitude."*3.1415)/180) * cos((latitude*3.1415)/180) * cos((".$longitude."*3.1415)/180 - (longitude*3.1415)/180))*6370.996) <= ".$km)
            ->count();
        return $count;
    }
    public function getRangeShop($longitude,$latitude,$km)
    {
        $shops = Shop::selectRaw("*,(acos(sin((".$latitude."*3.1415)/180) * sin((latitude*3.1415)/180) + cos((".$latitude."*3.1415)/180) * cos((latitude*3.1415)/180) * cos((".$longitude."*3.1415)/180 - (longitude*3.1415)/180))*6370.996) as distance")
            ->whereRaw("(acos(sin((".$latitude."*3.1415)/180) * sin((latitude*3.1415)/180) + cos((".$latitude."*3.1415)/180) * cos((latitude*3.1415)/180) * cos((".$longitude."*3.1415)/180 - (longitude*3.1415)/180))*6370.996) <= ".$km)
            ->get()
            ->toArray();
        return $shops;
    }
}