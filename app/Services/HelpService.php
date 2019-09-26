<?php

namespace App\Services;

use Validator;
use DB;
use Log;
use App\Setting;
use App\TradeAccount;
use App\ShippingConfig;
use Illuminate\Http\Request;

class HelpService
{

	public $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 检验请求参数
	 */
	public function validateParameter($rules)
	{
		$validator = Validator::make($this->request->all(), $rules);
        if ($validator->fails()) {
    		throw new \App\Exceptions\Custom\OutputServerMessageException($validator->errors()->first());
        } else {
        	return true;
        }
	}

	public function wp_is_mobile() {
	    static $is_mobile;

	    if ( isset($is_mobile) )
	        return $is_mobile;

	    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
	        $is_mobile = false;
	    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
	        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
	            $is_mobile = true;
	    } else {
	        $is_mobile = false;
	    }

	    return $is_mobile;
	}
	public function is_wap(){
	    if(isset($_SERVER['HTTP_VIA'])) return TRUE;
	    if(isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) return TRUE;
	    if(isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) return TRUE;
	    if(strpos(strtoupper($_SERVER['HTTP_ACCEPT']), 'VND.WAP.WML') > 0) return TRUE;
	    $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
	    if($http_user_agent == '') return TRUE;
	    $mobile_os = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
	    $mobile_token = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');
	    $flag_os = $flag_token = FALSE;
	    foreach($mobile_os as $val){
	        if(strpos($http_user_agent, $val) > 0){ $flag_os = TRUE; break; }
	    }
	    foreach($mobile_token as $val){
	        if(strpos($http_user_agent, $val) > 0){ $flag_token = TRUE; break; }
	    }
	    if($flag_os || $flag_token) return TRUE;
	    return FALSE;
	}

	/**
	* desription 压缩图片
	* @param sting $imgsrc 图片路径
	* @param string $imgdst 压缩后保存路径
	*/
	public function image_png_size_add($imgsrc,$imgdst){
	  	list($width,$height,$type)=getimagesize($imgsrc);
		$ratio = $width>600 ? 600/$width : 1 ;
	  	$new_width = $ratio * $width * 0.9;
	  	$new_height =$ratio * $height * 0.9;

	  	switch($type){
	    	case 1:
	      		$giftype=$this->check_gifcartoon($imgsrc);
	      		if($giftype){
	        		$image_wp=imagecreatetruecolor($new_width, $new_height);
	        		$image = imagecreatefromgif($imgsrc);
	        		imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	        		imagegif($image_wp, $imgdst,75);
	        		imagedestroy($image_wp);
	      		}
	      		break;
	    	case 2:
				$image_wp=imagecreatetruecolor($new_width, $new_height);
				$image = imagecreatefromjpeg($imgsrc);
				imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_wp, $imgdst,75);
				imagedestroy($image_wp);
				break;
			case 3:
				$image_wp=imagecreatetruecolor($new_width, $new_height);
				$image = imagecreatefrompng($imgsrc);
				imagesavealpha($image,true);
				imagealphablending($image_wp,false);
           		imagesavealpha($image_wp,true);
				imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagepng($image_wp, $imgdst);
				imagedestroy($image_wp);
				break;
	  	}

	}
	/**
	* desription 判断是否gif动画
	* @param sting $image_file图片路径
	* @return boolean t 是 f 否
	*/
	public function check_gifcartoon($image_file){
	  $fp = fopen($image_file,'rb');
	  $image_head = fread($fp,1024);
	  fclose($fp);
	  return true;
	}
	public function isVaildImage($files)
	{
		$error = '';

		foreach($files as $key => $file)
		{
			$name = $file->getClientOriginalName();
			if(!$file->isValid())
			{
				$error.= $name.$file->getErrorMessage().';';
			}
			if(!in_array( strtolower($file->extension()),config('common.img_type'))){
				$error.= $name."类型错误;";
			}
			if($file->getClientSize() > config('common.img_size')){
				$img_size = config('common.img_size')/(1024*1024);
				$error.= $name.'超过'.$img_size.'M';
			}
		}
		if($error)
		{
			throw new \App\Exceptions\Custom\OutputServerMessageException($error);
		}
	}
}
