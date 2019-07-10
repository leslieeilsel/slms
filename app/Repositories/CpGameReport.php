<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CpGameReport
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
        $data = $this->getGameReportData($params, 'export');
        $count = count($data);
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
            ->setCellValue('A1', $startMonth . '-' . $endMonth . '玩法销量统计（' . $range . '报）')
            ->setCellValue('A2', '单位：元')
            ->setCellValue('A3', '序号')
            ->setCellValue('B3', '游戏名称')
            ->setCellValue('C3', '西安')
            ->setCellValue('D3', '杨凌')
            ->setCellValue('E3', '咸阳')
            ->setCellValue('F3', '渭南')
            ->setCellValue('G3', '宝鸡')
            ->setCellValue('H3', '铜川')
            ->setCellValue('I3', '商洛')
            ->setCellValue('J3', '安康')
            ->setCellValue('K3', '汉中')
            ->setCellValue('L3', '延安')
            ->setCellValue('M3', '榆林')
            ->setCellValue('N3', '韩城')
            ->setCellValue('O3', '合计');
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:O1')
            ->mergeCells('A2:N2');
        // 添加动态数据
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $data,      // The data to set
                null,       // Array values with this value will not be set
                'A4'        // Top left coordinate of the worksheet range where
            // we want to set these values (default is A1)
            );
        //  设置宽度
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
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
        // 设置高度
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        // 设置字体大小
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        // 设置对齐方式
        $numberStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($numberStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('C4:O' . ($count + 3))->applyFromArray($numberStyleArray);
        $centerStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:A' . ($count + 3))->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('B3:O3')->applyFromArray($centerStyleArray);
        // 设置边框
        $allBordersStyleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:O1')->applyFromArray($allBordersStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:O' . ($count + 3))->applyFromArray($allBordersStyleArray);
        $outlineBordersStyleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2:O2')->applyFromArray($outlineBordersStyleArray);
        // 设置千分位
        $spreadsheet->getActiveSheet()->getStyle('C4:O' . ($count + 3))->getNumberFormat()->setFormatCode('#,##0.00');
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);

        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="玩法销量统计-' . $range . '报.xlsx"');
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
    public function getGameReportData($params, $action)
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
    public function toGetData($date)
    {
        $startDate = explode('-', $date['startMonth']);
        $endDate = explode('-', $date['endMonth']);

        $table = $date['range'] === 'month' ? 'slms_sum_m_cp_region' : 'slms_sum_d_cp_region';

        $query = DB::table($table)
            ->join('ibiart_slms_game as game', 'game.id', '=', 'game_id')
            ->select(
                'game.name as game_name',
                DB::raw("SUM(case when region_num = 6101 then sale_amt else 0 end ) as xian"),
                DB::raw("SUM(case when region_num = 6106 then sale_amt else 0 end ) as yangling"),
                DB::raw("SUM(case when region_num = 6107 then sale_amt else 0 end ) as xianyang"),
                DB::raw("SUM(case when region_num = 6110 then sale_amt else 0 end ) as weinan"),
                DB::raw("SUM(case when region_num = 6113 then sale_amt else 0 end ) as baoji"),
                DB::raw("SUM(case when region_num = 6116 then sale_amt else 0 end ) as tongchuan"),
                DB::raw("SUM(case when region_num = 6117 then sale_amt else 0 end ) as shangluo"),
                DB::raw("SUM(case when region_num = 6119 then sale_amt else 0 end ) as ankang"),
                DB::raw("SUM(case when region_num = 6121 then sale_amt else 0 end ) as hanzhong"),
                DB::raw("SUM(case when region_num = 6124 then sale_amt else 0 end ) as yanan"),
                DB::raw("SUM(case when region_num = 6127 then sale_amt else 0 end ) as yulin"),
                DB::raw("SUM(case when region_num = 6130 then sale_amt else 0 end ) as hancheng"));
        if ($date['range'] === 'month') {
            $query->whereIn('date', $this->buildMonthList($startDate, $endDate));
        } else {
            $query->whereBetween('date', [$date['startMonth'], $date['endMonth']]);
        }
        if (isset($date['gameType']) && $date['gameType'] != '-1') {
            $query = $query->where('game.type', $date['gameType']);
        }
        if (isset($date['gameName'])) {
            $query = $query->where('game_name', 'like', '%' . $date['gameName'] . '%');
        }
        $data = $query->groupBy('game.num')->orderBy('game.num')->get()->toArray();

        $data = $this->addGameTotal($data);
        $data = array_merge($data, [$this->addRegionTotal($data)]);

        return $data;
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
            $dates[] = $startDate[0] . '-' . sprintf("%02d", $i);
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
        foreach ($body as $key => $rows) {
            foreach ($rows as $k => $row) {
                if ($k !== 'game_name' && $k !== 'num') {
                    $body[$key][$k] = ($action === 'page') ? number_format($row, 2) : $row;
                }
            }
        }

        return $body;
    }

    /**
     * 组织xx年数据
     *
     * @param array  $data
     * @return array
     */
    public function addGameTotal($data)
    {
        $year = [];
        foreach ($data as $dk => $dv) {
            $temp['num'] = $dk + 1;
            $this->array_insert($dv, 0, $temp);
            $dv['game_total'] = $dv['xian'] + $dv['yangling'] + $dv['xianyang'] + $dv['weinan'] + $dv['baoji'] + $dv['tongchuan'] + $dv['shangluo'] + $dv['ankang'] + $dv['hanzhong'] + $dv['yanan'] + $dv['yulin'] + $dv['hancheng'];
            $year[] = $dv;
        }

        return $year;
    }

    public function addRegionTotal($data)
    {
        return [
            'num' => '-',
            'game_name' => '合计',
            'xian' => array_sum(array_column($data, 'xian')),
            'yangling' => array_sum(array_column($data, 'yangling')),
            'xianyang' => array_sum(array_column($data, 'xianyang')),
            'weinan' => array_sum(array_column($data, 'weinan')),
            'baoji' => array_sum(array_column($data, 'baoji')),
            'tongchuan' => array_sum(array_column($data, 'tongchuan')),
            'shangluo' => array_sum(array_column($data, 'shangluo')),
            'ankang' => array_sum(array_column($data, 'ankang')),
            'hanzhong' => array_sum(array_column($data, 'hanzhong')),
            'yanan' => array_sum(array_column($data, 'yanan')),
            'yulin' => array_sum(array_column($data, 'yulin')),
            'hancheng' => array_sum(array_column($data, 'hancheng')),
            'game_total' => array_sum(array_column($data, 'game_total'))
        ];
    }

    public function array_insert(&$array, $position, $insert_array)
    {
        $first_array = array_splice ($array, 0, $position);
        $array = array_merge ($first_array, $insert_array, $array);
    }
}
