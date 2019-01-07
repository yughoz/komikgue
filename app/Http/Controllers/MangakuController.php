<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Auth;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class MangakuController extends Controller
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

        $this->category = [
            'Action'
        ];
    }

    public function home(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        // echo "mangaDetail";
        try {
            // sleep(2)
            $url = 'https://www.komikgue.com/';
            $listCache =  Cache::get($url);  
            $client = new Client();
            $chapters = substr($url, strrpos($url, '/') + 1);
            
            if (!empty($listCache) && $request->input('debug') != "yes") {
                $datas = json_decode($listCache);
            } else {
                $crawler = $client->request('GET', $url);
                $this->log->apiLog('api_populer');  
                $datas = [];
                $i = 0;
                
                $startI = $i;
                $crawler->filter('div.manga-name > a')->each(function ($node) use (&$datas,&$startI) {
                    $datas['unggulan'][$startI]['href']  = $node->attr('href');
                    $datas['unggulan'][$startI]['title']  = $node->text();
                    $startI++;
                });
                
                $startI = $i;
                $crawler->filter('a.thumbnail')->each(function ($node) use (&$datas,&$startI) {
                    $datas['unggulan'][$startI]['chapterHref']  = $node->attr('href');
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('a.thumbnail > img')->each(function ($node) use (&$datas,&$startI) {
                    $datas['unggulan'][$startI]['imgSrc']  = $node->attr('src');
                    $datas['unggulan'][$startI]['img']  = $node->attr('src');
                    $startI++;
                });
                
                
                $startI = $i;
                $crawler->filter('div.well > p')->each(function ($node) use (&$datas,&$startI) {
                    $datas['unggulan'][$startI]['chapter']  = $node->text();
                    $startI++;
                });


                $startI = $i;
                $crawler->filter('div.media-left > a')->each(function ($node) use (&$datas,&$startI) {
                    $datas['popular'][$startI]['href']  = $node->attr('href');
                    $datas['popular'][$startI]['img']  = str_replace("manga", "uploads/manga", $node->attr('href'))."/cover/cover_250x350.jpg";
                    $url = $node->attr('href');
                    $listCacheDetail =  Cache::get("headerOnly_".$url);  

                    if (!empty($listCacheDetail)) {
                        $arrListDetail = json_decode($listCacheDetail,true);
                        // unset($arrListDetail['listChapter']);
                        $datas['popular'][$startI]['header_detail'] = $arrListDetail;
                    }
                    $startI++;
                });
                $startI = $i;
                $crawler->filter('div.media-left > a > img')->each(function ($node) use (&$datas,&$startI) {
                    $datas['popular'][$startI]['imgSrc']  = $node->attr('src');
                    $startI++;
                });
                $startI = $i;
                $crawler->filter('a.chart-title')->each(function ($node) use (&$datas,&$startI) {
                    $datas['popular'][$startI]['title']  = $node->text();
                    $startI++;
                });
                $startI = $i;
                $crawler->filter('div.media-body > a')->each(function ($node) use (&$datas,&$startI) {
                    $datas['popular'][$startI]['chapterHref']  = $node->attr('href');
                    $datas['popular'][$startI]['chapter']  = $node->text();
                    $startI++;
                });

                $startI = $i;
                // $crawler->filter('div.manga-name > a')->each(function ($node) use (&$datas,&$startI) {
                //     $datas['komikUpdate'][$startI]['href']  = $node->attr('href');;
                //     $datas['komikUpdate'][$startI]['title']  = $node->text();
                //     $startI++;
                // });
                // $startI = $i;
                // $crawler->filter('div.photo > a.thumbnail > img ')->each(function ($node) use (&$datas,&$startI) {
                //     $datas['komikUpdate'][$startI]['imgSrc']  = $node->attr('src');;
                //     $startI++;
                // });
                
                
                $startI = $i;
                $crawler->filter('small.pull-right')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['komikUpdate'][$startI]['date']  = $this->parseHTML($text);
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('h3.manga-heading > a')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    if( $node->attr('href') != "https://www.komikgue.com/news/mode-baca-komik"){
                        $datas['komikUpdate'][$startI]['title_href']  = $node->attr('href');
                        $datas['komikUpdate'][$startI]['title']  = $this->parseHTML($text);
                        $datas['komikUpdate'][$startI]['img']  = str_replace("manga", "uploads/manga", $node->attr('href'))."/cover/cover_250x350.jpg";
                        
                        $url = $node->attr('href');
                        $listCacheDetail =  Cache::get("headerOnly_".$url);  

                        if (!empty($listCacheDetail)) {
                            $arrListDetail = json_decode($listCacheDetail,true);
                            // unset($arrListDetail['listChapter']);
                            $datas['komikUpdate'][$startI]['header'] = $arrListDetail;
                        }
                        $startI++;
                    }
                    
                });

                $startI = $i;
                $crawler->filter('h3.manga-heading')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $html = $node->html();
                    $htmlParse = $this->parseHTML($html);
                    if (strpos($htmlParse,"https://www.komikgue.com/news/mode-baca-komik") == 0) {
                        if (strpos($htmlParse,"hot") > 0) {
                            $datas['komikUpdate'][$startI]['hot']  = "hot";
                        } else {
                            $datas['komikUpdate'][$startI]['hot']  = "";
                        }
                        $startI++;
                    }
                });

                Cache::store('file')->put($url, json_encode($datas), now()->addSeconds(100));
            }


            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $datas,
                        ];
            // echo base64_encode($crawler->text());
            // die();
            $this->log->apiLog('api_manga_detail');  
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

    public function mangaList(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        // echo "mangaDetail";
        try {
            $kategori = $this->kategori();
            // echo $request->input('debug');die;
            $url = "https://www.komikgue.com/filterList";
            $cacheName = $url.$request->input('page').$request->input('params');
            // echo $cacheName;die;
            $listCache =  Cache::get($cacheName);  
            $client = new Client();
            $chapters = substr($url, strrpos($url, '/') + 1);
            
            if (!empty($listCache) && $request->input('debug') != "yes") {
                $datas = json_decode($listCache);
            } else {
                // echo print_r($client->request('GET', $url));die();
            	$crawler = $client->request('GET', $url."?".$request->input('params'));
            	$this->log->apiLog('api_laters-release-end');  
                $datas = [];
                $i = 0;

                // echo print_r($crawler);die;
                $startI = $i;
                $crawler->filter('h5.media-heading > a')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['title']  = $this->parseHTML($text);
                    $datas['listManga'][$startI]['title_href']  = $node->attr('href');
                    $datas['listManga'][$startI]['img']  = str_replace("manga", "uploads/manga", $node->attr('href'))."/cover/cover_250x350.jpg";
                    
                    $url = $node->attr('href');
                    $listCacheDetail =  Cache::get("headerOnly_".$url);  
                    if (!empty($listCacheDetail)) {
                        $arrListDetail = json_decode($listCacheDetail,true);
                        // unset($arrListDetail['listChapter']);
                        $datas['listManga'][$startI]['header'] = $arrListDetail;
                    }
                    
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('span.label')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['status']  = $this->parseHTML($text);
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('i.fa-eye')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['viewer']  = $this->parseHTML($text);
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('dd')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    if(!    in_array( $this->parseHTML($text) , ['Complete', 'Ongoing'])){
                        $datas['listManga'][$startI]['genre']  = $this->parseHTML($text);
                        $startI++;
                    }
                });
	            Cache::store('file')->put($cacheName, json_encode($datas), now()->addSeconds(60));
            }



            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $datas,
                        ];
            // echo base64_encode($crawler->text());
            // die();
            $this->log->apiLog('api_laters-release-end');  
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

    public function mangaListSearch(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        // echo "mangaDetail";
        try {
            $kategori = $this->kategori();
            // echo $request->input('debug');die;
            $url = "https://www.komikgue.com/advSearchFilter";
            $cacheName = $url.$request->input('page').$request->input('params');
            // echo $cacheName;die;
            $listCache =  Cache::get($cacheName);  
            $client = new Client();
            $chapters = substr($url, strrpos($url, '/') + 1);
            
            if (!empty($listCache) && $request->input('debug') != "yes") {
                $datas = json_decode($listCache);
            } else {
                // echo print_r($client->request('GET', $url));die();
            	$crawler = $client->request('POST', $url,[
                    'page' =>  $request->input('page'),
                    'params' => $request->input('params')
                ]);
            	$this->log->apiLog('api_laters-release-end');  
                $datas = [];
                $i = 0;

                // echo print_r($crawler);die;
                $startI = $i;
                $crawler->filter('h5.media-heading > a')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['title']  = $this->parseHTML($text);
                    $datas['listManga'][$startI]['title_href']  = $node->attr('href');
                    $datas['listManga'][$startI]['img']  = str_replace("manga", "uploads/manga", $node->attr('href'))."/cover/cover_250x350.jpg";
                    
                    $url = $node->attr('href');
                    $listCacheDetail =  Cache::get("headerOnly_".$url);  
                    if (!empty($listCacheDetail)) {
                        $arrListDetail = json_decode($listCacheDetail,true);
                        // unset($arrListDetail['listChapter']);
                        $datas['listManga'][$startI]['header'] = $arrListDetail;
                    }
                    
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('span.label')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['status']  = $this->parseHTML($text);
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('i.fa-eye')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['viewer']  = $this->parseHTML($text);
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('dd')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    if(!    in_array( $this->parseHTML($text) , ['Complete', 'Ongoing'])){
                        $datas['listManga'][$startI]['genre']  = $this->parseHTML($text);
                        $startI++;
                    }
                });
	            Cache::store('file')->put($cacheName, json_encode($datas), now()->addSeconds(60));
            }



            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $datas,
                        ];
            // echo base64_encode($crawler->text());
            // die();
            $this->log->apiLog('api_laters-release-end');  
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

    public function mangaDetail(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        // echo "mangaDetail";
        try {
            $kategori = $this->kategori();
            
            $url = $request->input('url');
            $listCache =  Cache::get($url);  
            $client = new Client();
            $chapters = substr($url, strrpos($url, '/') + 1);
            
            if (!empty($listCache)) {
                $datas = json_decode($listCache);
            } else {
                $crawler = $client->request('GET', $url);
                $this->log->apiLog('api_manga_detail');  
                $datas = [];
                $i = 0;
                $datas['Judul'] = $crawler->filter('h2.widget-title')->text();

                $datas['Img'] = $crawler->filter('img.img-responsive')->attr('src');

                if ($crawler->filter('div.well > div')->count() > 0) {
                    $datas['sinopsis'] = $crawler->filter('div.well > div')->text();
                } else {
                    $datas['sinopsis'] = "";
                }

                // filter(div.classname1.classname2 > ul.classname > li.classname)
                $startI = $i;
                $crawler->filter('dl.dl-horizontal > dt')->each(function ($node) use (&$datas,&$startI) {
                    $datas['header'][$startI]['title']  = trim(str_replace('\n', '', $node->text()));
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('dl.dl-horizontal > dd')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['header'][$startI]['label']  = $this->parseHTML($text);
                    $startI++;
                });
                // $crawler->filter('dl.dl-horizontal > dd')->each(function ($node) use (&$datas) {
                //     for ($i=0; $i < $datas['header'] ; $i++) { 
                //         if (empty($datas['header'][$i]['label'])) {
                //             $datas['header'][$i]['label'] = trim(str_replace('\n', '', $node->text()));
                //         }
                //     }
                //     // $datas['header'][]['title']  = trim(str_replace('\n', '', $node->text()));
                // });
                $startI = $i;
                $crawler->filter('td.chapter > a')->each(function ($node) use (&$datas,&$startI) {
                    $datas['listChapter'][$startI]['chapterHref']  = $node->attr('href');
                    $datas['listChapter'][$startI]['label']  = $node->text();
                    $datas['listChapter'][$startI]['noId']  = $startI;
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('td.date')->each(function ($node) use (&$datas,&$startI) {
                    $datas['listChapter'][$startI]['date']  = $node->text();
                    $startI++;
                });
                // $startI = $i;
                // $crawler->filter('div.date-chapter-title-rtl')->each(function ($node) use (&$datas,&$startI) {
                //     $text = $node->text();
                //     $datas['listChapter'][$startI]['date']  = $this->parseHTML($text);
                //     $startI++;
                // });

                $checkCategory = $i;
                foreach ($datas['header'] as $key => $value) {
                    if ($value['title'] == "Genre") {
                        $kategoriCount = 0;
                        foreach (explode(', ', $value['label']) as $key => $valKategori) {
                            if (!empty($kategori[trim($valKategori)])) {
                                $datas['header'][$i]['link'][$kategoriCount]['title'] = trim($valKategori);
                                $datas['header'][$i]['link'][$kategoriCount]['link'] = $kategori[trim($valKategori)];
                            }
                            $kategoriCount++;
                        };
                        // $datas['header'][$i]['link'] = "test";
                    }
                    $i++;
                }
                
                $datas['url'] = $request->input('url');
                Cache::store('file')->put($url, json_encode($datas), now()->addSeconds(100));
                
                $tempHeaderOnly = $datas;
                unset($tempHeaderOnly['listChapter']);
                Cache::store('file')->put("headerOnly_".$url, json_encode($tempHeaderOnly), now()->addSeconds(100));
            }


            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $datas,
                        ];
            // echo base64_encode($crawler->text());
            // die();
            $this->log->apiLog('api_manga_detail');  
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

    public function latestRelease(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        // echo "mangaDetail";
        try {
            $kategori = $this->kategori();
            // echo $request->input('debug');die;
            $url = "https://www.komikgue.com/latest-release?page=".$request->input('page');
            $listCache =  Cache::get($url);  
            $client = new Client();
            $chapters = substr($url, strrpos($url, '/') + 1);
            
            if (!empty($listCache) && $request->input('debug') != "yes") {
                $datas = json_decode($listCache);
            } else {
                // echo print_r($client->request('GET', $url));die();
            	$crawler = $client->request('GET', $url);
            	$this->log->apiLog('api_laters-release-end');  
                $datas = [];
                $i = 0;

                // if ($crawler->filter('div.well > p')->count() > 0) {
                //     $datas['sinopsis'] = $crawler->filter('div.well > p')->text();
                // } else {
                //     $datas['sinopsis'] = "";
                // }

                $startI = $i;
                $crawler->filter('h3.manga-heading > a')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['title']  = $this->parseHTML($text);
                    $datas['listManga'][$startI]['title_href']  = $node->attr('href');
                    $datas['listManga'][$startI]['img']  = str_replace("manga", "uploads/manga", $node->attr('href'))."/cover/cover_250x350.jpg";
                    
                    $url = $node->attr('href');
                    $listCacheDetail =  Cache::get("headerOnly_".$url);  
                    if (!empty($listCacheDetail)) {
                        $arrListDetail = json_decode($listCacheDetail,true);
                        // unset($arrListDetail['listChapter']);
                        $datas['listManga'][$startI]['header'] = $arrListDetail;
                    }
                    
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('h3.manga-heading')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $html = $node->html();
                    $htmlParse = $this->parseHTML($html);
                    if (strpos($htmlParse,"hot") > 0) {
                        $datas['listManga'][$startI]['hot']  = "hot";
                    } else {
                        $datas['listManga'][$startI]['hot']  = "";
                    }
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('small.pull-right')->each(function ($node) use (&$datas,&$startI) {
                    $text = $node->text();
                    $datas['listManga'][$startI]['date']  = $this->parseHTML($text);
                    $startI++;
                });


                // if($request->input('home') == "yes"){
                //     $temp = $datas['listManga'];
                //     $datas['listManga'] = [];
                //     $datas['listManga'] = array_slice($temp,0,10);
                // }
	            Cache::store('file')->put($url, json_encode($datas), now()->addSeconds(60*10));
            }



            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $datas,
                        ];
            // echo base64_encode($crawler->text());
            // die();
            $this->log->apiLog('api_laters-release-end');  
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
    public function chapterDetail(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        
        try {
            // sleep(5);
            $url = $request->input('url');
            $listCache =  Cache::get($url);  
            if (!empty($listCache) && $request->input('debug') != "yes") {
                $listChapterDetail = json_decode($listCache);
            } else {
                $listChapterDetail = [];
                $i = 0;
                $client = new Client();
                $chapters = substr($url, strrpos($url, '/') + 1);
                $crawler = $client->request('GET', $url);
                $startI = $i;
                $listChapterDetail['chapter'] = $chapters;
                
                $crawler->filter('li.previous > a')->each(function ($node) use (&$listChapterDetail,&$startI) {
                    if($node->attr('href') != "#"){
                        $listChapterDetail['previous_link']  = trim($node->attr('href'));
                    }
                });

                $crawler->filter('li.next > a')->each(function ($node) use (&$listChapterDetail,&$startI) {
                    if($node->attr('href') != "#"){
                        $listChapterDetail['next_link']  = trim($node->attr('href'));
                    }
                });

                
                $crawler->filter('ul.dropdown-menu > li.active > a')->each(function ($node) use (&$listChapterDetail,&$startI) {
                    if($node->attr('href') != "#"){
                        $listChapterDetail['chapter_link']  = trim($node->attr('href'));
                        $listChapterDetail['chapter_title']  = trim($node->text());
                    }
                });
                

                $crawler->filter('img.img-responsive')->each(function ($node) use (&$listChapterDetail,&$startI) {

                    $url = trim($node->attr('src')); 
                    // echo $url;
                    if (!empty($url)) {
                        $listChapterDetail['listChapterDetail'][$startI]['link']  = trim($node->attr('src')); ;
                        $startI++;
                    }
                });
                
                Cache::store('file')->put($url, json_encode($listChapterDetail), 60 * 60 * 24);
            }

            // Cache::store('database')->put($url, json_encode($listChapterDetail), 10);
            // $fullPath = "chache/".urlencode($url);
            // Storage::put($fullPath, json_encode($listChapterDetail));   

            // print_r($listChapterDetail);die;
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

    public function kategoriList(Request $request)
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        
        try {
            
            $url = 'https://www.komikgue.com/advanced-search';
            $listCache =  Cache::get("kategoriList");  
            if (!empty($listCache)  && $request->input('debug') != "yes") {
                $listData = json_decode($listCache);
            } else {
                $listData = [];
                $i = 0;
                $client = new Client();
                $chapters = substr($url, strrpos($url, '/') + 1);
                $crawler = $client->request('GET', $url);
                $startI = $i;
                $crawler->filter('select[name="categories[]"] > option')->each(function ($node) use (&$listData,&$startI) {

                    $idc = trim($node->attr('value'));
                    // $listData['categories'][$startI]["id"]  = $idc;
                    // $listData['categories'][$startI]["label"]  = $node->text();
                    $listData['categories'][$idc]  = $node->text();
                    $startI++;
                });

                $startI = $i;
                $crawler->filter('select[name="status[]"] > option')->each(function ($node) use (&$listData,&$startI) {

                    $idc = trim($node->attr('value'));
                    // $listData['status'][$startI]["id"]  = $idc;
                    // $listData['status'][$startI]["label"]  = $node->text();
                    $listData['status'][$idc]  = $node->text();
                    $startI++;

                });
                
                $startI = $i;
                $crawler->filter('select[name="types[]"] > option')->each(function ($node) use (&$listData,&$startI) {

                    $idc = trim($node->attr('value'));
                    // $listData['types'][$startI]["id"]  = $idc;
                    // $listData['types'][$startI]["label"]  = $node->text();
                    $listData['types'][$idc]  = $node->text();
                    $startI++;

                });
                
                Cache::store('file')->put("kategoriList", json_encode($listData), 60*60*24);
            }

            // Cache::store('file')->put($url, json_encode($listChapterDetail), 10);
            // Cache::store('database')->put($url, json_encode($listChapterDetail), 10);
            // $fullPath = "chache/".urlencode($url);
            // Storage::put($fullPath, json_encode($listChapterDetail));   

            // print_r($listChapterDetail);die;
            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $listData,
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

    function kategori()
    {
        
        try {
            
            $url = 'https://www.komikgue.com/manga-list?action=category';
            $listCache =  Cache::get($url);  
            if (!empty($listCache)) {
                $listData = json_decode($listCache ,true);
            } else {
                $listData = [];
                $client = new Client();
                $crawler = $client->request('GET', $url);
                $crawler->filter('ul.list-category > li > a ')->each(function ($node) use (&$listData) {
                    $listData[$node->text()]  = trim($node->attr('href'));
                });
                
            }

            Cache::store('file')->put($url, json_encode($listData), 100);


            $result = [
                            'status'=>'success',
                            'statusCode'=>200,
                            'desc'=>'success ',
                            'data'=> $listData,
                        ];
            return $listData;
        } catch (\Exception $e) {
            return [];
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
