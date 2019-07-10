import request from '../utils/request'

/**
 * 概览表 - 月报
 *
 * @param startMonth 开始时间
 * @param endMonth 结束时间
 * @param reportType 报表类型
 * @param range 报表范围
 * @returns {*}
 */
export function getOverviewMonthData(startMonth, endMonth, reportType, range) {
  return request({
    url: '/api/overviewmonth',
    method: 'post',
    data: {
      startMonth,
      endMonth,
      reportType,
      range
    }
  })
}

/**
 * 区域销量统计
 *
 * @param startMonth 开始时间
 * @param endMonth 结束时间
 * @param range 报表范围
 * @returns {*}
 */
export function getCpRegionData(startMonth, endMonth, range) {
  return request({
    url: '/api/cpRegion',
    method: 'post',
    data: {
      startMonth,
      endMonth,
      range
    }
  })
}

/**
 * 玩法销量统计
 *
 * @returns {*}
 */
export function getCpGameData(form) {
  return request({
    url: '/api/cpGame',
    method: 'post',
    data: {...form}
  })
}

/**
 * 门店销量统计
 *
 * @returns {*}
 */
export function getCpStoreData(form) {
  return request({
    url: '/api/cpStore',
    method: 'post',
    data: {...form}
  })
}
