<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isApi
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('a');
        $user_id = $request->header('b');
        $code = $request->header('c');
        $exp_token = $request->header('d');
        $type = $request->header('e');

        if(empty($token) || empty($user_id) || empty($code) || empty($exp_token) || empty($type)) {
            return response([
                'status' => false,
                'message' => "Anda tidak memiliki akses, silahkan login terlebih dahulu"
            ]);
        }

        $util = new AuthController();
        $cek_token = $util->build_token($user_id, $code);
        if($token != $cek_token) {
            return response([
                'status' => false,
                'message' => 'Token tidak valid! silahkan login kembali'
            ]);
        }

        $cek_exp = $util->base64_to_datetime($exp_token);
        if(strtotime('now') >= strtotime($cek_exp)) {
            return response([
                'status' => false,
                'message' => 'Token telah kadaluarsa! silahkan login kembali'
            ]);
        }
        
        $user = User::where('id', $user_id)->first();
        if(empty($user)) {
            return response([
                'status' => false,
                'message' => 'Akun anda tidak ditemukan, silahkan login kembali'
            ]);
        }

        if($type != '2') {
            return response([
                'status' => false,
                'message' => 'Tipe akun anda perusahaan, anda tidak memiliki akses!'
            ]);
        }

        $user['id'] = $user->id;
        $user['code'] = $code;
        $user['token'] = $token;
        $user['exp_token'] = $cek_exp;
        $user['type'] = $type;
        $request->merge(['user' => $user]);

        return $next($request);
    }
}
