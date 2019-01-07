<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Auth;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SamehadaController extends Controller
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

    public function listDownload(Request $request)
    {
// download-eps
        try {
            
            $url = $request->input('url');
            $listCache =  Cache::get($url);  
            if (!empty($listCache)) {
                $listChapterDetail = json_decode($listCache);
            } else {
                $listChapterDetail = [];
                $client = new Client();
                $chapters = substr($url, strrpos($url, '/') + 1);
                $crawler = $client->request('GET', $url);
                // echo print_r($crawler);die();
                // $crawler->filter('div.download-eps')->each(function ($node) use (&$listChapterDetail) {

                //     $url = trim($node->attr('data-src')); 
                //     if (!empty($url)) {
                //         // echo $url;
                //         $listChapterDetail[]  = trim($node->attr('data-src')); ;
                //     }
                // });
                // echo $crawler->filter('div.download-eps > ul')->html();
                // echo $crawler->filter('div.download-eps > ul > li > strong')->html();
                $crawler->filter('div.download-eps > ul >li >strong')->each(function ($node) use (&$listChapterDetail) {
                    // $htmls = $node->text();
                    // echo $htmls;
                    // echo "<br>\n";
                    $listChapterDetail['str'][]  = $node->html();
                });
                $crawler->filter('div.download-eps > ul >li> span > a')->each(function ($node) use (&$listChapterDetail) {
                    // $htmls = $node->html();
                    // echo $htmls;
                    // echo "<br>\n";
                    $listChapterDetail['listLink'][$node->attr('href')][]  = $node->text();
                    // $listChapterDetail['list'][]  = $node->html();
                });
                
                
            }

            // Cache::store('file')->put($url, json_encode($listChapterDetail), 10);
            // Cache::store('database')->put($url, json_encode($listChapterDetail), 10);
            // $fullPath = "chache/".urlencode($url);
            // Storage::put($fullPath, json_encode($listChapterDetail));   


            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $listChapterDetail,
                        ];
            return response()->json($result);
        } catch (\Exception $e) {
            $result = [
                            'status'=>'error',
                            'statusCode'=> 501,
                            'desc'=>'Failed update data',
                            'error'=> $e->getMessage()
                        ];    
        return response()->json($result);
        }


    }
    public function index(Request $request)
    {
       return view('samehadaku');
    }
    public function download(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        // echo "mangaDetail";
        try {
            
            $url = $request->input('url');
            $listCache =  Cache::get($url);  
            $chapters = substr($url, strrpos($url, '/') + 1);
            
            if (!empty($listCache)) {
                $datas = json_decode($listCache);
            } else {
                $this->log->apiLog('api_samehada_crack');  
                $datas = [];
                $parsUrl = $url;
                $this->getParsingHtml($parsUrl);
                $datas['link'] = $parsUrl;
            	
                
	            Cache::store('file')->put($url, json_encode($datas), now()->addSeconds(10));
            }


            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $datas,
                        ];
            // echo base64_encode($crawler->text());
            // die();
            $this->log->apiLog('api_samehada_crack');  
            return response()->json($result);
        } catch (\Exception $e) {
            $result = [
                            'status'=>'error',
                            'statusCode'=> 501,
                            'desc'=>'Failed update data',
                            'error'=> $e->getMessage()
                        ];    
        return response()->json($result);
        }


    }

    public function parseBase64($labels)
    {
        $label = base64_decode($labels);
        if (filter_var($label, FILTER_VALIDATE_URL)) {
            return $label;
        } else {
            return $labels;
        }
    }
    public function getParsingHtml(&$url)
    {
        try {
            $client = new Client();
            $crawler = $client->request('GET', $url);
            
            $i = 0;
            $tempLink = $crawler->filter('div.download-link > a')->attr('href');
             $Query_String  = explode("r=", explode("&", explode("?", $tempLink)[1] )[0] );
             // if (empty($Query_String[1])) {
             //     return $url;
             // } else {
                $url = base64_decode($Query_String[1]);
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    // return $label;
                 $this->getParsingHtml($url);
                } else {
                    return $url;
                }
                
             // }
             // $url = $this->parseBase64($Query_String[1]);
            // echo $url,"-AAAAAAAAAA--";
            // var_dump($Query_String);die();
        
        } catch (\Exception $e) {
            return $url;
        }
    }
    public function parseHTML(&$label)
    {
        $label = trim(str_replace("\n", '', $label));
        $label =  preg_replace( "~\x{00a0}~siu", "", $label );
        $label = preg_replace('!\s+!', ' ', $label);
        return $label;
    }
}
