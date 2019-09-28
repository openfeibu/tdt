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

        $this->debug = false;
    }
    public function geocode_regeo($lng,$lat)
    {
        $url = "https://apis.map.qq.com/ws/geocoder/v1/?key=".$this->key."&location=".$lat.','.$lng.'&get_poi=0';
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
        $url = "https://apis.map.qq.com/ws/geocoder/v1/?key=".$this->key."&address=".$address;
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