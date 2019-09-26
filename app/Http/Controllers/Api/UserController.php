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
            'message' => '提交成功',
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
            'message' => '提交成功',
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
            'old_password.required' => '旧密码不能为空',
            'password.required' => '新密码不能为空',
            'password.confirmed' => '重复新密码不正确',
            'password.min' => '密码最少六位',
        ]);
        if ($validator->fails()) {
            throw new OutputServerMessageException($validator->errors()->first());
        }
        $user->password = User::where('id',$user->id)->value('password');
        if (!Hash::check($request->get('old_password'), $user->password)) {
            throw new OutputServerMessageException('旧密码错误');
        }

        $password = $request->get('password');

        $user->password = bcrypt($password);

        $update = User::where('id',$user->id)->update(['password' => bcrypt($password)]);

        if ($update) {
            throw new RequestSuccessException("修改成功");
        } else {
            throw new OutputServerMessageException('服务器出错了');
        }
    }
}
