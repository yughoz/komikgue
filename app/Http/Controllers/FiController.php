<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Auth;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;

class FiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $data = [];
    public function __construct(Request $request)
    {
        $this->log = new \App\library\logging;
        $this->log->request = $request->all();
    }

    public function index(Request $request)
    {
       if (!empty($request->input('url'))) {
        $this->log->apiLog('finger');  
       }
       return view('finger');
    }
    public function add(Request $request)
    {
        $this->log->apiLog('finger');  
    }

    public function getToken(Request $request)
    {
        echo csrf_token();
    }
}
