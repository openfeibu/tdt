<?php

namespace App\Console\Commands;


use App\Models\Region;
use App\Models\RegionArea;
use App\Models\Shop;
use Illuminate\Console\Command;

class RegionStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'region_statistics:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '区域统计';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $regions = Region::get();
        foreach ($regions as $key => $region)
        {
            Region::updateShopCount($region->id);
        }
    }
}
