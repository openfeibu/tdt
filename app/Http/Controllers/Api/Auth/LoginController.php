<?php

namespace App\Http\Controllers\Api\Auth;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use App\Models\User;
use App\Http\Controllers\Api\BaseController;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        // 规则
        $rules = [
            'phone' => 'required|string',
            'password' => 'required|string|min:6',
        ];
        $messages = [
            'phone.require' => '账号不能为空',
            'password.require' => '密码不能为空',
            'password.min' => '密码不能少于六个字符串',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            throw new \App\Exceptions\OutputServerMessageException($validator->errors()->first());
        }

        $token = Auth::guard('user.api')->attempt($request->all());

        if(!$token)
        {
            throw new \App\Exceptions\OutputServerMessageException("账号或密码错误");
        }
        User::where('phone',$request->phone)->update(['token' => $token]);

        $user = User::getUserByPhone($request->phone);

        return $this->response->success()->message("登录成功")->data(
            $user->toArray()
        )->json();

    }
}
