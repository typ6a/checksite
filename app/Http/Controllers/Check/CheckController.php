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
  
    public function index()
    {
        return view('check/index');
    }

    public function results()
    {
        $data = [
            'checkResult' => $this->check(),
            'metrics' => CheckMetrics::$metrics,
        ];
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

        $data = [
            'checkResult' => $data,
            'metrics' => CheckMetrics::$metrics,
        ];
        @unlink(storage_path() . '\sitecheck\robots.txt');
        session(['data' => $data]);
        return view('check.result')->with('data', $data);
    }

    public function export()
    {   
        $data = session('data');
        // pre($data,1);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getColumnDimension('A')->setWidth(9);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(75);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('D')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('E')->getAlignment()->setWrapText(true);


        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Название проверки');
        $sheet->setCellValue('C1', 'Статус');
        $sheet->setCellValue('D1', '');
        $sheet->setCellValue('E1', 'Текущее состояние');
        $sheet->getStyle("A1:E1")->getFont()->setBold(true);
        $currentRow = 2;
// pre($data);
        $show8 = true;
        foreach($data['metrics'] as $metric_key => $metric){
                if(!$data['checkResult'][$metric_key]['status']){
                    $show8 = false;
                }
                if($metric_key === \App\Enum\CheckMetrics::HOST_COUNT && !$show8) { 
                    continue;
                }
            $number = $metric['number'];
            $title = $metric['title'];
            $status = $data['checkResult'][$metric_key]['status'];

            if ($status) {
                $state = sprintf($metric['status']['ok']['state'], $data['checkResult'][$metric_key]['value']);
                $recomendation = $metric['status']['ok']['recomendation'];
            } else {
                $state = $metric['status']['error']['state'];
                $recomendation = $metric['status']['error']['recomendation'];
            }

            $sheet->setCellValue("A{$currentRow}", $number);
            $sheet->setCellValue("B{$currentRow}", $title);
            $sheet
                ->setCellValue("C{$currentRow}", ($status ? 'Ok' : 'Error'))
                ->getStyle("C{$currentRow}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB($status ? '8055FF33' : '80FF3355');
            $sheet->setCellValue("D{$currentRow}", 'Состояние');
            $sheet->setCellValue("E{$currentRow}", $state);

            $currentRow++;

            $sheet->setCellValue("A{$currentRow}", $number);
            $sheet->setCellValue("B{$currentRow}", $title);
            $sheet->setCellValue("C{$currentRow}", ($status ? 'Ok' : 'Error'));
            $sheet->setCellValue("D{$currentRow}", 'Рекомендации');
            $sheet->setCellValue("E{$currentRow}", $recomendation);

            $sheet->mergeCells('A' . ($currentRow - 1) . ':A' . $currentRow);
            $sheet->mergeCells('B' . ($currentRow - 1) . ':B' . $currentRow);
            $sheet->mergeCells('C' . ($currentRow - 1) . ':C' . $currentRow);

            $currentRow++;
            if ($data['checkResult']['robotstxtPresents']['status'] === false) {
                break;
            }
            if (($data['checkResult']['hostDirectivePresents']['status'] === false) && $currentRow == 8) {
                continue;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('result.xlsx'));
        return response()->download(storage_path('result.xlsx'))->deleteFileAfterSend(true);
    }
}
