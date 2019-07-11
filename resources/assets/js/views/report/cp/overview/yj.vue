<template>
  <div>
    <Card>
      <h2 class="report-title">彩票年 - 佣金分配概览</h2>
      <Row class="filter">
        <Form ref="searchForm" :model="searchForm" inline :label-width="70" class="search-form">
          <Form-item label="报表类型">
            <Select v-model="searchForm.report_type" style="width: 150px" @on-change="switchSearchForm">
              <Option value="month">月报表</Option>
              <Option value="day">日报表</Option>
            </Select>
          </Form-item>
          <span v-if="reportType === 'month'">
            <Form-item label="开始时间" prop="startMonth">
              <DatePicker type="month" v-model="searchForm.startMonth" placeholder="开始时间" style="width: 150px"
                          :editable=false @on-change="startChange"></DatePicker>
            </Form-item>
            <Form-item label="结束时间" prop="endMonth">
              <DatePicker type="month" v-model="searchForm.endMonth" placeholder="结束时间" style="width: 150px"
                          :editable=false @on-change="endChange"></DatePicker>
            </Form-item>
          </span>
          <span v-if="reportType === 'day'">
            <Form-item label="开始时间" prop="startMonth">
              <DatePicker type="date" v-model="searchForm.startMonth" placeholder="开始时间" style="width: 150px"
                          :editable=false
                          @on-change="startChange"></DatePicker>
            </Form-item>
            <Form-item label="结束时间" prop="endMonth">
              <DatePicker type="date" v-model="searchForm.endMonth" placeholder="结束时间" style="width: 150px"
                          :editable=false
                          @on-change="endChange"></DatePicker>
            </Form-item>
          </span>
          <Form-item style="margin-left:-70px;">
            <Button type="primary" @click="filterData" :disabled="disable" icon="ios-search">查询</Button>
          </Form-item>
          <Button class="exportReport" @click="exportData" type="primary" :disabled="btnDisable" icon="md-cloud-upload"
                  style="margin-right: 10px">
            导出报表
          </Button>
        </Form>
      </Row>
      <Table :columns="columns" :loading="loading" :data="data" border class="default" stripe size="small"
             ref="table"></Table>
    </Card>
  </div>
</template>
<script>
  import {getOverviewMonthData} from '../../../../api/report';
  import './table.css';

  export default {
    data() {
      return {
        reportType: 'month',
        searchForm: {
          'report_type': 'month',
          'endMonth': '',
          'startMonth': '',
        },
        btnDisable: true,
        disable: true,
        loading: false,
        baseUrl: '',
        columns: [
          {
            title: '市区',
            key: '0',
            align: 'center',
            width: 120,
            fixed: 'left',
          },
          {
            title: '月体育彩票销量',
            align: 'center',
            children: [
              {
                title: '概率游戏',
                key: 'tc1',
                align: 'right',
                width: 100
              },
              {
                title: '大乐透',
                key: 'tc2',
                align: 'right',
                width: 100
              },
              {
                title: '排三',
                key: 'tc3',
                align: 'right',
                width: 100
              },
              {
                title: '11选5',
                key: 'tc4',
                align: 'right',
                width: 110
              },
              {
                title: '竞彩',
                key: 'tc5',
                align: 'right',
                width: 120
              },
              {
                title: '足彩',
                key: 'tc6',
                align: 'right',
                width: 100
              },
              {
                title: '即开型',
                key: 'tc7',
                align: 'right',
                width: 100
              },
              {
                title: '总销量',
                key: 'tc8',
                align: 'right',
                width: 120
              }
            ]
          },
          {
            title: '月分配彩票佣金',
            align: 'center',
            children: [
              {
                title: '概率游戏',
                key: 'fee0',
                align: 'right',
                width: 100
              },
              {
                title: '大乐透',
                key: 'fee1',
                align: 'right',
                width: 100
              },
              {
                title: '排三',
                key: 'fee2',
                align: 'right',
                width: 100
              },
              {
                title: '11选5',
                key: 'fee3',
                align: 'right',
                width: 110
              },
              {
                title: '竞彩',
                key: 'fee4',
                align: 'right',
                width: 100
              },
              {
                title: '足彩',
                key: 'fee5',
                align: 'right',
                width: 100
              },
              {
                title: '即开型',
                key: 'fee6',
                align: 'right',
                width: 100
              }
            ]
          },
          {
            title: '佣金合计',
            key: 'fee7',
            align: 'right',
            width: 120,
            fixed: 'right'
          }
        ],
        data: [],
        startValue: '',
        endValue: '',
        startArray: [],
        endArray: []
      }
    },
    mounted() {
    },
    methods: {
      startChange(daterange) {
        this.startValue = daterange;
        this.disable = !(this.startValue && this.endValue);
      },
      endChange(daterange) {
        this.endValue = daterange;
        this.disable = !(this.startValue && this.endValue);
      },
      filterData() {
        if (this.reportType === 'month') {
          const startArray = this.startValue.split('-');
          const endArray = this.endValue.split('-');
          if ((endArray[0] === startArray[0] && endArray[1] >= startArray[1])) {
            this.loading = true;
            getOverviewMonthData(this.startValue, this.endValue, 'yj', 'month').then(res => {
              this.columns[1].title = '月体育彩票销量';
              this.columns[2].title = '月分配彩票佣金';
              this.data = res.result;
              this.baseUrl = res.baseUrl;
              this.loading = false;
              this.btnDisable = false;
            }).catch(function () {
              alert("出错了！");
            });
          } else {
            if ((startArray[0] < endArray[0]) || (startArray[0] > endArray[0])) {
              this.$Message.error({
                content: '过滤时间不能跨年，请重新选择！',
                closable: true
              });
            }
            if (endArray[1] < startArray[1]) {
              this.$Message.error({
                content: '开始月份不能大于结束月份，请重新选择！',
                closable: true
              });
            }
            this.btnDisable = true;
          }
        } else {
          const startArray = this.startValue.split('-');
          const endArray = this.endValue.split('-');
          if ((endArray[0] === startArray[0] && endArray[1] === startArray[1] && endArray[2] >= startArray[2]) ||
            (endArray[0] === startArray[0] && endArray[1] > startArray[1])) {
            this.loading = true;
            getOverviewMonthData(this.startValue, this.endValue, 'yj', 'day').then(res => {
              this.columns[1].title = '日体育彩票销量';
              this.columns[2].title = '日分配彩票佣金';
              this.data = res.result;
              this.baseUrl = res.baseUrl;
              this.loading = false;
              this.btnDisable = false;
            }).catch(function () {
              alert("出错了！");
            });
          } else {
            if ((startArray[0] < endArray[0]) || (startArray[0] > endArray[0])) {
              this.$Message.error({
                content: '过滤时间不能跨年，请重新选择！',
                closable: true
              });
            } else if (endArray[1] < startArray[1]) {
              this.$Message.error({
                content: '开始月份不能大于结束月份，请重新选择！',
                closable: true
              });
            } else if (endArray[1] === startArray[1] && endArray[2] < startArray[2]) {
              this.$Message.error({
                content: '开始日期不能大于结束日期，请重新选择！',
                closable: true
              });
            }
            this.btnDisable = true;
          }
        }
      },
      exportData() {
        window.location.href = this.baseUrl + '/api/exportoverviewmonth/' + this.startValue + '/' + this.endValue + '/' + 'yj' + '/' + this.reportType;
      },
      switchSearchForm(e) {
        this.reportType = e;
        this.searchForm.startMonth = this.searchForm.endMonth = this.startValue = this.endValue = '';
        this.disable = this.btnDisable = true;
      }
    }
  }
</script>
