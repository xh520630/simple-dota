<template>
  <div class="hello">
    <el-container>
      <el-main>
          <template>
            <el-carousel height='400px' indicator-position='outside'>
              <el-carousel-item v-for="(item, index) in images_arr" :key="index">
                <img class='banner' :src="item.url" alt="#">
              </el-carousel-item>
            </el-carousel>
          </template>
      </el-main>
    </el-container>
  </div>
</template>

<script>
export default {
  data () {
    return {
      msg: 'Hello Dota',
      images_arr: [],
    }
  }
  ,methods:{
    get_info(){
      this.$axios.get(this.GLOBAL.api_url + '/ornament_detail?ornament_id='+
        this.$route.query.ornament_id).then((res)=>{
          console.log(res.data.data);
          this.images_arr = res.data.data;
        })
    }
  }
  ,created(){
    this.get_info();
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