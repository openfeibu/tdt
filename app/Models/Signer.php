<?php

namespace App\Models;

use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\BaseModel;
use App\Traits\Database\Slugger;
use App\Traits\Filer\Filer;
use App\Traits\Hashids\Hashids;
use App\Traits\Trans\Translatable;

class Signer extends BaseModel
{
    use Filer, Hashids, Slugger, Translatable, LogsActivity;

    /**
     * Configuartion for the model.
     *
     * @var array
     */
    protected $config = 'model.signer.signer';

    public static function addSigner($name)
    {
        $signer = self::where('name',trim($name))->first();
        if(!$signer)
        {
            $signer =self::create([
                'name' => $name
            ]);
        }
        return $signer;
    }

}