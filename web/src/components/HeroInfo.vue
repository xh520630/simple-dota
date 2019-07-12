<template>
    <div class='hero_info'>
      <el-row type="flex" justify='center'>
        <el-col :span="4" v-for="(o, index) in 2" :key="o" :offset="index > 0 ? 2 : 0">
          <el-card :body-style="{ padding: '0px' }">
            <img src="https://shadow.elemecdn.com/app/element/hamburger.9cf7b091-55e9-11e9-a976-7f4d0b07eef6.png" class="image">
            <div style="padding: 14px;">
              <span>好吃的汉堡</span>
              <div class="bottom clearfix">
                <el-button type="text" class="button">操作按钮</el-button>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
</template>

<script>
export default {
  data () {
    return {
      msg: 'Welcome to the DOTA WORLD'
    }
  }
  ,methods:{
    getInfo(){
      this.$axios.get(this.GLOBAL.api_url + '/hero_detail?hero_id=' 
        + this.$route.query.hero_id).then((res)=>{
          console.log(res.data.data);    
        })
    }
  },created(){
    this.getInfo();
  }
}
</script>
<style scope>
  .hero_info{
    margin: 15px auto;
  }
  .time {
    font-size: 13px;
    color: #999;
  }
  
  .bottom {
    margin-top: 13px;
    line-height: 12px;
  }

  .button {
    padding: 0;
    float: right;
  }

  .image {
    width: 100%;
    display: block;
  }

  .clearfix:before,
  .clearfix:after {
      display: table;
      content: "";
  }
  
  .clearfix:after {
      clear: both
  }
</style>