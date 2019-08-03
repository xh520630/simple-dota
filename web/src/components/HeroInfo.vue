<template>
    <!-- <div class='hero_info' style="margin:0 auto; width: 1440px;"> -->
    <div class='hero_info' style="margin:0 auto; max-width: 1080px;">
      <el-row :gutter="40" justify="left" style="flex-wrap: wrap">
        <el-col :span="6" v-for="(item, index) in ornament"  :key="index" style='margin-top:40px'>
          <el-card :body-style="{ padding: '0px' }">
            <router-link :to="{ path : '/ornament' , query : { 'ornament_id' : item.id }}">
              <div style="overflow:hidden;"><img :src="item.avatar" class="image"></div>
              <div style="padding: 14px;">
                <span style="color: black">{{ item.name }}</span>
                <div class="hidden-sm-and-down bottom clearfix" style='height: 30px; line-height: 30px'>
                  <el-button type="text" class="button">稀有度</el-button>
                  <el-tooltip class="item" effect="dark" :content="tips[item.value]" placement="bottom">
                  <el-rate
                    style='display: inline-block;line-height:10px'
                    v-model="item.value"
                    disabled
                    show-score
                    text-color="#ff9900"
                    score-template="">
                  </el-rate>
                  </el-tooltip>
                </div>
              </div>
            </router-link>
          </el-card>
        </el-col>
      </el-row>
      <div style="margin-top:40px;text-align: center;" v-if='!ornament.length'>
        <el-alert
          style='margin-bottom: 40px;'
          title="这个小可怜还莫得不朽"
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
      <error/>
    </div>
</template>

<script>
import 'element-ui/lib/theme-chalk/display.css';
import errorSubmit from './errorSubmit';
import { mapState, mapGetters, mapActions } from 'vuex';
export default {
  data () {
    return {
      msg: 'Welcome to the DOTA WORLD'
      ,ornament:[]
      ,hero_id: this.$route.query.hero_id ? this.$route.query.hero_id : this.$route.params.hero_id
      ,tips:[
        '不存在的', '很便宜', '一顿饭钱', '一个至宝的价格', '反正我买不起', '正常人都买不起'
      ]
    }
  }, methods: {
    getInfo(){
      this.$axios.get(this.GLOBAL.api_url + '/hero_detail?hero_id=' 
        + this.hero_id).then((res)=>{
          console.log(res.data.data);  
          this.ornament = res.data.data;  
        })
    }
  }, created() {
    this.getInfo();
    if(this.$route.query) 
      this.hero_id = this.$route.query.id;
    console.log(this.$router.currentRoute.path);
  }, watch: {
    $route(to,from){
      this.hero_id = to.query.hero_id;
      return this.getInfo();
    }
  }, components:{
    'error' : errorSubmit
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
    object-fit: fill;
    max-height: 238px;
    height: 22vw;
    width: 100%;
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
  .el-tooltip__popper,.is-dark{
    font-size: 14px;
  }
</style>