<template>
  <div class="message_board">
     <el-alert
      style='max-width:1000px;margin: 0 auto 30px auto'
      title="留下点什么让我晓得有人来过~"
      type="info"
      :center=true>
    </el-alert>
    <el-form :model="newMessage" ref="ruleForm" label-width="20%" class="newMessage">
      <el-form-item label="留言内容" prop="cont" 
      :rules="[{ required: true, message: '请输入留言内容', trigger: 'blur' }]">
        <el-input type="textarea" v-model="newMessage.cont"></el-input>
      </el-form-item>
      <el-form-item label="留言人" prop="name"
      :rules="[{ required: true, message: '请输入留言内容', trigger: 'blur' }]">
        <el-input v-model="newMessage.name"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm('ruleForm')" style="margin-left:-20%">立即创建</el-button>
        <el-button @click="resetForm('ruleForm')">重置</el-button>
      </el-form-item>
    </el-form>
    <el-card class="box-card msgBoard">
      <div v-for="(item, index) in messageList" :key="index" class='msg' style="position: relative; text-align:left;">
        <span style='margin-left: 40px'>{{ item.create_user + '：' + item.content | stringLength }}</span>
        <span style="float:right; font-size:14px; position: absolute; right: 20px; bottom:0">{{ item.create_time | createTime }}</span>
      </div>
      <el-pagination
        style="text-align: right"
        background
        @current-change="handleCurrentChange"
        layout="prev, pager, next"
        :total="total"
        :page-size=4>
      </el-pagination>
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
      page: 1,
      total: 0,
      messageList: [],
    }
  },
  methods: {
    getMessage() {
      this.$axios.get(this.GLOBAL.api_url + '/message?page=' + this.page).then((res)=>{
        console.log(res.data.data);
        this.total = res.data.data.total;
        this.messageList = res.data.data.data;
      });
    },
    addMessage() {
      this.$axios.post(this.GLOBAL.api_url + '/message', this.newMessage).then((res)=>{
        if (res.data.code !== 200)
          return this.$message.error('出错了噢,' + res.data.message);
        this.$message.success('留言成功,审核后会发出~(其实没审核)');
        this.resetForm('ruleForm');
        this.getMessage();
      });
    },
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (!valid) return false;
        this.addMessage();
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    },
    handleCurrentChange(val) {
      this.page = val;
      this.getMessage();
    }
  },
  created() {
    this.getMessage();
  },
  watch:{

  },
  filters: {
  stringLength: function (value) {
    if (value.length > 60);
      return value.substr(0, 120) + '...';
  },
  createTime: function (value) {
      return value.substr(6, 10);
  }
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
    margin: 20px auto 0 auto;
    /* height: 392px; */
  }
  .msg{
    font-size: 16px;
    height: 80px;
    line-height: 40px;
  }
</style>