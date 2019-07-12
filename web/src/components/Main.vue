<template>
  <div class="hello">
    <el-container>
      <el-main style="min-width:1440px;margin:0 auto;">
          <heroesList></heroesList>
      </el-main>
    </el-container>
  </div>
</template>

<script>
import HeroesList from './HeroesList'
export default {
  data () {
    return {
      msg: 'Hello Dota'
    }
  }
  ,components : {
    'heroesList': HeroesList,
  }
}
</script>
<!--
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
-->