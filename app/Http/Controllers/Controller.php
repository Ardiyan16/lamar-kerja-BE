<?php

namespace App\Http\Controllers;

abstract class Controller
{

    public function link_local()
    {
        return 'http://localhost:8000/api';
    }

    public function link_frontend()
    {
        return 'http://localhost:3000';
    }

    // public function link_production()
    // {
    //     return
    // }

    public function responseJson($status, $message)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function responseData($status, $data, $message)
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ]);
    }

    public function responseValidation($type, $error)
    {
        return response()->json([
            'status' => false,
            'type' => $type,
            'message' => $error
        ]);
    }
}
