<template>
  <div class="message_board">
    <el-form :model="newMessage" ref="ruleForm" label-width="20%" class="newMessage">
      <el-form-item label="留言内容" prop="cont">
        <el-input type="textarea" v-model="newMessage.cont"></el-input>
      </el-form-item>
      <el-form-item label="留言人" prop="name">
        <el-input v-model="newMessage.name"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
        <el-button @click="resetForm('ruleForm')">重置</el-button>
      </el-form-item>
    </el-form>
    <el-card class="box-card msgBoard">
      <div v-for="o in 4" :key="o" class='msg'>
        {{'列表内容 ' + o }}
      </div>
    </el-card>
  </div>
</template>

<script>
export default {
  name: 'HelloWorld',
  data () {
    return {
      msg: 'Welcome to Your Vue.js App',
      newMessage: {},
    }
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          alert('submit!');
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    }
  },
  watch:{

  }
}
</script>
<style scoped>
  .message_board{
    max-width: 1440px;
    margin: 20px auto 0 auto;
    text-align: center;
  }
  .newMessage{
    max-width: 800px;
    margin: 0 auto; 
  }
  .msgBoard{
    max-width:1000px;
    margin: 20px auto 0 auto
  }
  .msg{
    font-size: 16px;
    height: 40px;
    line-height: 40px;
  }
</style>