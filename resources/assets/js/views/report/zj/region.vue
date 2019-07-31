<template>
  <Card class="content-card">
    <h2 class="report-title">区域中奖统计</h2>
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
    <Table stripe border size="small" :columns="columns" :loading="tableLoading"
           :data="data"></Table>
  </Card>
</template>
<script>
  import {getZjRegionData} from '../../../api/report';

  export default {
    data() {
      return {
        reportType: 'month',
        searchForm: {
          report_type: 'month',
          endMonth: '',
          startMonth: '',
          region: '-1',
          store: '-1',
        },
        btnDisable: true,
        disable: true,
        tableLoading: false,
        host: window.document.location.host,
        columns: [
          {
            title: '地市',
            key: 'region_name',
            width: 120,
            fixed: 'left',
            align: 'center',
          },
          {
            title: '热线中奖',
            key: 'rx',
            align: 'right',
          },
          {
            title: '高频中奖',
            key: 'gp',
            align: 'right',
          },
          {
            title: '竞彩中奖',
            key: 'jc',
            align: 'right',
          },
          {
            title: '即开中奖',
            key: 'jk',
            align: 'right',
          },
          {
            title: '中奖合计',
            key: 'total',
            align: 'right',
            fixed: 'right',
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
      switchSearchForm(e) {
        this.reportType = e;
        this.searchForm.startMonth = this.searchForm.endMonth = this.startValue = this.endValue = '';
        this.disable = this.btnDisable = true;
      },
      exportData() {
        window.location.href = 'http://' + this.host + '/api/exportZjRegion/' + this.startValue + '/' + this.endValue + '/' + this.reportType;
      },
      filterData() {
        if (this.reportType === 'month') {
          const startArray = this.startValue.split('-');
          const endArray = this.endValue.split('-');
          if ((endArray[0] === startArray[0] && endArray[1] >= startArray[1])) {
            this.tableLoading = true;
            getZjRegionData({
              startMonth: this.startValue,
              endMonth: this.endValue,
              range: 'month'
            }).then(res => {
              this.data = res.result;
              this.tableLoading = false;
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
            this.tableLoading = true;
            getZjRegionData({
              startMonth: this.startValue,
              endMonth: this.endValue,
              range: 'day'
            }).then(res => {
              this.data = res.result;
              this.tableLoading = false;
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