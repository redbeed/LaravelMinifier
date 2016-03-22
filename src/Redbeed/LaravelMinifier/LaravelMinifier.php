<?php namespace Redbeed\LaravelMinifier;

use App;
use Config;
use File;
use URL;
use Illuminate\Foundation\Application;

class LaravelMinifier {

	public static function hash(){

		$hash = [];
		$salt = md5(config('app.key'));

		$ivSize = 16;
		$iv = substr(md5($salt.'abc123'), 0, $ivSize);

		$hash[] = config('app.key'); //!! add first
		$hash[] = substr(md5(env('COMMIT')), 0, 5).substr(md5(env('COMMIT')), 7, 2);

		$ciphertext = openssl_encrypt(
            implode('__', $hash),
            'aes-128-cfb',
            $salt,
            OPENSSL_RAW_DATA,
            $iv
        );

		return str_replace('/', '_1', base64_encode($ciphertext));
	}

	public static function validHash($hash){

		$salt = md5(config('app.key'));

		$ivSize = openssl_cipher_iv_length(config( 'app.cipher' ));
		$iv = substr(md5($salt.'abc123'), 0, $ivSize);

		$hash = str_replace('_1', '/', $hash);
		$hash = base64_decode($hash, true);
		if ($hash == false){
			return false;
		}

		$ivSize = 16;
        $ciphertext = $hash;

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-128-cfb',
            $salt,
            OPENSSL_RAW_DATA,
            $iv
        );

        $hash = explode('__', $plaintext);
		return (head($hash) === config('app.key')) ? true : false;
	}

	public static function css($groupname, $minfiy = true, $noTag = false){
		if (App::environment('local', 'staging') || $minfiy == false){

			$returnString = '';

			$group = config('assets.css.'.$groupname, false);
			foreach($group as $file){
				$returnString .= (string) ($noTag) ? $file."\n" : Html::style($file)."\n";
			}

			return $returnString;

		}else{
			$minifyRoute = route('assets.minify.css', ['hash' => self::hash(), 'group' => $groupname ]);
			return (string) ($noTag) ? $minifyRoute : Html::style($minifyRoute);
		}
	}

	public static function javascript($groupname, $minfiy = true, $noTag = false){
		if (App::environment('local', 'staging') || $minfiy == false){

			$returnString = '';

			$group = config('assets.javascript.'.$groupname, false);
			foreach($group as $file){
				$returnString .= (string) ($noTag) ? $file."\n" : Html::script($file)."\n";
			}

			return $returnString;

		}else{
			$minifyRoute = route('assets.minify.javascript', ['hash' => self::hash(), 'group' => $groupname ]);
			return (string) ($noTag) ? $minifyRoute : Html::script($minifyRoute);
		}
	}

}
