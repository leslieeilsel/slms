<template>
  <Card class="content-card">
    <h2 class="report-title">自然年 - 玩法销量统计</h2>
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
        <Form-item label="游戏类型" prop="gameType">
          <Select v-model="searchForm.gameType" style="width:150px">
            <Option value="-1">全部</Option>
            <Option value="1">电彩</Option>
            <Option value="2">竞彩</Option>
          </Select>
        </Form-item>
        <Form-item label="游戏名称" prop="gameName">
          <Input v-model="searchForm.gameName" placeholder="支持迷糊搜索" style="width:150px"></Input>
        </Form-item>
        <Form-item style="margin-left:-70px;">
          <Button type="primary" @click="filterData" :disabled="disable" icon="ios-search">查询</Button>
        </Form-item>
        <Button class="exportReport" @click="exportData" type="primary" :disabled="btnDisable" icon="md-cloud-upload"
                style="margin-right: 10px">
          导出报表
        </Button>
      </Form>
    </Row>
    <Table stripe border size="small" :columns="columns" :loading="tableLoading" :data="data"></Table>
  </Card>
</template>
<script>
  import {getZrGameData} from '../../../api/report';

  export default {
    data() {
      return {
        reportType: 'month',
        searchForm: {
          report_type: 'month',
          endMonth: '',
          startMonth: '',
          gameType: '-1',
          gameName: '',
        },
        btnDisable: true,
        disable: true,
        tableLoading: false,
        host: window.document.location.host,
        columns: [
          {
            title: '序号',
            key: 'num',
            width: 70,
            fixed: 'left',
            align: 'center',
          },
          {
            title: '游戏名称',
            key: 'game_name',
            width: 150,
            fixed: 'left',
            align: 'left',
          },
          {
            title: '西安',
            key: 'xian',
            align: 'right',
            width: 110,
          },
          {
            title: '杨凌',
            key: 'yangling',
            align: 'right',
            width: 100,
          },
          {
            title: '咸阳',
            key: 'xianyang',
            align: 'right',
            width: 100,
          },
          {
            title: '渭南',
            key: 'weinan',
            align: 'right',
            width: 100,
          },
          {
            title: '宝鸡',
            key: 'baoji',
            align: 'right',
            width: 100,
          },
          {
            title: '铜川',
            key: 'tongchuan',
            align: 'right',
            width: 100,
          },
          {
            title: '商洛',
            key: 'shangluo',
            align: 'right',
            width: 100,
          },
          {
            title: '安康',
            key: 'ankang',
            align: 'right',
            width: 100,
          },
          {
            title: '汉中',
            key: 'hanzhong',
            align: 'right',
            width: 100,
          },
          {
            title: '延安',
            key: 'yanan',
            align: 'right',
            width: 100,
          },
          {
            title: '榆林',
            key: 'yulin',
            align: 'right',
            width: 100,
          },
          {
            title: '韩城',
            key: 'hancheng',
            align: 'right',
            width: 100,
          },
          {
            title: '合计',
            key: 'game_total',
            align: 'right',
            fixed: 'right',
            width: 120,
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
        window.location.href = 'http://' + this.host + '/api/exportZrGame/' + this.startValue + '/' + this.endValue + '/' + this.reportType + '/' + this.searchForm.gameType + '/' + this.searchForm.gameName;
      },
      filterData() {
        if (this.reportType === 'month') {
          const startArray = this.startValue.split('-');
          const endArray = this.endValue.split('-');
          if ((endArray[0] === startArray[0] && endArray[1] >= startArray[1])) {
            this.tableLoading = true;
            getZrGameData({
              startMonth: this.startValue,
              endMonth: this.endValue,
              range: 'month',
              gameType: this.searchForm.gameType,
              gameName: this.searchForm.gameName
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
            getZrGameData({
              startMonth: this.startValue,
              endMonth: this.endValue,
              range: 'day',
              gameType: this.searchForm.gameType,
              gameName: this.searchForm.gameName
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