<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Auth;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;

class HtmlParseController extends Controller
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

    public function chapterDetail()
    {

        #NOTES 
        // https://stackoverflow.com/questions/29067113/simple-html-dom-parser-for-laravel-5
        // https://github.com/FriendsOfPHP/Goutte
        $data['header'] = "DUMMMY!11  --- 1";


        // $url = "https://www.komikgue.com/uploads/manga/shingeki-no-kyojin/chapters/106/02.jpg";
        // $contents = file_get_contents($url);
        // Storage::put('manga/test/02.jpg', $contents);

        // echo $manga;
        $client = new Client();
        $url = 'https://www.komikgue.com/manga/shingeki-no-kyojin/103';
        // echo strrpos($url, '/');
        $chapters = substr($url, strrpos($url, '/') + 1);
        // die();

        $crawler = $client->request('GET', $url);
        // $link = $crawler->selectLink('Security Advisories')->link();
        // $crawler = $client->click($link);
        // $crawler->filter('a')->each(function ($node) {
        // $crawler->filter('a[class="o_title"][href]')->each(function ($node) {
        $crawler->filter('img ')->each(function ($node) {
            // print_r($node->link()));
            // print $node->text()."\n";
            $url = trim($node->attr('data-src')); 
            if (!empty($url)) {
                $name = substr($url, strrpos($url, '/') + 1);
                $fullPath = substr($url, strrpos($url, 'uploads/') );
                $contents = file_get_contents($url);
                Storage::put($fullPath, $contents);   
                echo $url ."\n";
                # code...
            }
            // echo $name ."\n";
        });

        // return view('dummy.dummy1',$data);
    }
}
