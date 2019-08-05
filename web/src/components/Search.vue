<template>
  <el-header>
    <el-row type="flex" justify="center">
      <el-col :span="6">
        <el-button plain  @click="$router.back(-1)" v-if="!isMain">
          <i class="el-icon-arrow-left"></i>
        </el-button>
        <!-- <router-link :to="{ path: 'main'}" v-if='showMessageBoard'>
          <el-button plain v-if="!isMain">
            <i class="el-icon-arrow-left"></i>
          </el-button>
        </router-link> -->
        <el-button plain  v-if="isMain">
          <i class="el-icon-s-home"></i>
        </el-button>
        <router-link  class="hidden-xs-only" :to="{ path: '/message_board'}" v-if='showMessageBoard'>
          <el-button plain style="margin-left: 15px;">
          留言板
          </el-button>
        </router-link>
        <router-link  class="hidden-md-and-down" :to="{ path: '/update_log'}" v-if='showMessageBoard'>
          <el-button plain style="margin-left: 15px;">
          更新日志
          </el-button>
        </router-link>
      </el-col>      
      <el-col :span="6" style="text-align:center;">
        
      </el-col>
      <el-col :span="6">
        <el-autocomplete
          style='max-width:250px; float:right'
          placeholder='试着输入"JB"什么的'
          :fetch-suggestions="querySearch"
          :trigger-on-focus="false"
          suffix-icon="el-icon-search"
          @select="selectHero"
          v-model="heroName">
        </el-autocomplete>
      </el-col>
    </el-row>
  </el-header>
</template>

<script>
import 'element-ui/lib/theme-chalk/display.css';
import { mapState, mapGetters, mapActions } from 'vuex';
export default {
  data () {
    return {
      heroName: '',
      list:[1234,45,456,342],
      isMain: false,
      showMessageBoard: true,
    }
  }
  ,methods:{
    searchHero(list, keyword){
      var reg =  new RegExp(keyword.toUpperCase());
      var arr = [];
      for (let j = 0; j < list.length; j++)
        // if (reg.test(list[j]['name']) || reg.test(list[j]['cname']))
          // arr.push(list[j]);
          this.$set(list[j], 'unselected', (reg.test(list[j]['name']) 
            || reg.test(list[j]['cname'])) ? false : true);
          // list[j]['selected'] = (reg.test(list[j]['name']) || reg.test(list[j]['cname'])) ? 'true' : 'false';
      // arr.forEach((current)=>{
      //     console.log(current);
      //     if (list.indexOf(current))
      //       console.log(list.indexOf(current));
      // });
    },
    selectHero(item){
      // 这个最好
      this.$router.push({ path: '/hero', query: { hero_id: item.id }});
      // 下面这个name需要与router.config内的name对应...
      // this.$router.push({ name : 'Hero', params:{ hero_id : item.id }});
      // 太粗暴了直接替换路由
      // this.$router.replace({path:'/hero/',query:{hero_id:item.id}});
    }, 
    querySearch(queryString, cb) {
      var restaurants = this.strength.concat(this.intelligent, this.agile);
      
      // 批量替换key名
      var restaurants = restaurants.map(o=>{return{id:o.id, value:o.name, cname:o.cname}});
      var results = queryString ? restaurants.filter(this.createFilter(queryString, restaurants)) : restaurants;
      // 调用 callback 返回建议列表的数据

      cb(results);
    }, // 输入后搜索
    createFilter(queryString, restaurants) {
      return (restaurants) => {
        return (restaurants.value.indexOf(queryString.toUpperCase()) != -1 ||
          restaurants.cname.indexOf(queryString.toUpperCase()) != -1 );
      };
    }, // 创建搜索菜单
    changeIcon(){
      if (this.$route.path == '/')
        this.isMain = true;
        else this.isMain = false;
      if (this.$route.path == '/message_board')
        this.showMessageBoard = false;
        else this.showMessageBoard = true;
      this.heroName = '';
    },
  }
  ,computed:{
    ...mapGetters('heroStatus',{
      strength: 'getStrength',
      intelligent: 'getIntelligent',
      agile: 'getAgile',
    }),
  }
  ,created(){
    this.changeIcon();
  }
  ,watch:{
    heroName(curVal,oldVal){
      // if (curVal == '') return;
      this.searchHero(this.strength, curVal);
      this.searchHero(this.intelligent, curVal);
      this.searchHero(this.agile, curVal);
    },
    '$route': 'changeIcon',
  }
}
</script>