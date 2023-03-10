<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\OutputServerMessageException;
use App\Exceptions\RequestSuccessException;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Services\WXBizDataCryptService;
use App\Services\AmapService;
use Illuminate\Support\Facades\Validator;
use Route,Auth,Hash,Input,Log,Image,File;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

    }
    public function getUser(Request $request)
    {
        $user = User::getUser();
        return response()->json([
            'code' => '200',
            'data' => $user,
        ]);
    }
    public function submitPhone(Request $request)
    {
        $user = User::getUser();
        $encryptedData = $request->input('encryptedData');
        $iv = $request->input('iv');

        $WXBizDataCryptService = new WXBizDataCryptService($user['session_key']);

        $data = [];
        $errCode = $WXBizDataCryptService->decryptData($encryptedData, $iv, $data );

        if ($errCode != 0) {
            return response()->json([
                'code' => '400',
                'message' => $errCode,
            ]);
        }

        $phone_data = json_decode($data);

        $phone = $phone_data->phoneNumber;

        User::where('id',$user->id)->update([
            'phone' => $phone
        ]);
        return response()->json([
            'code' => '200',
            'message' => 'ζδΊ€ζε',
            'data' => $phone
        ]);
    }
    public function submitLocation(Request $request)
    {
        $user = User::getUser();
        $longitude = $request->input('longitude','');
        $latitude =  $request->input('latitude','');
        $amap_service = new AmapService();

        $data = $amap_service->geocode_regeo($longitude.','.$latitude);

        User::where('id',$user->id)->update([
            'longitude' => $longitude,
            'latitude' => $latitude,
            'city' => $data['regeocode']['addressComponent']['city'],
        ]);

        return response()->json([
            'code' => '200',
            'message' => 'ζδΊ€ζε',
            'data' => $data['regeocode']['addressComponent']['city'],
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = User::getUser();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password'     => 'required|confirmed|min:6',
        ],[
            'old_password.required' => 'ζ§ε―η δΈθ½δΈΊη©Ί',
            'password.required' => 'ζ°ε―η δΈθ½δΈΊη©Ί',
            'password.confirmed' => 'ιε€ζ°ε―η δΈζ­£η‘?',
            'password.min' => 'ε―η ζε°ε­δ½',
        ]);
        if ($validator->fails()) {
            throw new OutputServerMessageException($validator->errors()->first());
        }
        $user->password = User::where('id',$user->id)->value('password');
        if (!Hash::check($request->get('old_password'), $user->password)) {
            throw new OutputServerMessageException('ζ§ε―η ιθ――');
        }

        $password = $request->get('password');

        $user->password = bcrypt($password);

        $update = User::where('id',$user->id)->update(['password' => bcrypt($password)]);

        if ($update) {
            throw new RequestSuccessException("δΏ?ζΉζε");
        } else {
            throw new OutputServerMessageException('ζε‘ε¨εΊιδΊ');
        }
    }
}
