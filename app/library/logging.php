<?php 

namespace App\library ;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

    class logging {
        public $request = "";
    	private $startTime;
        protected $hiddenReq = [
            'password','password_confirm','columns'
        ];
        protected $hidden = [
            'password', 'remember_token','desc','status','success',
        ];
        public function __construct()
        {
            // parent::__construct();
            $this->startTime = microtime(true);
        }

        public function apiLog($nameLog = "generalLog", $paramSave = "",$custom = 'api') {
            if ($custom == "api") {
                foreach ($this->hiddenReq as $key => $value) {
                    if (isset($this->request[$value])) {
                        unset($this->request[$value]);
                    }
                }
                // foreach ($this->hidden as $key => $value) {
                //     if (isset($paramSave[$value])) {
                //         unset($paramSave[$value]);
                //     }
                // }

            	$fileContents = [
                                    "timeProcces" => microtime(true)-$this->startTime,
                                    "time" => time(),
                                    "request" => $this->request,
                                    // "requestPath" => $this->request->path(),
                                    $paramSave,
            					];
            } else {
                $fileContents = [
                                    "time" => time(),
                                    "datas" => $paramSave,
                                ];
            }
        	// Storage::append('log/'.date("Y")."/".date("m")."/".date("d")."/".$nameLog."_".date("Ymd"), date("Y-m-d H:i:s")."\t".json_encode($fileContents));
            Storage::append('log/'.date("Y")."/".date("m")."/".date("d")."/".$nameLog."v2_".date("Ymd"), date("Y-m-d H:i:s")."\t".$this->parsing_arr($fileContents));
            return true;
        }

        public function parsing_arr($arr, $keySalt = "") {
            $string = "";

            $keySalt = ($keySalt == "0_" ? "" : $keySalt);
            foreach ($arr as $key => $value) {
                if (is_array($value)) {
                    $string .= $this->parsing_arr($value , $keySalt.$key."_");
                } else {
                    if (!in_array($key, $this->hidden)) {
                        $string .= $keySalt.$key."=".urlencode($value)."\t"; 
                    }
                }
            }
            return $string;
        }
    }