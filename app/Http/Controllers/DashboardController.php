<?php

namespace App\Http\Controllers;

use App\Models\job_post;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        $count_user = User::where('type', 2)->where('status', 1)->count();
        $count_company = User::where('type', 3)->where('status', 1)->count();
        $count_post_job_active = job_post::where('status', 1)->count();
        $count_post_job_all = job_post::count();

        $data = [
            'count_user' => $count_user,
            'count_company' => $count_company,
            'count_post_job_active' => $count_post_job_active,
            'count_post_job_all' => $count_post_job_all
        ];

        return $this->responseData(true, $data, 'Ok');
    }
}
