<?php

namespace App\Http\Controllers\Check;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckFormRequest;
use App\Http\Requests\SaveFormRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Enum\CheckMetrics;
use PhpOffice\PhpSpreadsheet\Cell as Fuck;

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

        function parseHttpCode($http_response_string){
            if(preg_match('/^HTTP\/1\.1\040([^\040]\d+).*$/', $http_response_string, $matches)){
                if(!empty($matches[1])){
                    return $matches[1];
                }
            }
            return false;
        }

        $robotstxtUrl = $url  . '/robots.txt';
        $result = @file_get_contents($robotstxtUrl);
        //pre($http_response_header,1);


        $httpCodes = [];
        foreach ($http_response_header as $line) {
            if (stristr($line, 'HTTP/')) {
                $httpCodes[] = parseHttpCode($line);
            }
        }

        $httpCode = end($httpCodes);


        if((int) $httpCode === 200){
            $data[CheckMetrics::HTTP_CODE]['status'] = true;

            $data['targetUrl'] = $robotstxtUrl;
            $robotstxtContents = @file_get_contents($robotstxtUrl);
            if ($robotstxtContents !== false) {
                $data[CheckMetrics::ROBOTS_EXISTS]['status'] = true;
                file_put_contents(storage_path() . '\sitecheck\robots.txt', $robotstxtContents);
                $filesize = round((filesize(storage_path() . '\sitecheck\robots.txt') / 1024), 2);
                $data[CheckMetrics::ROBOTS_SIZE]['status'] = (float) $filesize > 32 ? false : true;
                $data[CheckMetrics::ROBOTS_SIZE]['value'] = $filesize;
            }
            

            $robotstxt = @file(storage_path() . '\sitecheck\robots.txt');
            //pre($robotstxt,1);
            if ($data[CheckMetrics::ROBOTS_EXISTS]['status']) {
                foreach($robotstxt as $line) {
                    if (mb_stristr($line, 'host') !== false) {
                        $data[CheckMetrics::HOST_EXISTS]['status'] = true;
                        $data[CheckMetrics::HOST_COUNT]['status'] = true;
                        $data[CheckMetrics::HOST_COUNT]['value']++;
                    }
                    if (mb_stristr($line, 'sitemap') !== false) {
                        $data[CheckMetrics::SITEMAP_EXISTS]['status'] = true;
                    }
                }
            }
        }   
        $data[CheckMetrics::HTTP_CODE]['value'] = $httpCode;

// pre($data,1);
        $data = [
            'checkResult' => $data,
            'metrics' => CheckMetrics::$metrics,
        ];
        @unlink(storage_path() . '\sitecheck\robots.txt');
        session(['data' => $data]);
        return view('check.result')->with('data', $data);
    }

    public static function getCells($columnNumber, $columnWidth, $rowNumer, $rowHeight)
    {
        // column starts
        $xM = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnNumber);
        // column ends
        $xN = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnNumber + ($columnWidth - 1));
        // row starts
        $yM = $rowNumer;
        // row ends
        $yN = ($rowHeight > 0) ? $rowNumer + ($rowHeight - 1) : $rowNumer;
        return "{$xM}{$yM}:{$xN}{$yN}";
    }

    public function export()
    {   
        $data = session('data');
        
        //pre($data,1);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cellsChars = ['A', 'B', 'C', 'D', 'E'];

        $header = [
            'No',
            'Title',
            'Status',
            'Empty',
            'State',
        ];

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Название проверки');
        $sheet->setCellValue('C1', 'Статус');
        $sheet->setCellValue('D1', '');
        $sheet->setCellValue('E1', 'Текущее состояние');

        $currentRow = 2;
        foreach($data['metrics'] as $metric_key => $metric){
            foreach($header as $column_index => $column){
                $xM = $cellsChars[($column_index)];
                $xN = $xM;
                $yM = $currentRow;
                $yN = $yM;
                
                $code = "{$xM}{$yM}:{$xN}{$yN}";

                $number = $data['checkResult'][$metric_key]['number'];
                $title = $data['checkResult'][$metric_key]['title'];
                $status = $data['checkResult'][$metric_key]['status'];
                $state = $data['checkResult'][$metric_key]['state'];

                $sheet->setCellValue($code, $number);
                $sheet->setCellValue($code, $title);
                $sheet->setCellValue($code, ($status ? 'Ok' : 'Error'));
                $sheet->setCellValue($code, '');
                $sheet->setCellValue($code, $state);

                pre($code);
            }
            $currentRow++;
        }



        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('result.xlsx'));




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
