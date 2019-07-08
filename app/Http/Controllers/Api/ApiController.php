<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CpRegionReport;
use App\Repositories\FeeOverviewMonthReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Exception;

class ApiController extends Controller
{
    /**
     * 获取概览表（月报）数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getOverviewMonthData(Request $request)
    {
        $param = $request->input();
        $report = new FeeOverviewMonthReport();
        $data = $report->getOverviewMonthData($param, 'page');

        return response()->json(['result' => $data, 'baseUrl' => env('APP_URL')], 200);
    }

    /**
     * 导出概览表（月报）数据
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $reportType
     * @param string $range
     * @return void
     * @throws Exception
     */
    public function exportOverviewMonthData($startMonth, $endMonth, $reportType, $range)
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'reportType' => $reportType,
            'range' => $range,
        ];
        $report = new FeeOverviewMonthReport();
        $data = $report->export($params);
    }


    /**
     * 获取概览表（月报）数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCpRegionData(Request $request)
    {
        $param = $request->input();
        $report = new CpRegionReport();
        $data = $report->getRegionReportData($param, 'page');

        return response()->json(['result' => $data, 'baseUrl' => env('APP_URL')], 200);
    }

    /**
     * 导出区域销量统计
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $range
     * @return void
     * @throws Exception
     */
    public function exportCpRegion($startMonth, $endMonth, $range)
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'range' => $range,
        ];
        $report = new CpRegionReport();
        $data = $report->export($params);
    }
}
