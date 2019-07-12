<template>
  <div class="hello">
    <el-container>
      <el-header>Header</el-header>
      <el-main>
          <template>
            <el-carousel height='400px'>
              <el-carousel-item v-for="item in 1" :key="item">
                <img class='banner' src="http://html.cs704.cn/gouma.jpg" alt="#">
              </el-carousel-item>
            </el-carousel>
          </template>
      </el-main>
      <el-footer>Footer</el-footer>
    </el-container>
  </div>
</template>

<script>
export default {
  data () {
    return {
      msg: 'Hello Dota'
    }
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
<style>
  .el-header, .el-footer {
    background-color: #B3C0D1;
    color: #333;
    text-align: center;
    line-height: 60px;
  }
  
  .el-aside {
    background-color: #D3DCE6;
    color: #333;
    text-align: center;
    line-height: 200px;
  }
  
  .el-main {
    background-color: #E9EEF3;
    color: #333;
    text-align: center;
    padding: 0;
  }
  
  body > .el-container {
    margin-bottom: 40px;
  }
  
  .el-container:nth-child(5) .el-aside,
  .el-container:nth-child(6) .el-aside {
    line-height: 260px;
  }
  
  .el-container:nth-child(7) .el-aside {
    line-height: 320px;
  }
</style>