<?php

namespace App\Http\Controllers;

use App\Mail\SendVerification;
use App\Models\User;
use App\Models\verification_account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set("Asia/Jakarta");
    }

    const SALT = '6UejnN';

    public function build_token($id, $code)
    {
        return md5(self::SALT.$id.$code);
    }

    public function datetime_to_base64($date)
    {
        return base64_encode($date);
    }

    public function base64_to_datetime($base64) 
    {
        return base64_decode($base64);
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ], [
            'username.required' => 'Username harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid, isikan email dengan valid! (contoh: user@mail.com)',
            'email.unique' => 'Email telah terdaftar, silahkan gunakan email lain!',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok, silahkan isi kembali!',
            'password_confirmation.required' => 'Konfirmasi password harus diisi'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }

        $value = [
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'read_password' => $request->password,
            'status' => 0,
            'type' => 2
        ];
        
        $token = Str::random(60);
        $val_token = [
            'email' => $request->email,
            'token' => $token,
            'time' => time()
        ];
        
        $save = User::create($value);
        if($save) {
            verification_account::create($val_token);
            $link_verif = $this->link_frontend() . '/verifikasi-akun?email=' . $request->email . '&token=' . $token;
            $send_data = [
                'link' => $link_verif,
                'username' => $request->username
            ];
            Mail::to($request->email)->send(new SendVerification($send_data));
            return $this->responseJson(true, 'Anda berhasil mendaftar, silahkan cek email untuk link verifikasi akun!');
        }

        return $this->responseJson(false, 'Gagal mendaftar, silahkan coba kembali');
    }

    public function verification_account(Request $request)
    {
        $email = $request->post('email');
        $token = $request->post('token');

        $cek_akun = User::where('email', $email)->first();
        if($cek_akun) {
            $cek_token = verification_account::where('email', $email)->where('token', $token)->first();
            if($cek_token && time() - $cek_token->time < (60 * 60 * 24)) {
                User::where('email', $email)->update([
                    'status' => 1,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ]);

                $cek_token->delete();
                return $this->responseJson(true, 'Akun anda berhasil diverifikasi silahkan lanjut ke menu login/masuk');
            } else {
                $cek_akun->delete();
                $cek_token->delete();
                return $this->responseJson(false, 'Link verifikasi telah kadaluarsa silahkan anda daftar kembali');
            }
        } else {
            return $this->responseJson(false, 'Akun tidak ditemukan silahkan mendaftar terlebih dahulu ');
        }
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email yang dimasukkan tidak valid',
            'password.required' => 'Password harus diisi'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }

        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return $this->responseJson(false, 'Email atau password yang dimasukkan salah / akun tidak terdaftar');
        }

        if($user->status == 0) {
            return $this->responseJson(false, 'Akun anda belum di verifikasi silahkan verifikasi akun anda dengan cek email anda');
        }

        $code = rand(1234, 9879);
        $token = $this->build_token($user->id, $code);
        $exp_token = $this->datetime_to_base64(date('Y-m-d H:i:s', strtotime("+7 days")));
        $data = [
            'token' => $token,
            'code' => $code,
            'exp_token' => $exp_token,
            'user_id' => $user->id,
            'type' => $user->type,
        ];

        return $this->responseData(true, $data, 'Anda berhasil masuk akan dialihkan ke halaman utama');
    }

    public function user(Request $request)
    {
        // $data = User::where('id', $request->user['id'])->first();
        $data = DB::table('users')
        ->select('users.id as users_id', 'users.email', 'users.username', 'users.email_verified_at', 'users.status', 'users.type', 'pu.*')
        ->leftJoin('profile_users as pu', 'users.id', '=', 'pu.user_id')
        ->where('users.id', $request->user['id'])
        ->first();
        if($data) {
            return $this->responseData(true, $data, 'Ok');
        }

        return $this->responseData(false, null, 'Data tidak ditemukan');
    }
}
