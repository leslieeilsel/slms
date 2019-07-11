<template>
  <Card class="content-card">
    <h2 class="report-title">自然年 - 区域销量统计</h2>
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
</template>
<script>
  import {getZrRegionData} from '../../../api/report';

  export default {
    data() {
      return {
        reportType: 'month',
        searchForm: {
          report_type: 'month',
          endMonth: '',
          startMonth: '',
        },
        btnDisable: true,
        disable: true,
        loading: false,
        host: window.document.location.host,
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
              },
              {
                title: '大乐透',
                key: 'tc2',
                align: 'right',
              },
              {
                title: '排三',
                key: 'tc3',
                align: 'right',
              },
              {
                title: '11选5',
                key: 'tc4',
                align: 'right',
              },
              {
                title: '竞彩',
                key: 'tc5',
                align: 'right',
              },
              {
                title: '足彩',
                key: 'tc6',
                align: 'right',
              },
              {
                title: '总销量',
                key: 'tc7',
                align: 'right',
              }
            ]
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
            getZrRegionData(this.startValue, this.endValue, 'month').then(res => {
              this.columns[1].title = '月体育彩票销量';
              this.data = res.result;
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
            getZrRegionData(this.startValue, this.endValue, 'day').then(res => {
              this.columns[1].title = '日体育彩票销量';
              this.data = res.result;
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
        window.location.href = 'http://' + this.host + '/api/exportZrRegion/' + this.startValue + '/' + this.endValue + '/' + this.reportType;
      },
      switchSearchForm(e) {
        this.reportType = e;
        this.searchForm.startMonth = this.searchForm.endMonth = this.startValue = this.endValue = '';
        this.disable = this.btnDisable = true;
      }
    }
  }
</script>
<style scoped src="./table.css"></style>
<style>
  .content-card .ivu-card-body {
    padding: 0 16px 16px;
  }
  
  .ivu-table-cell {
    padding-left: 5px !important;
    padding-right: 5px !important;
  }
  
  .ivu-table-small td {
    height: 25px !important;
  }
  
  .ivu-table-small th {
    height: 25px !important;
    text-align: center !important;
  }
  
  .ivu-table td,
  .ivu-table th {
    border-bottom: 1px solid #ddd !important;
  }
  
  .ivu-table-border td,
  .ivu-table-border th {
    border-right: 1px solid #ddd !important;
  }
</style>
