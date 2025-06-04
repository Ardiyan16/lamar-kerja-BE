<?php

namespace App\Http\Controllers;

use App\Models\company_profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('company_profiles as cp')
        ->select('cp.*', 'u.email', 'u.email_verified_at', 'ti.name_industry', 'p.name as name_province', 'r.name as name_regency', 'd.name as name_district')
        ->leftJoin('users as u', 'u.id', '=', 'cp.user_id')
        ->leftJoin('type_industry as ti', 'ti.id', '=', 'cp.type_industry')
        ->leftJoin('provinces as p', 'p.id', '=', 'cp.province')
        ->leftJoin('regencies as r', 'r.id', '=', 'cp.regency')
        ->leftJoin('districts as d', 'd.id', '=', 'cp.district')
        ->where('u.id', $request->user['id'])
        ->first();

        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'Data tidak tersedia');
    }

    public function update(Request $request)
    {
        $step = $request->step;
        if($step == 1) {
            $validation = Validator::make($request->all(), [
                'name' => 'required|min:3',
                'address' => 'required|min:10',
                'province' => 'required',
                'regency' => 'required',
                'district' => 'required',
                'telp_number' => 'required|numeric|max_digits:13|min_digits:10',
                'type_industry' => 'required',
                'total_employee' => 'required',
            ], [
                'name.required' => 'Nama perusahaan harus diisi',
                'name.min' => 'Nama perusahaan minimal 3 karakter',
                'address.required' => 'Alamat perusahaan harus diisi',
                'address.min' => 'Alamat perusahaan minimal 10 karakter (contoh: Jl. Panglima Besar Jendral Sudirman No 1 Jakarta)',
                'province.required' => 'Provinsi harus dipilih',
                'regency.required' => 'Kabupaten/Kota harus dipilih',
                'district.required' => 'Kecamatan harus dipilih',
                'telp_number.required' => 'No Telepon / Whatsapp harus diisi',
                'telp_number.numeric' => 'No Telepon harus berupa angka',
                'telp_number.max_digits' => 'No Telepon maksimal 13 digit',
                'telp_number.min_digits' => 'No Telepon minimal 10 digit',
                'type_industry.required' => 'Tipe industri harus diisi',
                'total_employee' => 'Jumlah karyawan harus diisi'
            ]);
        } else {
            $validation = Validator::make($request->all(), [
                'motto' => ['nullable', 'regex:/^(\S+(\s+\S+){2,})$/'],
                'link_web' => 'url',
                'link_maps' => 'url',
            ],[
                'motto.regex' => 'Motto harus setidaknya 3 kata (contoh: Kami membentuk keluarga untuk kesuksesan bersama)',
                'link_web' => 'Link tidak valid (contoh: https://lamarkerja.com)',
                'link_maps' => 'Link tidak valid (contoh: https://maps.app.goo.gl/tq8qbZcptuKyZi247)'
            ]);
        }

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }

        $process = $this->process_update($request, $step);
        return $this->responseJson($process['status'], $process['message']);
    }

    private function process_update($request, $step)
    {
        if($step == 1) {
            $value = [
                'name' => $request->name,
                'address' => $request->address,
                'province' => $request->province,
                'regency' => $request->regency,
                'district' => $request->district,
                'telp_number' => $request->telp_number,
                'type_industry' => $request->type_industry,
                'total_employee' => $request->total_employee,
                'about_us' => $request->about_us,
            ];
        } else {
            $value = [
                'motto' => $request->motto,
                'corporate_culture' => $request->corporate_culture,
                'link' => $request->link,
                'social_media' => $request->social_media,
                'status_profile' => 1
            ];
        }

        $save = company_profile::where('user_id', $request->user['id'])->update($value);
        if($save) {
            $data = [
                'status' => true,
                'message' => 'Profile berhasil diperbarui'
            ];
            return $data;
        }

        $data = [
            'status' => false,
            'message' => 'Profile gagal diperbarui'
        ];
        return $data;
    }

    public function upload_profile(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'logo_profile' => 'required|image|mimes:jpeg,png,jpg,webp|max:3048',
        ], [
            'logo_profile.required' => 'Foto profile harus diunggah',
            'logo_profile.image' => 'Foto profile harus berupa gambar/foto',
            'logo_profile.mimes' => 'Foto profile harus berformat jpeg, jpg, png atau webp',
            'logo_profile.max' => 'Foto profile maksimal berukuran 3 Mb',
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }

        $logo = $request->file('logo_profile');
        $foto = $logo;
        $name_image = time() . Str::random(5) . "_" . $foto->getClientOriginalName();
        $folder = 'image/logo_company';
        $foto->move($folder, $name_image);

        $old_logo = company_profile::where('user_id', $request->user['id'])->first();
        if($old_logo) {
            $image_path = public_path('image/logo_company/' . $old_logo->logo_profile);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $upload = company_profile::where('user_id', $request->user['id'])->update([
            'logo_profile' => $name_image
        ]);
        
        if($upload) {
            return $this->responseJson(true, 'Foto profile berhasil diperbarui');
        }

        return $this->responseJson(false, 'Foto profile gagal diperbaharui');
    }

    public function upload_gallery(Request $request)
    {
        $images = $request->file('gallery');
        if (!is_array($images)) $images = [$images];
        if(empty($images)) {
            return $this->responseJson(false, 'Upload gagal!');
        }

        $user_id = $request->user['id'];
        $profile = User::where('user_id', $user_id)->first();

        $paramGallery = [];
        $paramGallery = !empty($profile) ? json_decode($profile->gallery, true) : [];
        $error = [];
        $sizePrev = sizeof($paramGallery);
        foreach($images as $key => $img) {
            $file = $images[$key];
            $file_name =  $key . "-profileID-" . $profile->id . "-" . date('Ymdhis') . '-' . ($key + $sizePrev) . '.webp';
            $folder = 'gallery';
            $process = $file->move($folder, $file_name);
            if($process) {
                $paramGallery[] = [
                    'index' => $key,
                    'image' => $file_name
                ];
            } else {
                $error[] = $img->getClientOriginalName();
            }
        }

        if(!empty($error)) {
            return $this->responseData(false, $error, 'Upload Gagal');
        }

        company_profile::where('user_id', $user_id)->update([
            'gallery' => json_encode(array_values($paramGallery))
        ]);

        return $this->responseData(true, $paramGallery, 'Upload Berhasil');
    }

    public function delete_gallery(Request $request)
    {
        $index = $request->index;
        $name_image = $request->name_image;
        $user_id = $request->user['id'];

        $profile = company_profile::where('user_id', $user_id)->first();
        if(!$profile) {
            return $this->responseJson(false, 'User tidak ditemukan');
        }

        $paramGallery = !empty($profile->gallery) ? json_decode($profile->gallery, true) : [];
        $arrImgIdx = array_column($paramGallery, 'image_id');
        unset($paramGallery[array_search($index, $arrImgIdx)]);

        if (empty($paramGallery)) $paramGallery = null;
        else $paramGallery = json_encode(array_values($paramGallery));

        $image_path = public_path('image/gallery/' . $name_image);
        $delete = false;
        if (file_exists($image_path)) {
            unlink($image_path);
            $delete = true;
        }

        if($delete == false) {
            return $this->responseJson(false, 'Gagal menghapus gambar');
        }

        $hapus = company_profile::where('user_id', $user_id)->update([
            'gallery' => $paramGallery
        ]);

        if($hapus) {
            return $this->responseData(true, $paramGallery, 'Berhasil hapus gambar');
        }

        return $this->responseJson(false, 'Gagal menghapus gambar');
    }
}
