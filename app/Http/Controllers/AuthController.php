<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmResetPassword;
use App\Mail\SendVerification;
use App\Models\company_profile;
use App\Models\User;
use App\Models\verification_account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

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
            'password.min' => 'Kata sandi minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok, silahkan isi kembali!',
            'password_confirmation.required' => 'Konfirmasi kata sandi harus diisi'
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
            $link_verif = $this->link_frontend() . '/verifikasi-akun?email=' . $request->email . '&token=' . $token . '&type=pengguna';
            $send_data = [
                'link' => $link_verif,
                'type' => 'employee',
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

        $user = User::where('email', $request->email)->where('type', $request->type)->first();
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

        $message = '';
        if($request->type == 2) {
            $message = 'Anda berhasil masuk akan dialihkan ke halaman utama';
        } else if($request->type == 3) {
            $message = 'Anda berhasil masuk akan dialihkan ke halaman member';
        } else {
            $message = 'Anda berhasil masuk akan dialihkan ke halaman admin';
        }
        return $this->responseData(true, $data, $message);
    }

    public function user(Request $request)
    {
        // $data = User::where('id', $request->user['id'])->first();
        $type = $request->user['type'];
        if($type == 2) {
            $data = DB::table('users')
            ->select('users.id as users_id', 'users.email', 'users.username', 'users.email_verified_at', 'users.status', 'users.type', 'pu.*')
            ->leftJoin('profile_users as pu', 'users.id', '=', 'pu.user_id')
            ->where('users.id', $request->user['id'])
            ->first();
        } else {
            $data = DB::table('users')
            ->select('users.id as users_id', 'users.email', 'users.username', 'users.email_verified_at', 'users.status', 'users.type', 'cp.*')
            ->leftJoin('company_profiles as cp', 'users.id', '=', 'cp.user_id')
            ->where('users.id', $request->user['id'])
            ->first();
        }
        if($data) {
            return $this->responseData(true, $data, 'Ok');
        }

        return $this->responseData(false, null, 'Data tidak ditemukan');
    }

    public function forgot_password(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid, isikan email dengan valid (contoh: admin@gmail.com)'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }

        $cek_user = User::where('email', $request->email)->first();
        if(empty($cek_user)) {
            return $this->responseJson(false, 'Email anda tidak terdaftar, silahkan daftar akun terlebih dahulu');
        }
        
        if($cek_user->status == 0) {
            return $this->responseJson(false, 'Akun anda belum terverifikasi, silahkan verifikasi akun dengan cek email');
        }

        $token = Str::random(60);
        $val_token = [
            'email' => $request->email,
            'token' => $token,
            'time' => time()
        ];

        $save = verification_account::create($val_token);
        if($save) {
            $link = $this->link_frontend() . '/lupa-password/ubah-password?email=' . $request->email . '&token=' . $token;
            $send_data =[
                'link' => $link,
                'username' => $cek_user->username
            ];
            $send = Mail::to($request->email)->send(new ConfirmResetPassword($send_data));
            if($send) {
                return $this->responseJson(true, 'Konfirmasi lupa password berhasil dikirim, silahkan cek email anda untuk mengubah password');
            }
            return $this->responseJson(false, 'Konfirmasi email gagal dikirim, silahkan coba kembali');
        }

        return $this->responseJson(false, 'Konfirmasi ubah password gagal, silahkan dicoba kembali');
    }

    public function reset_password(Request $request)
    {
        $email = $request->email;
        if(empty($email)) {
            return $this->responseJson(false, 'Email tidak ditemukan');
        }

        $validation = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ], [
            'password.required' => 'Kata sandi baru harus diisi',
            'password.min' => 'Kata sandi minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'password_confirmation.required' => 'Konfirmasi kata sandi harus diisi'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }

        $cek_user = User::where('email', $email)->where('status', 1)->first();
        if($cek_user) {
            $cek_token = verification_account::where('email', $email)->where('token', $request->token)->first();
            if($cek_token && time() - $cek_token->time < (60 * 60 * 24)) {
                User::where('email', $email)->update([
                    'password' => Hash::make($request->password),
                    'read_password' => $request->password
                ]);
                $cek_token->delete();
                return $this->responseJson(true, 'Kata sandi berhasil diubah silahkan masuk untuk menjelajahi loker');
            }

            return $this->responseJson(false, 'Token anda tidak valid / token telah kadaluarsa');
        } else {
            return $this->responseJson(false, 'Akun anda tidak ditemukan, silahkan daftar akun terlebih dahulu');
        }

        return $this->responseJson(false, 'Ubah password gagal, silahkan coba lagi!');
    }

    public function register_company(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'username' => 'required|min:3',
            'telp_number' => ['required', 'regex:/^8[1-9][0-9]{7,10}$/'],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ], [
            'name.required' => 'Nama perusahaan harus diisi',
            'name.min' => 'Nama perusahaan minimal minimal 3 karakter',
            'username.required' => 'Nama user (penanggung jawab) harus diisi',
            'username.min' => 'Nama user (penanggung jawab) minimal minimal 3 karakter',
            'telp_number.required' => 'No Telepon / hp / whatsapp harus diisi',
            'telp_number.regex' => 'No Telepon / hp / whatsapp harus berupa angka, minimal 7 karakter, maksimal 11 karakter, dan tidak diawali angka 0',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid, isikan email dengan valid! (contoh: user@mail.com)',
            'email.unique' => 'Email telah terdaftar, silahkan gunakan email lain!',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Kata sandi minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok, silahkan isi kembali!',
            'password_confirmation.required' => 'Konfirmasi kata sandi harus diisi'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }

        $value_user = [
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'read_password' => $request->password,
            'status' => 0,
            'type' => 3
        ];

        $token = Str::random(60);
        $value_token = [
            'email' => $request->email,
            'token' => $token,
            'time' => time()
        ];

        $save = User::create($value_user);
        if($save) {
            $user = User::where('email', $request->email)->first();
            $value_company = [
                'user_id' => $user->id,
                'name' => $request->name,
                'telp_number' => $request->telp_number,
            ];
            company_profile::create($value_company);
            verification_account::create($value_token);
            $link_verif = $this->link_frontend() . '/verifikasi-akun?email=' . $request->email . '&token=' . $token . '&type=perusahaan';
            $send_data = [
                'link' => $link_verif,
                'type' => 'company',
                'username' => $request->username
            ];
            Mail::to($request->email)->send(new SendVerification($send_data));
            return $this->responseJson(true, 'Anda berhasil mendaftarkan akun, silahkan cek email untuk verifikasi akun!');
        }

        return $this->responseJson(false, 'Anda gagal mendaftarkan akun, silahkan coba lagi!');
    }

    public function check_auth(Request $request)
    {
        $id = $request->user['id'];
        if(!empty($id)) {
            $user = User::where('id', $id)->first();
            $cek_token = $this->build_token($user->id, $request->user['code']);
            if($cek_token != $request->user['token']) {
                return $this->responseJson(false, 'Token tidak sesuai, silahkan masuk kembali!');
            }

            if(strtotime('now') >= strtotime($request->user['exp_token'])) {
                return $this->responseJson(false, 'Token telah kadaluarsa, silahkan masuk kembali!');
            }

            return $this->responseJson(true, 'Ok');
        }

        return $this->responseJson(false, 'User tidak ditemukan, silahkan masuk kembali');
    }

    public function login_google()
    {
        $google = Socialite::driver('google')->user();

        dd($google); 

    }
}
