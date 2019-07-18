<template>
  <div class="addOrnament" style="margin-top: 20px">
    <el-row>
      <el-col :span="8"><div class="grid-content bg-purple"></div></el-col>
      <el-col :span="8">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>添加饰品</span>
            <router-link :to="{ path: '/'}">
              <el-button style="float: right; padding: 3px 0" type="text">回到首页</el-button>
            </router-link>
          </div>
          这里
        </el-card>
      </el-col>
      <el-col :span="8"><div class="grid-content bg-purple"></div></el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  data () {
    return {
      msg: 'Hello Dota',
    }
  }
  ,methods:{

  }
  ,created(){
  }
}
</script>
<style>
  .el-row {
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .el-col {
    border-radius: 4px;
  }
  .grid-content {
    border-radius: 4px;
    min-height: 36px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }
</style>