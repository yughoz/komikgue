<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Auth;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;

class TokenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $data = [];
    public function __construct()
    {
        // $this->yuuLTElib = new \App\library\yuuLTElib;
        // $this->middleware('auth');
    }

    public function getToken(Request $request)
    {
        $result = [
            'status'=>'success',
            'statusCode'=>200,
            'desc'=>'success ',
            'data'=> csrf_token(),
        ];
        // echo base64_encode($crawler->text());
        // die();
        return response()->json($result);
        // echo csrf_token();
    }
}
