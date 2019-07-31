<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ZjRegionReport
{
    public $choose_year;
    public $last_year;

    public function __construct()
    {
    }

    /**
     * 导出报表
     *
     * @param array $params
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export($params)
    {
        $data = $this->getRegionReportData($params, 'export');
        $startMonth = str_replace('-', '.', $params['startMonth']);
        $endMonth = str_replace('-', '.', $params['endMonth']);
        $range = $params['range'] === 'month' ? '月' : '日';
        // 创建一个Spreadsheet对象
        $spreadsheet = new Spreadsheet();
        // 设置文档属性
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        // 添加表头
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '区域中奖统计_' . $startMonth . '-' . $endMonth . '（' . $range . '报）')
            ->setCellValue('A2', '单位：元')
            ->setCellValue('A3', '地市')
            ->setCellValue('B3', '热线中奖')
            ->setCellValue('C3', '高频中奖')
            ->setCellValue('D3', '竞彩中奖')
            ->setCellValue('E3', '即开中奖')
            ->setCellValue('F3', '中奖合计');
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:F1')
            ->mergeCells('A2:E2');
        // 添加动态数据
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $data,      // The data to set
                null,       // Array values with this value will not be set
                'A4'        // Top left coordinate of the worksheet range where
            // we want to set these values (default is A1)
            );
        //  设置宽度
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        // 设置高度
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        // 设置对齐方式
        $numberStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($numberStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A4:F16')->applyFromArray($numberStyleArray);
        $centerStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:A16')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('B3:F3')->applyFromArray($centerStyleArray);
        // 设置边框
        $allBordersStyleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->applyFromArray($allBordersStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:F16')->applyFromArray($allBordersStyleArray);
        $outlineBordersStyleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2:F2')->applyFromArray($outlineBordersStyleArray);
        // 设置千分位
        $spreadsheet->getActiveSheet()->getStyle('B4:F16')->getNumberFormat()->setFormatCode('#,##0.00');
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 设置字体大小
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="区域中奖统计_' . $startMonth . '-' . $endMonth . '（' . $range . '报）.xlsx"');
        header('Cache-Control: max-age=0');
        // 如果正在使用IE 9
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');

        exit;
    }

    /**
     * 入口函数
     *
     * @param array  $params
     * @param string $action
     * @return array
     */
    public function getRegionReportData($params, $action)
    {
        $body = $this->getData($params, $action);

        return $body;
    }

    /**
     * 发行分配概览表
     *
     * @param array  $date
     * @param string $action
     * @return array
     */
    public function getData($date, $action)
    {
        $body = $this->toGetData($date, $action);
        $body = $this->bodyFormat($body, $action);

        return $body;
    }

    /**
     * @param array  $date
     * @param string $action
     * @return array
     */
    public function toGetData($date, $action)
    {
        $table = $date['range'] === 'month' ? 'slms_sum_m_zj_store' : 'slms_sum_d_zj_store';

        $query = DB::table($table)
            ->join('ibiart_slms_region as region', 'region.num', '=', DB::raw('LEFT ( store_num, 4 )'))
            ->select(DB::raw("region.name as region_name"), 'claim_rx as rx', 'claim_gp as gp', 'claim_jc as jc', 'claim_jk as jk', 'claim_total as total');

        $query->whereBetween('date', [$date['startMonth'], $date['endMonth']]);

        $data = $query->whereIn(DB::raw('LEFT ( store_num, 4 )'), ['6101', '6106', '6107', '6110', '6113', '6116', '6117', '6119', '6121', '6124', '6127', '6130'])
            ->groupBy('region_name')
            ->orderBy(DB::raw('LEFT ( store_num, 4 )'))
            ->get()
            ->toArray();

        $data = array_merge($data, [$this->addGameTotal($data)]);

        return $data;
    }

    /**
     * 数字格式化
     *
     * @param $body
     * @param $action
     * @return array
     */
    public function bodyFormat($body, $action)
    {
        foreach ($body as $key => $rows) {
            foreach ($rows as $k => $row) {
                if ($k !== 'region_name') {
                    $body[$key][$k] = ($action === 'page') ? number_format($row, 2) : (string) $row;
                }
            }
        }

        return $body;
    }

    public function addGameTotal($data)
    {
        return [
            'region_name' => '合计',
            'rx' => array_sum(array_column($data, 'rx')),
            'gp' => array_sum(array_column($data, 'gp')),
            'jc' => array_sum(array_column($data, 'jc')),
            'jk' => array_sum(array_column($data, 'jk')),
            'total' => array_sum(array_column($data, 'total')),
        ];
    }
}
