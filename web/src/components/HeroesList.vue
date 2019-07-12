<template>
  <div class="heroes-list">
    <el-row :gutter="5" type="flex" justify='left' style="flex-wrap: wrap; padding: 0 5px">
      <el-col v-for="(index, key) in strength" :key=key :span="1" style='padding-top:10px'>
        <router-link :to="{ path : '/hero', query : { hero_id : index.id }}">
          <div class="grid-content bg-purple">
              <el-image
              style="width: 100%; height: 100%"
              :src="index.avatar"
              fit="fit"></el-image>
          </div>
        </router-link>
      </el-col>
    </el-row>
    <el-row :gutter="5" type="flex" justify='left' style="flex-wrap: wrap; padding: 0 5px">
      <el-col v-for="(index, key) in intelligent" :key=key :span="1" style='padding-top:10px'>
        <router-link :to="{ path : '/hero', query : { hero_id : index.id }}">        
          <div class="grid-content bg-purple">
            <el-image
            style="width: 100%; height: 100%"
            :src="index.avatar"
            fit="fit"></el-image>
          </div>
        </router-link>
      </el-col>
    </el-row>
    <el-row :gutter="5" type="flex" justify='left' style="flex-wrap: wrap; padding: 0 5px">
      <el-col v-for="(index, key) in agile" :key=key :span="1" style='padding-top:10px'>
        <router-link :to="{ path : '/hero', query : { hero_id : index.id }}">
          <div class="grid-content bg-purple">
            <el-image
            style="width: 100%; height: 100%"
            :src="index.avatar"
            fit="fit"></el-image>
          </div>
        </router-link>
      </el-col>
    </el-row>
  </div>
</template>

<script>
export default {
  name: 'HeroesList',
  data () {
    return {
      strength:[]
      ,intelligent:[]
      ,agile:[]
      ,
    }
  }, methods: {
    test1(val){
      console.log(val);
    },
    heroes_info(){
      this.$axios.get(this.GLOBAL.api_url + '/heroes_info').then((res)=>{
        console.log(res.data.data);
        this.strength    = res.data.data.data[0];
        this.intelligent = res.data.data.data[1];
        this.agile       = res.data.data.data[2];
      })
    }
  }, created: function() {
        this.heroes_info();
  }
}
</script>
<style lang="">
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
  .bg-purple-light {
    background: #e5e9f2;
  }
  .grid-content {
    border-radius: 4px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }
</style>