<?php

namespace App\Http\Controllers\DataV;

use App\Http\Controllers\Controller;
use App\Models\Dict;
use App\Models\Project\Projects;

class ProjectController extends Controller
{

    public function getDataVProject()
    {
        $projectData = $this->getAllProjects();

        $projectArr = [];
        $dataArr = [];
        foreach ($projectData as $k => $v) {
            $aaa = [];
            $aaa['type'] = 'Feature';

            $positions = $v['positions'];
            if ($v['positions'] && $v['center_point']) {
                $positions = json_decode($positions, true);
                foreach ($positions as $kk => $vv) {
                    if ($vv['drawingMode'] === 'polyline') {
                        $vv['coordinates'] = array_merge($vv['coordinates'], array_reverse($vv['coordinates']));
                    }
                    $points = '';
                    foreach ($vv['coordinates'] as $kkk => $vvv) {
                        $points .= $vvv['lng'] . ',' . $vvv['lat'] . ';';
                    }
                    $points = $this->getGaoDeGeo($points);
                    $pointsArr = explode(';', $points);
                    $pointsItem = [];
                    foreach ($pointsArr as $kkkk => $vvvv) {
                        $pointArr = explode(',', $vvvv);
                        $point = [
                            (float) $pointArr[0],
                            (float) $pointArr[1],
                        ];
                        $pointsItem[] = $point;
                    }
                    $aaa['geometry'] = [
                        'type' => 'Polygon',
                        'coordinates' => [$pointsItem],
                    ];

                    $properties = [];
                    $properties['name'] = $v['title'];
                    $properties['adcode'] = $v['id'];
                    $properties['level'] = 'district';
                    $center = json_decode($v['center_point'], true);
                    $center = $this->getGaoDeGeo($center['coordinates']['lng'] . ',' . $center['coordinates']['lat']);
                    $centerPoint = explode(',', $center);
                    $properties['center'] = [
                        'lng' => (float) $centerPoint[0],
                        'lat' => (float) $centerPoint[1],
                    ];

                    $aaa['properties'] = $properties;
                }
//                // 数据
//                $data['area_id'] = $v['id'];
//                $data['value'] = 64;
//                $data['info'] =
//                    '项目名称：' . $v['title'] . '<br/>' .
//                    '项目类型：' . $v['type'] . '<br/>' .
//                    '投资状态：' . $v['status'] . '<br/>' .
//                    '投资概况：' . $v['description'];
//
                $projectArr[] = $aaa;
//                $dataArr[] = $data;
            }
        }

        $projectJson = json_encode(['type' => 'FeatureCollection', 'features' => $projectArr]);
//        $dataJson = json_encode($dataArr);

        return $projectJson;
    }

    public function getAllProjects()
    {
        $projects = Projects::whereIn('is_audit', [1, 3])->get()->toArray();

        $type = Dict::getOptionsArrByName('工程类项目分类');
        $is_gc = Dict::getOptionsArrByName('是否为国民经济计划');
        $status = Dict::getOptionsArrByName('项目状态');
        $money_from = Dict::getOptionsArrByName('资金来源');
        $build_type = Dict::getOptionsArrByName('建设性质');
        $nep_type = Dict::getOptionsArrByName('国民经济计划分类');

        foreach ($projects as $k => $row) {
            $projects[$k]['amount'] = number_format($row['amount'], 2);
            $projects[$k]['land_amount'] = isset($row['land_amount']) ? number_format($row['land_amount'], 2) : '';
            $projects[$k]['type'] = $type[$row['type']];
            $projects[$k]['is_gc'] = $is_gc[$row['is_gc']];
            $projects[$k]['status'] = $status[$row['status']];
            $projects[$k]['money_from'] = $money_from[$row['money_from']];
            $projects[$k]['build_type'] = $build_type[$row['build_type']];
            $projects[$k]['nep_type'] = isset($row['nep_type']) ? $nep_type[$row['nep_type']] : '';
        }

        return $projects;
    }

    public function getGaoDeGeo($point)
    {
        $res = file_get_contents('https://restapi.amap.com/v3/assistant/coordinate/convert?locations=' . $point . '&coordsys=baidu&output=json&key=86a30535207aa2d5fc6d2aec25c26b12');
        $res = json_decode($res);

        return $res->locations;
    }
}