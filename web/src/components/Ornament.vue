<template>
  <div class="hello">
    <el-container>
      <el-main>
          <template v-if='images_arr.length !== 0'>
            <el-carousel height='480px' indicator-position='outside'>
              <el-carousel-item v-for="(item, index) in images_arr" :key="index">
                <img class='banner' :src="item.url" alt="#">
              </el-carousel-item>
            </el-carousel>
          </template>
          <template v-if='!images_arr.length'>
            <div style="text-align: center;max-width: 1440px;margin: 40px auto 0 auto;">
              <el-alert
                style='margin-bottom: 40px;'
                title="这个小可怜还莫得不朽预览图"
                type="warning"
                description="或者是我忘记录了"
                center
                show-icon>
              </el-alert>
              <template>
                <div>
                  <span>那棵树看起来生气了</span>
                  <el-divider></el-divider>
                  <span>与其感慨路难行，不如马上出发</span>
                </div>
              </template>
            </div>
          </template>
      </el-main>
    </el-container>
    <error></error>
  </div>
</template>

<script>
import errorSubmit from './errorSubmit';
export default {
  data () {
    return {
      msg: 'Hello Dota',
      images_arr: [],
    }
  }, methods: {
    get_info(){
      this.$axios.get(this.GLOBAL.api_url + '/ornament_detail?ornament_id='+
        this.$route.query.ornament_id).then((res)=>{
          console.log(res.data.data);
          this.images_arr = res.data.data;
        })
    }
  }, created() {
    this.get_info();
  }, components:{
    'error' : errorSubmit
  }
}
</script>
<style scoped> 
  .banner {
    max-height: 100%;
    max-width: 100%;
  }
  .el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
  }
  
  .el-carousel__item:nth-child(2n+1) {
    background-color: #d3dce6;
  }
</style>