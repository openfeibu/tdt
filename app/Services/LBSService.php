<?php
namespace App\Services;

use GuzzleHttp\Client;
use App\Exceptions\OutputServerMessageException;

class LBSService
{
    public $debug;

    public function __construct()
    {
        $this->key = config('lbs.web_key');

        $this->client = new Client();

        $this->domain = "https://apis.map.qq.com/";

        $this->debug = false;
    }
    public function geocode_regeo($lng,$lat)
    {
        $url = $this->domain."ws/geocoder/v1/?key=".$this->key."&location=".$lat.','.$lng.'&get_poi=0';
        $res = $this->client->get($url);
        $data = json_decode($res->getBody()->getContents(),true);

        if($data['status'])
        {
            if($this->debug)
            {
                $data['error'] = 1;
                return $data;
            }
            throw new \App\Exceptions\OutputServerMessageException($data['message']);
        }

        return $data;
    }
    public function geocode_geo($address)
    {
        $url = $this->domain."ws/geocoder/v1/?key=".$this->key."&address=".preg_replace_blank($address,' ');
        $res = $this->client->get($url);
        $data = json_decode($res->getBody()->getContents(),true);
        if($data['status'])
        {
            if($this->debug)
            {
                $data['error'] = 1;
                return $data;
            }
            throw new \App\Exceptions\OutputServerMessageException($data['message']);
        }

        return $data;
    }

    /**
     * @param $type
     * @param $from_latitude
     * @param $from_longitude
     * @param $to_latitude
     * @param $to_longitude
     * @return array
     * 1. 驾车（driving）：支持结合实时路况、少收费、不走高速等多种偏好，精准预估到达时间（ETA）；
     * 2. 步行（walking）：基于步行路线规划。
     * 3. 骑行（bicycling）：基于自行车的骑行路线；
     * 4. 公交（transit）：支持公共汽车、地铁等多种公共交通工具的换乘方案计算；
     */
    public function direction($type,$from_latitude,$from_longitude,$to_latitude,$to_longitude)
    {
        $url = $this->domain."ws/direction/v1/".$type."?key=".$this->key."&from=".$from_latitude.','.$from_longitude."&to=".$to_latitude.','.$to_longitude;

        $res = $this->client->get($url);
        $data = json_decode($res->getBody()->getContents(),true);

        if($data['status'])
        {
            if($this->debug)
            {
                $data['error'] = 1;
                return $data;
            }
            throw new \App\Exceptions\OutputServerMessageException($data['message']);
        }

        return $data;
    }

}