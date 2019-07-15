<template>
  <Card class="content-card">
    <h2 class="report-title">自然年 - 投注站销量统计</h2>
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
        <Form-item label="归属地市" prop="region">
          <Select v-model="searchForm.region" style="width:150px">
            <Option value="-1">全部</Option>
            <Option value="6101">西安</Option>
            <Option value="6106">杨凌</Option>
            <Option value="6107">咸阳</Option>
            <Option value="6110">渭南</Option>
            <Option value="6113">宝鸡</Option>
            <Option value="6116">铜川</Option>
            <Option value="6117">商洛</Option>
            <Option value="6119">安康</Option>
            <Option value="6121">汉中</Option>
            <Option value="6124">延安</Option>
            <Option value="6127">榆林</Option>
            <Option value="6130">韩城</Option>
          </Select>
        </Form-item>
        <!--        <Form-item label="投注站" prop="store">-->
        <!--          <Select v-model="searchForm.store" style="width:150px">-->
        <!--            <Option value="-1">全部</Option>-->
        <!--          </Select>-->
        <!--        </Form-item>-->
        <Form-item style="margin-left:-70px;">
          <Button type="primary" @click="filterData" :disabled="disable" icon="ios-search">查询</Button>
        </Form-item>
        <Button class="exportReport" @click="exportData" type="primary" :disabled="btnDisable" icon="md-cloud-upload"
                style="margin-right: 10px">
          导出报表
        </Button>
      </Form>
    </Row>
    <Table :height="tableHeight" stripe border size="small" :columns="columns" :loading="tableLoading" :data="data"></Table>
  </Card>
</template>
<script>
  import {getZrStoreData} from '../../../api/report';

  export default {
    data() {
      return {
        tableHeight: 0,
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
            title: '投注站',
            key: 'store_num',
            width: 120,
            fixed: 'left',
            align: 'center',
          },
          {
            title: '概率游戏',
            key: 'sale_gl',
            align: 'right',
          },
          {
            title: '大乐透',
            key: 'sale_dlt',
            align: 'right',
          },
          {
            title: '排三',
            key: 'sale_pl',
            align: 'right',
          },
          {
            title: '11选5',
            key: 'sale_xuan',
            align: 'right',
          },
          {
            title: '竞彩',
            key: 'sale_jc',
            align: 'right',
          },
          {
            title: '足彩',
            key: 'sale_zc',
            align: 'right',
          },
          {
            title: '总销量',
            key: 'sale_total',
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
      this.tableHeight = this.$parent.$el.clientHeight - 175 - this.$el.childNodes[4].childNodes[2].clientHeight;
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
        window.location.href = 'http://' + this.host + '/api/exportZrStore/' + this.startValue + '/' + this.endValue + '/' + this.reportType + '/' + this.searchForm.region + '/' + this.searchForm.store;
      },
      filterData() {
        if (this.reportType === 'month') {
          const startArray = this.startValue.split('-');
          const endArray = this.endValue.split('-');
          if ((endArray[0] === startArray[0] && endArray[1] >= startArray[1])) {
            this.tableLoading = true;
            getZrStoreData({
              startMonth: this.startValue,
              endMonth: this.endValue,
              range: 'month',
              region: this.searchForm.region,
              store: this.searchForm.store
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
            getZrStoreData({
              startMonth: this.startValue,
              endMonth: this.endValue,
              range: 'day',
              region: this.searchForm.region,
              store: this.searchForm.store
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