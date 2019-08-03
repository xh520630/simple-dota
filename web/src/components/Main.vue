<template>
  <div class="hello">
    <el-container>
      <el-main style="margin:0 auto;">
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