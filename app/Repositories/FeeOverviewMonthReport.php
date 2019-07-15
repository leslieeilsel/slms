<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FeeOverviewMonthReport
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
        $data = $this->getOverviewMonthData($params, 'export');
        $startMonth = str_replace('-', '.', $params['startMonth']);
        $endMonth = str_replace('-', '.', $params['endMonth']);

        $reportType = $params['reportType'];
        $reportName = '';
        switch ($reportType) {
            case 'fxf':
                $reportName = '发行费';
                break;
            case 'gyj':
                $reportName = '公益金';
                break;
            case 'yj':
                $reportName = '佣金';
                break;
            case 'fj':
                $reportName = '返奖';
                break;
        }
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
            ->setCellValue('A1', $reportName . '分配概览_彩票年_' . $startMonth . '-' . $endMonth . '（' . $range . '报）')
            ->setCellValue('A2', '单位：元')
            ->setCellValue('A3', '市区')
            ->setCellValue('B3', '' . $range . '体育彩票销量')
            ->setCellValue('J3', '' . $range . '分配体彩' . $reportName)
            ->setCellValue('Q3', $reportName . '合计')
            ->setCellValue('B4', '概率游戏')
            ->setCellValue('C4', '大乐透')
            ->setCellValue('D4', '排三')
            ->setCellValue('E4', '11选5')
            ->setCellValue('F4', '竞彩')
            ->setCellValue('G4', '足彩')
            ->setCellValue('H4', '即开型')
            ->setCellValue('I4', '总销量')
            ->setCellValue('J4', '概率游戏')
            ->setCellValue('K4', '大乐透')
            ->setCellValue('L4', '排三')
            ->setCellValue('M4', '11选5')
            ->setCellValue('N4', '竞彩')
            ->setCellValue('O4', '足彩')
            ->setCellValue('P4', '即开型');
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:Q1')
            ->mergeCells('A2:P2')
            ->mergeCells('A3:A4')
            ->mergeCells('B3:I3')
            ->mergeCells('J3:P3')
            ->mergeCells('Q3:Q4');
        // 添加动态数据
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $data,      // The data to set
                null,       // Array values with this value will not be set
                'A5'        // Top left coordinate of the worksheet range where
            // we want to set these values (default is A1)
            );
        //  设置宽度
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(16);
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
        $spreadsheet->getActiveSheet()->getStyle('B5:Q20')->applyFromArray($numberStyleArray);
        $centerStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:A20')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('B3:Q4')->applyFromArray($centerStyleArray);
        // 设置边框
        $allBordersStyleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:Q1')->applyFromArray($allBordersStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:Q20')->applyFromArray($allBordersStyleArray);
        $outlineBordersStyleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2:Q2')->applyFromArray($outlineBordersStyleArray);
        // 设置千分位
        $spreadsheet->getActiveSheet()->getStyle('B5:Q18')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('B19:Q19')->getNumberFormat()->setFormatCode('0.0%');
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 设置字体大小
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $reportName . '分配概览_彩票年_' . $startMonth . '-' . $endMonth . '（' . $range . '报）.xlsx"');
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
    public function getOverviewMonthData($params, $action)
    {
        $body = [];
        $reportType = $params['reportType'];
        unset($params['reportType']);
        $date = $params;

        switch ($reportType) {
            case 'fxf':
                $body = $this->getFxfOverviewMonthData($date, $action);
                break;
            case 'gyj':
                $body = $this->getGyjOverviewMonthData($date, $action);
                break;
            case 'yj':
                $body = $this->getYjOverviewMonthData($date, $action);
                break;
            case 'fj':
                $body = $this->getFjOverviewMonthData($date, $action);
                break;
        }

        return $body;
    }

    /**
     * 发行分配概览表
     *
     * @param array  $date
     * @param string $action
     * @return array
     */
    public function getFxfOverviewMonthData($date, $action)
    {
        $fee = ['0.04', '0.04', '0.04', '0.03', '0.005', '0.03', '0.015'];
        $body = $this->getMonthFeeData($date, $fee, $action);
        $body = $this->bodyFormat($body, $action);

        return $body;
    }

    /**
     * 公益金分配概览表
     *
     * @param array  $date
     * @param string $action
     * @return array
     */
    public function getGyjOverviewMonthData($date, $action)
    {
        $fee = ['0.0925', '0.09', '0.085', '0.07', '0.045', '0.055', '0.05'];
        $body = $this->getMonthFeeData($date, $fee, $action);
        $body = $this->bodyFormat($body, $action);

        return $body;
    }

    /**
     * 佣金分配概览表
     *
     * @param array  $date
     * @param string $action
     * @return array
     */
    public function getYjOverviewMonthData($date, $action)
    {
        $fee = ['0.08', '0.08', '0.08', '0.08', '0.08', '0.08', '0.1'];
        $body = $this->getMonthFeeData($date, $fee, $action);
        $body = $this->bodyFormat($body, $action);

        return $body;
    }

    /**
     * 返奖分配概览表
     *
     * @param array  $date
     * @param string $action
     * @return array
     */
    public function getFjOverviewMonthData($date, $action)
    {
        $fee = ['0.5', '0.51', '0.53', '0.59', '0.73', '0.65', '0.65'];
        $body = $this->getMonthFeeData($date, $fee, $action);
        $body = $this->bodyFormat($body, $action);

        return $body;
    }

    /**
     * 组织报表体数据
     *
     * @param array  $date
     * @param array  $fee
     * @param string $action
     * @return array
     */
    protected function getMonthFeeData($date, $fee, $action)
    {
        $body = [];
        $jin = [];
        $jinPer = [];
        $sale_jin = [];
        $sale = $this->getFeeMonthReportData($date, $action);
        //对应费率
        if (!empty($sale)) {
            foreach ($sale as $sk => $sv) {
                $temp = $sale[$sk];
                array_splice($sv, 0, 1);//删除地区
                array_splice($sv, -1, 1);//删除总量行
                if ($sk == 14 || $sk == 15) {
                    $jin[$sk] = $temp;
                    array_splice($jin[$sk], 0, 1);//增幅和排名行保持不变
                    $k = 0;//费率下标
                    foreach ($jin[$sk] as $jk => $jv) {
                        $sale[$sk]["fee" . $k] = $jv;
                        $k++;
                    }
                    $sale_jin[$sk] = $sale[$sk];
                } else {
                    $k = 0;//费率下标
                    foreach ($sv as $svk => $svv) {
                        $jinPer["fee" . $k] = $svv * $fee[$k];//其余行 销量*费率
                        $k++;
                    }
                    $jinPerCount = array_sum($jinPer);
                    $jinPer["fee" . $k] = $jinPerCount;
                    $jin[$sk] = $jinPer;
                    $sale_jin[$sk] = array_merge($sale[$sk], $jin[$sk]);
                }
                //合并销量和公益金
                $jinPer = [];
            }
            $body = $sale_jin;
        }

        return $body;
    }

    /**
     * @param array  $date
     * @param string $action
     * @return array
     */
    public function getFeeMonthReportData($date, $action)
    {
        $startDate = explode('-', $date['startMonth']);
        $endDate = explode('-', $date['endMonth']);
        $this->choose_year = $startDate[0];
        $this->last_year = $this->choose_year - 1;

        $table = $date['range'] === 'month' ? 'slms_sum_m_cp_region' : 'slms_sum_d_cp_region';

        $publicFun = new PublicReportRepository();

        $query = DB::table($table)
            ->join('ibiart_slms_game as game', 'game.id', '=', 'game_id')
            ->select(
                DB::raw("replace(region_name,' ','') as region_name"),
                DB::raw("left(date, 4) as year"),
                DB::raw("SUM(case when game.num IN('A0009','A0011','A0034') then sale_amt else 0 end) as tc1"),
                DB::raw("SUM(case when game.num='A0014' then sale_amt else 0 end) as tc2"),
                DB::raw("SUM(case when game.num='A0010' then sale_amt else 0 end) as tc3"),
                DB::raw("SUM(case when game.num='A0052' then sale_amt else 0 end) as tc4"),
                DB::raw("SUM(case when game.type=2 then sale_amt else 0 end) as tc5"),
                DB::raw("SUM(case when game.num IN('B009','B010','B012','B002') then sale_amt else 0 end) as tc6"),
                DB::raw("SUM(case when game.type=0 then sale_amt else 0 end) as tc7"));
        if ($date['range'] === 'month') {
            $query->whereIn(DB::raw('left(date, 4)'), [(int) $this->choose_year, (int) $this->last_year]);
            $query->whereBetween(DB::raw('right(date, 2)'), [(int) $startDate[1], (int) $endDate[1]]);
        } else {
            $thisDate = $publicFun->getDay($date['startMonth'], $date['endMonth']);
            $lastDate = $publicFun->getDay(
                $this->last_year . '-' . $startDate[1] . '-' . $startDate[2],
                $this->last_year . '-' . $endDate[1] . '-' . $endDate[2]
            );
            $query->whereIn('date', array_merge($thisDate, $lastDate));
        }
        $data = $query->whereIn('region_num', ['6101', '6106', '6107', '6110', '6113', '6116', '6117', '6119', '6121', '6124', '6127', '6130'])
            ->groupBy('year', 'region_name')
            ->orderBy('region_num')
            ->get();
        $data = collect($data)->groupBy('year');

        //组织今年-同比年数据
        $this_year = !empty($data[$this->choose_year]) ? $publicFun->dataByYear($data[$this->choose_year], $this->choose_year) : $this->setZeroData();
        $last_year = !empty($data[$this->last_year]) ? $publicFun->dataByYear($data[$this->last_year], $this->last_year) : $this->setZeroData();
        //添加合计+增幅行
        $this_year_ct = $publicFun->array_sum_column($this_year);
        $last_year_ct = $publicFun->array_sum_column($last_year);
        $great = $publicFun->greating($this_year_ct, $last_year_ct, $action);
        $order = $publicFun->orderGreaating($great);

        array_splice($this_year_ct, 0, 1, '合计');
        array_splice($last_year_ct, 0, 1, '上年同期');
        array_splice($great, 0, 1, '同比销售增幅');
        array_splice($order, 0, 1, '同比增幅排名');
        //组织body
        $body = $this_year;
        $body[12] = $this_year_ct;
        $body[13] = $last_year_ct;
        $body[14] = $great;
        $body[15] = $order;

        return $body;
    }

    /**
     * 组装所有的月
     *
     * @param array $startDate
     * @param array $endDate
     * @return array
     */
    public function buildMonthList($startDate, $endDate)
    {
        $dates = [];
        for ($i = $startDate[1]; $i <= $endDate[1]; $i++) {
            $dates[] = $this->choose_year . '-' . sprintf("%02d", $i);
            $dates[] = $this->choose_year - 1 . '-' . sprintf("%02d", $i);
        }

        return $dates;
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
        $newBody = [];
        foreach ($body as $key => $rows) {
            if ($key >= 14) {
                $newBody[$key] = $rows;
            } else {
                foreach ($rows as $k => $row) {
                    $row = ($k !== 0 && $row == 0) ? '0.00' : $row;
                    if ($action === 'page') {
                        $newBody[$key][$k] = ($k === 0) ? $row : number_format($row, 2);
                    } else {
                        $newBody[$key][$k] = ($k === 0) ? $row : $row;
                    }
                }
            }
        }

        return $newBody;
    }

    public function setZeroData()
    {
        $region = ['西安', '杨凌', '咸阳', '渭南', '宝鸡', '铜川', '商洛', '安康', '汉中', '延安', '榆林', '韩城'];

        $data = [];
        for ($i = 0; $i < 12; $i++) {
            $data[$i] = [
                $region[$i],
                'tc1' => 0,
                'tc2' => 0,
                'tc3' => 0,
                'tc4' => 0,
                'tc5' => 0,
                'tc6' => 0,
                'tc7' => 0,
                'tc8' => 0,
            ];
        }

        return $data;
    }
}
