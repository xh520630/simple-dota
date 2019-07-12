<template>
    <div>
        <h1>{{ msg }}</h1>
        <el-button>默认按钮</el-button>
        <el-button type="primary">主要按钮</el-button>
        <el-button type="success">成功按钮</el-button>
        <el-button type="info">信息按钮</el-button>
        <el-button type="warning">警告按钮</el-button>
        <el-button type="danger">危险按钮</el-button>
    </div>
</template>

<script>
export default {
  data () {
    return {
      msg: 'Welcome to the DOTA WORLD'
    }
  }
}
</script>