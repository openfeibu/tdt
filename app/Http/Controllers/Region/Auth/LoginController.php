<?php

namespace App\Http\Controllers\Region\Auth;

use App\Http\Controllers\Region\Controller;
use App\Traits\AdminUser\RoutesAndGuards;
use App\Traits\AdminUser\Auth\AuthenticatesUsers;
use App\Traits\Theme\ThemeAndViews;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Response\Auth\Response as AuthResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use RoutesAndGuards, ThemeAndViews, ValidatesRequests, AuthenticatesUsers;

    protected $redirectTo = '/region';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->response   = resolve(AuthResponse::class);
        $this->setRedirectTo();
        $this->middleware('guest:' . $this->getGuard(), ['except' => ['logout', 'verify', 'locked', 'sendVerification']]);
        $this->setTheme();
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect($this->redirectTo);
    }
    public function username()
    {
        return 'phone';
    }
}
