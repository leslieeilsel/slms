<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CpStoreReport
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
        $data = $this->getStoreReportData($params, 'export');
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
            ->setCellValue('A1', '投注站销量统计_彩票年_' . $startMonth . '-' . $endMonth . '（' . $range . '报）')
            ->setCellValue('A2', '单位：元')
            ->setCellValue('A3', '投注站')
            ->setCellValue('B3', '概率游戏')
            ->setCellValue('C3', '大乐透')
            ->setCellValue('D3', '排三')
            ->setCellValue('E3', '11选5')
            ->setCellValue('F3', '竞彩')
            ->setCellValue('G3', '足彩')
            ->setCellValue('H3', '即开型')
            ->setCellValue('I3', '总销量');
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:I1')
            ->mergeCells('A2:H2');
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
        $spreadsheet->getActiveSheet()->getStyle('A4:I' . ($count + 3))->applyFromArray($numberStyleArray);
        $centerStyleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:A' . ($count + 3))->applyFromArray($centerStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('B3:I3')->applyFromArray($centerStyleArray);
        // 设置边框
        $allBordersStyleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($allBordersStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:I' . ($count + 3))->applyFromArray($allBordersStyleArray);
        $outlineBordersStyleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2:I2')->applyFromArray($outlineBordersStyleArray);
        // 设置千分位
        $spreadsheet->getActiveSheet()->getStyle('B4:I' . ($count + 3))->getNumberFormat()->setFormatCode('#,##0.00');
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 设置字体大小
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="投注站销量统计-' . $range . '报.xlsx"');
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
    public function getStoreReportData($params, $action)
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
        $body = $this->toGetData($date);
        $body = $this->bodyFormat($body, $action);

        return $body;
    }

    /**
     * @param array $date
     * @return array
     */
    public function toGetData($date)
    {
        $table = $date['range'] === 'month' ? 'slms_sum_m_cp_store' : 'slms_sum_d_cp_store';

        $query = DB::table($table)->select('store_num', 'sale_gl', 'sale_dlt', 'sale_pl', 'sale_xuan', 'sale_jc', 'sale_zc', 'sale_jk', 'sale_total');

        $query->whereBetween('date', [$date['startMonth'], $date['endMonth']]);

        if (isset($date['region']) && $date['region'] != '-1') {
            $query = $query->where(DB::raw('left(store_num, 4)'), $date['region']);
        }
        if (isset($date['store_num']) && $date['store_num'] != '-1') {
            $query = $query->where('store_num', $date['store_num']);
        }

        $data = $query->limit(100)->get()->toArray();

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
                if ($k !== 'store_num') {
                    $body[$key][$k] = ($action === 'page') ? number_format($row, 2) : $row;
                }
            }
        }

        return $body;
    }
}
