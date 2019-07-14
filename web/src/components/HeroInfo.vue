<template>
    <!-- <div class='hero_info' style="margin:0 auto; width: 1440px;"> -->
    <div class='hero_info' style="margin:0 auto; width: 1080px;">
      <el-row :gutter="40" justify="left" style="flex-wrap: wrap">
        <el-col :span="6" v-for="(item, index) in ornament"  :key="index" style='margin-top:40px'>
          <el-card :body-style="{ padding: '0px' }">
            <router-link :to="{ path : '/ornament' , query : { 'ornament_id' : item.id }}">
              <div style="overflow:hidden;"><img :src="item.avatar" class="image"></div>
              <div style="padding: 14px;">
                <span style="color: black">{{ item.name }}</span>
                <div class="bottom clearfix" style='height: 30px; line-height: 30px'>
                  <el-button type="text" class="button">稀有度</el-button>
                  <el-rate
                    style='display: inline-block;line-height:10px'
                    v-model="item.value"
                    disabled
                    show-score
                    text-color="#ff9900"
                    score-template="">
                  </el-rate>
                </div>
              </div>
            </router-link>
          </el-card>
        </el-col>
      </el-row>
    </div>
</template>

<script>
import {mapState,mapGetters,mapActions} from 'vuex';
export default {
  data () {
    return {
      msg: 'Welcome to the DOTA WORLD'
      ,ornament:[]
      ,
    }
  }
  ,methods:{
    getInfo(){
      this.$axios.get(this.GLOBAL.api_url + '/hero_detail?hero_id=' 
        + this.$route.query.hero_id).then((res)=>{
          console.log(res.data.data);  
          this.ornament = res.data.data;  
        })
    }
  },created(){
    this.getInfo();
  }, computed:{

  }
}
</script>
<style scope>
  .hero_info{
    margin: 15px auto;
  }
  .button {
    padding: 0;
  }
  .image {
    max-width: 100%;
    display: block;
    cursor: pointer;
    transition: all 0.6s;
  }

  .clearfix:before,
  .clearfix:after {
      display: table;
      content: "";
  }
  
  .clearfix:after {
      clear: both
  }
  .image:hover{
    transform: scale(1.2)
  }
</style>