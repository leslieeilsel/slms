<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CpRegionReport;
use App\Repositories\CpGameReport;
use App\Repositories\CpStoreReport;
use App\Repositories\ZrRegionReport;
use App\Repositories\ZrGameReport;
use App\Repositories\ZrStoreReport;
use App\Repositories\ZjRegionReport;
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

        return response()->json(['result' => $data], 200);
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
     * 彩票年区域销量数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCpRegionData(Request $request)
    {
        $param = $request->input();
        $report = new CpRegionReport();
        $data = $report->getRegionReportData($param, 'page');

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出彩票年区域销量统计
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

    /**
     * 彩票年玩法销量数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCpGameData(Request $request)
    {
        $param = $request->input();
        $report = new CpGameReport();
        $data = $report->getGameReportData($param, 'page');

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出彩票年玩法销量统计
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $range
     * @param number $gameType
     * @param string $gameName
     * @return void
     * @throws Exception
     */
    public function exportCpGame($startMonth, $endMonth, $range, $gameType, $gameName = '')
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'range' => $range,
            'gameType' => $gameType,
            'gameName' => $gameName,
        ];
        $report = new CpGameReport();
        $data = $report->export($params);
    }

    /**
     * 彩票年门店销量数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCpStoreData(Request $request)
    {
        $param = $request->input();
        $report = new CpStoreReport();
        $data = $report->getStoreReportData($param, 'page');

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出彩票年玩法销量统计
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $range
     * @param number $gameType
     * @param string $gameName
     * @return void
     * @throws Exception
     */
    public function exportCpStore($startMonth, $endMonth, $range, $gameType, $gameName = '')
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'range' => $range,
            'gameType' => $gameType,
            'gameName' => $gameName,
        ];
        $report = new CpStoreReport();
        $data = $report->export($params);
    }

    /**
     * 彩票年区域销量数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getZrRegionData(Request $request)
    {
        $param = $request->input();
        $report = new ZrRegionReport();
        $data = $report->getRegionReportData($param, 'page');

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出彩票年区域销量统计
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $range
     * @return void
     * @throws Exception
     */
    public function exportZrRegion($startMonth, $endMonth, $range)
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'range' => $range,
        ];
        $report = new ZrRegionReport();
        $data = $report->export($params);
    }

    /**
     * 彩票年玩法销量数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getZrGameData(Request $request)
    {
        $param = $request->input();
        $report = new ZrGameReport();
        $data = $report->getGameReportData($param, 'page');

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出彩票年玩法销量统计
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $range
     * @param number $gameType
     * @param string $gameName
     * @return void
     * @throws Exception
     */
    public function exportZrGame($startMonth, $endMonth, $range, $gameType, $gameName = '')
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'range' => $range,
            'gameType' => $gameType,
            'gameName' => $gameName,
        ];
        $report = new ZrGameReport();
        $data = $report->export($params);
    }

    /**
     * 彩票年门店销量数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getZrStoreData(Request $request)
    {
        $param = $request->input();
        $report = new ZrStoreReport();
        $data = $report->getStoreReportData($param, 'page');

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出彩票年玩法销量统计
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $range
     * @param number $gameType
     * @param string $gameName
     * @return void
     * @throws Exception
     */
    public function exportZrStore($startMonth, $endMonth, $range, $gameType, $gameName = '')
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'range' => $range,
            'gameType' => $gameType,
            'gameName' => $gameName,
        ];
        $report = new ZrStoreReport();
        $data = $report->export($params);
    }

    /**
     * 彩票年区域销量数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getZjRegionData(Request $request)
    {
        $param = $request->input();
        $report = new ZjRegionReport();
        $data = $report->getRegionReportData($param, 'page');

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出彩票年区域销量统计
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $range
     * @return void
     * @throws Exception
     */
    public function exportZjRegion($startMonth, $endMonth, $range)
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'range' => $range,
        ];
        $report = new ZjRegionReport();
        $data = $report->export($params);
    }
}
