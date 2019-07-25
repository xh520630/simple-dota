<template>
  <div class="errorSubmit" style="text-align:center;">
    <el-button plain type="primary"  @click="errorSubmitVisible = true"
    style="margin: 20px auto 0 auto">错误提交</el-button>
    <el-dialog 
    title="错误提交" 
    :visible.sync="errorSubmitVisible"
    :before-close="cancelSubmit">
      <el-form :model="errorInfo">
        <el-form-item label="错误类型" label-width="20%">
          <el-radio-group v-model="errorInfo.type">
            <el-radio-button :label="1">不朽缺失</el-radio-button>
            <el-radio-button :label="2">特效有误</el-radio-button>
            <el-radio-button :label="3">图不够好看</el-radio-button>
            <el-radio-button :label="4">其他</el-radio-button>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="说出你的原因" label-width="20%" v-show='errorInfo.type == 4'>
          <el-input v-model="errorInfo.desc" placeholder="是啥子问题嘛?" style="width:60%;"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="cancelSubmit">取 消</el-button>
        <el-button type="primary" @click="submitError">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data () {
    return {
      msg: 'Welcome to Your Vue.js App',
      errorSubmitVisible: false,
      errorInfo:{
        'origin' : this.$route.query.hero_id ? 'hero' : 'ornament',
        'id': this.$route.query.hero_id ? this.$route.query.hero_id : this.$route.query.ornament_id,
      },
    }
  },
  methods:{
    cancelSubmit(){
      this.errorSubmitVisible = false;
      this.resetError();
    },
    submitError(){
      if (!this.errorInfo.type)
        return this.$message.error('还没选择什么错误噢');
      if (this.errorInfo.type == 4 && !this.errorInfo.desc)
        return this.$message.error('是啥子问题嘛..你得告诉我啊');      
      this.$axios.post(this.GLOBAL.api_url + '/errorSubmit', this.errorInfo).then((res)=>{
        if (res.data.code !== 200)
          return this.$message.error(res.data.message);
        this.cancelSubmit();
        this.$message.success('提交成功');
      })
    },
    resetError(){
      return this.errorInfo = {
        'origin' : this.$route.query.hero_id ? 'hero' : 'ornament',
        'id': this.$route.query.hero_id ? this.$route.query.hero_id : this.$route.query.ornament_id,
      };
    }
  },
  created(){
    this.test;
  }
}
</script>