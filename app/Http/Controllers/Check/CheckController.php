<?php

namespace App\Http\Controllers\Check;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckFormRequest;

use Illuminate\Support\Facades\Storage;

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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function check(CheckFormRequest $request)
    {   
        $data = [
            'robotstxtPresents' => false,
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
        $url = 'http://' . parse_url($requestUrl)['host'];
        $robotstxtUrl = $url  . '/robots.txt';
        $handle = curl_init($robotstxtUrl);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $data['httpCode'] = $httpCode;
        $redirectUrl = curl_getinfo($handle)['redirect_url'];//?
        if (!empty($redirectUrl)) {
            $robotstxtUrl = $redirectUrl;
        }
        $data['targetUrl'] = $robotstxtUrl;
        curl_close($handle);
        pre($robotstxtUrl);
        $robotstxtContents = @file_get_contents($robotstxtUrl);
        if ($robotstxtContents === false) {
            $data['robotstxtPresents'] = false;
        } else {
            $data['robotstxtPresents'] = true;
            file_put_contents(storage_path() . '\sitecheck\robots.txt', $robotstxtContents);
            $data['robotstxtSize'] = round((filesize(storage_path() . '\sitecheck\robots.txt') / 1024), 2);
        }
        $robotstxt = @file(storage_path() . '\sitecheck\robots.txt');
        if ($data['robotstxtPresents']) {
            foreach($robotstxt as $line) {
                if (mb_stristr($line, 'host') != false){
                    $data['hostDirectiveNum'] ++;
                }
                if (mb_stristr($line, 'sitemap') != false){
                    $data['sitemapDirectiveNum'] ++;
                }
            }
        }
        @unlink(storage_path() . '\sitecheck\robots.txt');
        return view('check.details')->with('data', $data);
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
