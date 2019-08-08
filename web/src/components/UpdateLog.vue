<template>
  <div class="update-log">
    <el-alert
      title="介个页面并没有实际作用，仅用来鞭策我寄几。"
      center
      type=""
      style="margin:20px auto;">
    </el-alert>
    <div style="margin-bottom:20px;text-align:center;">
      <el-tag type="success">已经做完了</el-tag>
      <el-tag type="warning">还在努力中</el-tag>
      <el-tag type="danger">准备开始努力</el-tag>
    </div>
    <el-collapse accordion>
      <el-collapse-item v-for="(item, index) in updateArr" :key='index'>
        <template slot="title" style='position:relative'>
          <el-tag 
          v-if="item.is_important > 0"
          effect="plain" 
          size="mini" 
          style='margin-left:20px;'>置顶</el-tag>
          <span style='font-size:14px'
          :style="item.is_important > 0 ? 'margin-left:5px;' : 'margin-left:20px;'">{{ item.title }}</span>
          <p style="font-size:12px; color:#999;position:absolute;right: 40px">{{ item.create_time | timeFilter }}</p>
        </template>
        <div v-for="(detail, id) in item.detail" :key='id'>
          <el-alert
            :title="detail.content"
            :type="typeArr[detail.type]"
            show-icon>
          </el-alert>
        </div>
      </el-collapse-item>
    </el-collapse>
    <div style="text-align: center;margin-top:20px;">
        <el-link type="primary" href="https://weibo.com/u/2386733455" target="_blank">联系作者</el-link>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      updateArr: [],
      typeArr: ['success', 'warning', 'error'],

    }
  },
  methods: {
    getUpdateLog(){
      this.$axios.get(this.GLOBAL.api_url + '/update_log').then((res)=>{
        console.log(res.data.data);
        if (res.data.code == 200)
          this.updateArr = res.data.data;
      });
    }
  },  
  created() {
    this.getUpdateLog();
  },
  watch:{

  },
  filters:{
    timeFilter(val){
      return val.substr(0, 10);
    }
  }
}
</script>
<style>
  .update-log{
    max-width: 1000px;
    margin: 20px auto;
  }
  .el-collapse-item__content>div{
    margin-left:40px;
  }
  .el-collapse-item__header{
    position:relative;
  }
</style>