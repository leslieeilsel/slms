<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CpRegionReport
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
            ->setCellValue('A1', '区域销量统计_彩票年_' . $startMonth . '-' . $endMonth . '（' . $range . '报）')
            ->setCellValue('A2', '单位：元')
            ->setCellValue('A3', '市区')
            ->setCellValue('B3', '' . $range . '体育彩票销量')
            ->setCellValue('B4', '概率游戏')
            ->setCellValue('C4', '大乐透')
            ->setCellValue('D4', '排三')
            ->setCellValue('E4', '11选5')
            ->setCellValue('F4', '竞彩')
            ->setCellValue('G4', '足彩')
            ->setCellValue('H4', '即开型')
            ->setCellValue('I4', '总销量');
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:I1')
            ->mergeCells('A2:H2')
            ->mergeCells('A3:A4')
            ->mergeCells('B3:I3');
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
        $spreadsheet->getActiveSheet()->getStyle('B5:I20')->applyFromArray($numberStyleArray);
        $centerStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:A20')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('B3:I4')->applyFromArray($centerStyleArray);
        // 设置边框
        $allBordersStyleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($allBordersStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:I20')->applyFromArray($allBordersStyleArray);
        $outlineBordersStyleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2:I2')->applyFromArray($outlineBordersStyleArray);
        // 设置千分位
        $spreadsheet->getActiveSheet()->getStyle('B5:I18')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('B19:I19')->getNumberFormat()->setFormatCode('0.0%');
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 设置字体大小
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="区域销量统计_彩票年_' . $startMonth . '-' . $endMonth . '（' . $range . '报）.xlsx"');
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
            $query->whereIn('date', $this->buildMonthList($startDate, $endDate));
        } else {
            $thisDate = $publicFun->getDay($date['startMonth'], $date['endMonth']);
            $lastDate = $publicFun->getDay(
                $this->last_year . '-' . $startDate[1] . '-' . $startDate[2],
                $this->last_year . '-' . $endDate[1] . '-' . $endDate[2]
            );
            $query->whereIn('date', array_merge($thisDate, $lastDate));
        }
        $data = $query->whereIn('region_num', ['6101', '6106', '6107', '6133', '6110', '6113', '6116', '6117', '6119', '6121', '6124', '6127', '6130'])
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
        $count = count($body);
        $body[$count] = $this_year_ct;
        $body[$count + 1] = $last_year_ct;
        $body[$count + 2] = $great;
        $body[$count + 3] = $order;

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
            if ($key >= count($body) - 2) {
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
