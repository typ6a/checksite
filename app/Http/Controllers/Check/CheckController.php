<?php

namespace App\Http\Controllers\Check;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckFormRequest;
use App\Http\Requests\SaveFormRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Enum\CheckMetrics;

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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function results()
    {
        $data = [
            'checkResult' => $this->check(),
            'metrics' => CheckMetrics::$metrics,
        ];
        // pre($data,1);

        
        return view('check.result')->with('data', $data);
        // // pre('12354',1);
        // $tableData = [];
        // $tableData['tableHead'] =[
        //     '№', 'Название проверки', 'Статус' , '&nbsp' , 'Текущее состояние' 
        // ] ;

            
        pre(CheckMetrics::$metrics,1);
    }

    protected function check(CheckFormRequest $request)
    {   
        $data = [
            CheckMetrics::ROBOTS_EXISTS => [
                'status' => false,
                'value' => '',
            ],
            CheckMetrics::HTTP_CODE => [
                'status' => false,
                'value' => 404,
            ],
            CheckMetrics::HOST_EXISTS => [
                'status' => false,
                'value' => '',
            ],
            CheckMetrics::HOST_COUNT => [
                'status' => false,
                'value' => 0,
            ],
            CheckMetrics::SITEMAP_EXISTS => [
                'status' => false,
                'value' => '',
            ],
            CheckMetrics::ROBOTS_SIZE => [
                'status' => false,
                'value' => 0,
            ],
            'targetUrl' => '',
        ];

        $requestUrl = mb_strtolower($request['url']);
        $requestUrl = str_replace('http://', '', $requestUrl);
        $requestUrl = str_replace('https://', '', $requestUrl);
        $requestUrl = str_replace('www.', '', $requestUrl);
        $requestUrl = 'http://' . $requestUrl;
        $url = 'http://' . parse_url($requestUrl)['host'];

        $robotstxtUrl = $url  . '/robots.txt';

        $result = @file_get_contents($robotstxtUrl);
        if(preg_match('/^HTTP\/1\.1\040([^\040]\d+).*$/', $http_response_header[0], $matches)){
            if(!empty($matches[1])){
                $httpCode = $matches[1];
            }
        }
        if((int) $httpCode === 200){
            $data['httpCode']['status'] = true;
        }   
        $data['httpCode']['value'] = $httpCode;

        $data['targetUrl'] = $robotstxtUrl;
        $robotstxtContents = @file_get_contents($robotstxtUrl);
        if ($robotstxtContents !== false) {
            $data['robotstxtPresents']['status'] = true;
            file_put_contents(storage_path() . '\sitecheck\robots.txt', $robotstxtContents);
            $filesize = round((filesize(storage_path() . '\sitecheck\robots.txt') / 1024), 2);
            $data['robotstxtSize']['status'] = (float) $filesize > 32 ? false : true;
            $data['robotstxtSize']['value'] = $filesize;
        }
        
        $robotstxt = @file(storage_path() . '\sitecheck\robots.txt');
        if ($data['robotstxtPresents']) {
            foreach($robotstxt as $line) {
                if (mb_stristr($line, 'host') !== false) {
                    $data['hostDirectivePresents']['status'] = true;
                    $data['hostDirectiveNum']['status'] = true;
                    $data['hostDirectiveNum']['value']++;
                }
                if (mb_stristr($line, 'sitemap') !== false) {
                    $data['sitemapDirectiveExists']['status'] = true;
                }
            }
        }

        $data = [
            'checkResult' => $data,
            'metrics' => CheckMetrics::$metrics,
        ];
        pre($data);
        @unlink(storage_path() . '\sitecheck\robots.txt');
        // return $data;
        return view('check.result')->with('data', $data);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function save()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('hello world.xlsx'));




        pre('',1);
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
