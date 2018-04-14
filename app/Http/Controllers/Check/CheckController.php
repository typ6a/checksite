<?php

namespace App\Http\Controllers\Check;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckFormRequest;

class CheckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('check/index');
        $url = $request->input('url');
        // $url = $request->url;
        pre('12354',1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    // facebook.com
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function check(CheckFormRequest $request)
    {   
        $data = [
            'robotstxtPresent' => false,
            'httpCode' => '',
            'hostDirectiveNum' => 0,
            'sitemapDirectiveNum' => 0,
            'robotstxtSize' => 0,
            'targetUrl' => ''
        ];
        $requestUrl = mb_strtolower($request['url']);
        $requestUrl = str_replace('http://', '', $requestUrl);
        $requestUrl = str_replace('https://', '', $requestUrl);
        $requestUrl = str_replace('www.', '', $requestUrl);
        $requestUrl = 'http://' . $requestUrl;
        $url = parse_url($requestUrl)['host'];
        $robotstxtUrl = $url  . '/robots.txt';
        // pre($robotstxtUrl);
        // $robotstxt = '';
        // $agents = array(preg_quote('*'));
        // if($useragent) $agents[] = preg_quote($useragent, '/');
        // $agents = implode('|', $agents);


        $handle = curl_init($robotstxtUrl);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($handle)['redirect_url'];//?
        if (!empty($redirectUrl)) {
            $robotstxtUrl = $redirectUrl;
        }
        $data['targetUrl'] = $robotstxtUrl;
        // pre($robotstxtUrl);
        // if ($httpCode != 400) {
        //     $data['robotstxtPresent'] = true;
        // }
        // pre($data['robotstxtPresent'],1);
        $data['httpCode'] = $httpCode;
        curl_close($handle);
        
        $robotstxtContents = @file_get_contents($robotstxtUrl);
        if ($robotstxtContents === false) {
            $data['robotstxtPresent'] = false;
        } else {
            $data['robotstxtPresent'] = true;
            file_put_contents(storage_path() . '\sitecheck\robots.txt', $robotstxtContents);
            $data['robotstxtSize'] = round((filesize(storage_path() . '\sitecheck\robots.txt') / 1024), 2);
        }
        $robotstxt = @file(storage_path() . '\sitecheck\robots.txt');
        if ($data['robotstxtPresent']) {
            # code...
            foreach($robotstxt as $line) {
      // skip blank lines
            // if (!$line = trim($line)) continue;
                if (mb_stristr($line, 'host') != false){
                    $data['hostDirectiveNum'] ++;
                }
                if (mb_stristr($line, 'sitemap') != false){
                    $data['sitemapDirectiveNum'] ++;
                }
            // pre(mb_stristr($line, 'sitemap'));
            }
        }
        pre($robotstxt);
        @unlink(storage_path() . '\sitecheck\robots.txt');
        pre($data);
        return view('check.details')->with('data', $data);
    // $arrContextOptions=array(
    //   "ssl"=>array(
    //         "verify_peer"=>false,
    //         "verify_peer_name"=>false,
    //     ),
    // );
    // $html = file_get_contents($url, false, stream_context_create($arrContextOptions));
    // $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    // pre($html,1);
    //     $is_url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) !== false;
    //     pre($is_url,1);
    //     // return $request->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function importExcel()
    {
         Excel::create('thecodingstuff', function($excel) {
            $excel->sheet('thecodingstuff', function($sheet) {
                $sheet->loadView('Check.details');
            });
        })->export('xls');
        //return view('thecodingstuff.bladexcel');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
