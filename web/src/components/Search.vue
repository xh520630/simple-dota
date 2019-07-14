<template>
  <el-header>
    <el-row type="flex" justify="center">
      <el-col :span="6">
        <el-button plain  @click="$router.back(-1)">
          <i class="el-icon-arrow-left"></i>
        </el-button>
      </el-col>      
      <el-col :span="6"><div class="grid-content bg-purple-light"></div></el-col>
      <el-col :span="6">
        <el-input
          style='max-width:200px; float:right'
          placeholder="请输入英雄名称"
          suffix-icon="el-icon-search"
          v-model="heroName">
        </el-input>
      </el-col>
    </el-row>
  </el-header>
</template>

<script>
import {mapState,mapGetters,mapActions} from 'vuex';
export default {
  data () {
    return {
      heroName: '',
      list:[1234,45,456,342]
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
    }
  }
  ,computed:{
    ...mapGetters('heroStatus',{
      strength:'getStrength',
      intelligent:'getIntelligent',
      agile:'getAgile',
    }),
  }
  ,created(){
  }
  ,watch:{
    heroName(curVal,oldVal){
      // if (curVal == '') return;
      this.searchHero(this.strength, curVal);
      this.searchHero(this.intelligent, curVal);
      this.searchHero(this.agile, curVal);
    },
  }
}
</script>