<?php

namespace App\Http\Controllers;

use App\Models\district;
use App\Models\field_work;
use App\Models\province;
use App\Models\regency;
use App\Models\sub_field_work;
use App\Models\type_industry;
use App\Models\village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function data_type_industry()
    {
        $data = type_industry::orderBy('id', 'desc')->get();
        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'Data tidak tersedia');
    }

    public function save_type_industry(Request $request)
    {
        $id = $request->id;
        
        $validation = Validator::make($request->all(), [
            'name_industry' => 'required|unique:type_industry,name_industry',
        ], [
            'name_industry.required' => 'Nama tipe industri harus diisi',
            'name_industry.unique' => 'Nama tipe industri sudah pernah diinputkan'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }
        
        $type = '';
        if(empty($id)) {
            $type = 'ditambahkan';
            $save = type_industry::create([
                'name_industry' => $request->name_industry
            ]);
        } else {
            $type = 'diperbarui';
            $save = type_industry::where('id', $id)->update([
                'name_industry' => $request->name_industry
            ]);
        }

        if($save) {
            return $this->responseJson(true, 'Data tipe industri berhasil '. $type);
        }

        return $this->responseJson(false, 'Data tipe industri gagal '. $type);
    }

    public function delete_type_industry($id)
    {
        $delete = type_industry::where('id', $id)->delete();
        if($delete) {
            return $this->responseJson(true, 'Data tipe industri berhasil dihapus');
        }

        return $this->responseJson(false, 'Data tipe industri gagal dihapus');
    }

    public function data_field_work()
    {
        $data = field_work::orderBy('id', 'desc')->get();
        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'data tidak tersedia');
    }

    public function save_field_work(Request $request)
    {
        $id = $request->id;
        
        $validation = Validator::make($request->all(), [
            'field_name' => 'required|unique:field_works,field_name',
        ], [
            'field_name.required' => 'Bidang kerja harus diisi',
            'field_name.unique' => 'Bidang kerja sudah pernah diinputkan'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }
        
        $type = '';
        if(empty($id)) {
            $type = 'ditambahkan';
            $save = field_work::create([
                'field_name' => $request->field_name
            ]);
        } else {
            $type = 'diperbarui';
            $save = field_work::where('id', $id)->update([
                'field_name' => $request->field_name
            ]);
        }

        if($save) {
            return $this->responseJson(true, 'Data bidang kerja berhasil '. $type);
        }

        return $this->responseJson(false, 'Data bidang kerja gagal '. $type);
    }

    public function delete_field_work($id)
    {
        $delete = field_work::where('id', $id)->delete();
        if($delete) {
            $query_sub = sub_field_work::where('field_work_id', $id)->first();
            if($query_sub) {
                $query_sub->delete();
            }
            return $this->responseJson(true, 'Data bidang kerja berhasil dihapus');
        }

        return $this->responseJson(false, 'Data bidang kerja gagal dihapus');
    }

    public function data_sub_field_work()
    {
        $data = DB::table('sub_field_works as sfw')
        ->select('sfw.*', 'fw.field_name')
        ->leftJoin('field_works as fw', 'fw.id', '=', 'sfw.field_work_id')
        ->orderBy('sfw.id', 'desc')
        ->get();

        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'Data belum tersedia');
    }

    public function save_sub_field_work(Request $request)
    {
        $id = $request->id;
        
        $validation = Validator::make($request->all(), [
            'field_work_id' => 'required',
            'name_sub_field' => 'required|unique:sub_field_works,name_sub_field',
        ], [
            'field_work_id.required' => 'Bidang kerja wajib dipilih',
            'name_sub_field.required' => 'Sub bidang kerja harus diisi',
            'name_sub_field.unique' => 'Sub bidang kerja sudah pernah diinputkan'
        ]);

        if ($validation->fails()) {
            return $this->responseValidation('validation_error', $validation->errors());
        }
        
        $type = '';
        if(empty($id)) {
            $type = 'ditambahkan';
            $save = sub_field_work::create([
                'field_work_id' => $request->field_work_id,
                'name_sub_field' => $request->name_sub_field
            ]);
        } else {
            $type = 'diperbarui';
            $save = sub_field_work::where('id', $id)->update([
                'field_work_id' => $request->field_work_id,
                'name_sub_field' => $request->name_sub_field
            ]);
        }

        if($save) {
            return $this->responseJson(true, 'Data sub bidang kerja berhasil '. $type);
        }

        return $this->responseJson(false, 'Data sub bidang kerja gagal '. $type);
    }

    public function delete_sub_field_work($id) 
    {
        $delete = sub_field_work::where('id', $id)->delete();
        if($delete) {
            return $this->responseJson(true, 'Data sub bidang kerja berhasil dihapus');
        }

        return $this->responseJson(false, 'Data sub bidang kerja gagal dihapus');
    }

    public function select_province()
    {
        $data = province::get();
        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'Data tidak tersedia');
    }

    public function select_regency($id)
    {
        $data = regency::where('province_id', $id)->get();
        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'Data tidak tersedia');
    }

    public function select_district($id)
    {
        $data = district::where('regency_id', $id)->get();
        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'Data tidak tersedia');
    }

    public function select_village($id)
    {
        $data = village::where('district_id', $id)->get();
        if($data) {
            return $this->responseData(true, $data, 'ok');
        }

        return $this->responseData(false, [], 'Data tidak tersedia');
    }
}
