<template>
<!-- 这个有空再封装一哈 -->
  <div class="heroes-list" style='margin: 0 auto;'>
    <el-row :gutter="5" type="flex" justify='left' style="flex-wrap: wrap; padding: 0 5px; margin: 0 auto;">
      <el-col v-for="(index, key) in strength" :key=key :span="1" style='padding-top:10px'>
        <router-link :to="{ path : '/hero', query : { hero_id : index.id }}">
          <div class="grid-content bg-purple" :class="{ unselected: index.unselected }">
          <!-- <div class="grid-content bg-purple" :class='index.selected'> -->
              <el-image
              :class="{ original_img : avatar_type == 1 }"
              :src="avatar_type == 0 ? index.avatar : index.avatar_sm"
              fit="fit">
                <!-- <div slot="placeholder">
                  <img class="el-image" src="http://qiniu.cs704.cn/DOTA2-logo.png" style="width:100%; height:100%" alt="#">
                </div> -->
              </el-image>
          </div>
        </router-link>
      </el-col>
    </el-row>
    <el-row :gutter="5" type="flex" justify='left' style="flex-wrap: wrap; padding: 0 5px; margin: 2% auto;">
      <el-col v-for="(index, key) in agile" :key=key :span="1" style='padding-top:10px'>
        <router-link :to="{ path : '/hero', query : { hero_id : index.id }}">        
          <div class="grid-content bg-purple" 
          :class="{ unselected: index.unselected }">
            <el-image
            :class="{ original_img : avatar_type == 1 }"
            :src="avatar_type == 0 ? index.avatar : index.avatar_sm"
            fit="fit">
              <!-- <div slot="placeholder">
                <img class="el-image" src="http://qiniu.cs704.cn/DOTA2-logo.png" style="width:100%; height:100%" alt="#">
              </div> -->
            </el-image>
          </div>
        </router-link>
      </el-col>
    </el-row>
    <el-row :gutter="5" type="flex" justify='left' style="flex-wrap: wrap; padding: 0 5px; margin: 0 auto;">
      <el-col v-for="(index, key) in intelligent" :key=key :span="1" style='padding-top:10px'>
        <router-link :to="{ path : '/hero', query : { hero_id : index.id }}">
          <div class="grid-content bg-purple" :class="{ unselected: index.unselected }">
            <el-image
            :class="{ original_img : avatar_type == 1 }"
            :src="avatar_type == 0 ? index.avatar : index.avatar_sm"
            fit="fit">
              <!-- <div slot="placeholder">
                <img class="el-image" src="http://qiniu.cs704.cn/DOTA2-logo.png" style="width:100%; height:100%" alt="#">
              </div> -->
            </el-image>
          </div>
        </router-link>
      </el-col>
    </el-row>
    <br>
    <el-button-group>
      <el-button plain :class="{ 'el-button--primary' : avatar_type == 0 }" @click='avatar_type = 0'>Q版头像</el-button>
      <el-button plain :class="{ 'el-button--primary' : avatar_type == 1 }" @click='avatar_type = 1'>原版头像</el-button>
    </el-button-group>
  </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex';
export default {
  name: 'HeroesList',
  data () {
    return {
      // strength:[]
      // intelligent:[],
      // agile:[],
      bollen:true,
      selectedClass:'selected',
      unselectedClass:'unselected',
      avatar_type: 0, // 头像类型 0 Q版 1原版
      
    }
  }, methods: {
    // heroes_info(){
    //   this.$axios.get(this.GLOBAL.api_url + '/heroes_info').then((res)=>{
    //     // console.log(res.data.data);
    //     this.agile       = res.data.data.data[1];
    //     this.strength    = res.data.data.data[0];
    //     this.intelligent = res.data.data.data[2];
    //     this.$store.dispatch('heroStatus/updateAgile',       this.agile);
    //     this.$store.dispatch('heroStatus/updateStrength',    this.strength);
    //     this.$store.dispatch('heroStatus/updateIntelligent', this.intelligent);
    //   })
    // }
  }, created: function() {
  },
  computed:{
    // 两种获取方式,一更友善点
    ...mapGetters('heroStatus',{ //footerStatus指的是modules文件夹下的footerStatus.js模块
      agile: 'getAgile',
      strength: 'getStrength',
      intelligent: 'getIntelligent'
    }),
    // ...mapState({  //这里的...是超引用，ES6的语法，意思是state里有多少属性值我可以在这里放多少属性值
    //   isShow:state=>state.ornamentStatus.testNum //注意这些与上面的区别就是state.footerStatus,
    // }),
  }
}
</script>
<style>
  .grid-content.bg-purple{
    width: 4vw;
    height: 4vw;
  }
  .el-row {
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .el-col {
    border-radius: 4px;
  }
  .bg-purple-dark {
    background: #99a9bf;
  }
  /* .bg-purple {
    background: #d3dce6;
  } */
  /* .bg-purple-light {
    background: #e5e9f2;
  } */
  .grid-content {
    border-radius: 4px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }
  .grid-content img{
    cursor: pointer;
    transition: all 0.3s;
  }
  .grid-content img:hover{
    transform: scale(1.4);
  }
  .heroes-list .el-image:hover{
    overflow:visible;
    z-index: 233;
  }
  .unselected{
    opacity: 0.3;
  }
  .default_img{
    margin-top:25%;
  }
  .original_img{
    padding-top: 1vw;
  }

</style>