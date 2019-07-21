<template>
  <div class="addOrnament" style="margin-top: 20px">
    <el-row>
      <el-col :span="8"><div class="grid-content bg-purple"></div></el-col>
      <el-col :span="8">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>添加饰品</span>
            <router-link :to="{ path: '/'}">
              <el-button style="float: right; padding: 3px 0" type="text">回到首页</el-button>
            </router-link>
          </div>
          <div class='selectHero'>
              <el-select v-model="hero_attr" placeholder="请选择属性">
                <el-option
                  v-for="item in base_attr"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value">
                </el-option>
              </el-select>
              <el-select v-model="hero_id" placeholder="请选择英雄" style="margin-left:20px">
                <el-option
                  v-for="item in hero_list"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id">
                </el-option>
              </el-select>
          </div>
          <div class='ornamentInfo' v-show='hero_id'>
            <el-form label-position="right" ref="ornamentForm" label-width="20%" :model="ornamentData">
              <el-form-item label="饰品名称" prop="name">
                <el-input v-model="ornamentData.name"></el-input>
              </el-form-item>
              <el-form-item label="出处简介" prop="desc">
                <el-input v-model="ornamentData.desc"></el-input>
              </el-form-item>
              <el-form-item label="饰品价值" prop="rate">
                  <el-rate v-model="ornamentData.rate" style="margin-top: 10px"></el-rate>
              </el-form-item>
              <el-form-item label="饰品封面" prop="avatar">
                <el-upload
                  class="avatar-uploader"
                  :action="upload_url + '?token=ritian'"
                  :show-file-list="false"
                  :on-success="handleAvatarSuccess"
                  :before-upload="beforeAvatarUpload">
                  <!-- <img v-if="imgUrl" :src="imgUrl" class="avatar"> -->
                  <img v-if="ornamentData.avatar" :src="ornamentData.avatar" class="avatar">
                  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                </el-upload>
              </el-form-item>
              <el-form-item label="效果图" prop="gifArr">
                <el-upload
                  class="upload-demo"
                  :action="upload_url + '?token=ritian'"
                  :on-preview="handlePreview"
                  :on-remove="handleRemove"
                  :file-list="ornamentData.gifArr"
                  list-type="picture">
                  <el-button size="small" type="primary">点击上传</el-button>
                </el-upload>
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="submitForm('ornamentForm')">立即创建</el-button>
                <el-button @click="resetForm('ornamentForm')">重置</el-button>
              </el-form-item>
            </el-form>
          </div>
        </el-card>
      </el-col>
      <el-col :span="8"><div class="grid-content bg-purple"></div></el-col>
    </el-row>
  </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex';
export default {
  data () {
    return {
      msg: 'Hello Dota',
      upload_url: this.GLOBAL.file_url + '/upload',
      base_attr: [{
          value: 'strength',
          label: '力量型'
        }, {
          value: 'agile',
          label: '敏捷型'
        }, {
          value: 'intelligent',
          label: '智力型'
        },], 
      hero_attr: '',
      hero_list: [],
      hero_id: '1',
      ornamentData: {
        // 'name': '',
        // 'desc': '',
        'avatar': '', // 这个必须设置不然图出不来
      },
      imgUrl: '',
      fileList: [],

    }
  }
  ,methods:{
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
    },
    handleAvatarSuccess(res, file) {
      this.ornamentData.avatar = URL.createObjectURL(file.raw);;
      // this.imgUrl = URL.createObjectURL(file.raw);;
    },
    beforeAvatarUpload(file) {
      const isJPG = file.type === 'image/jpeg';
      const isLt2M = file.size / 1024 / 1024 < 2;

      if (!isJPG) {
        this.$message.error('上传头像图片只能是 JPG 格式!');
      }
      if (!isLt2M) {
        this.$message.error('上传头像图片大小不能超过 2MB!');
      }
      return isJPG && isLt2M;
    },
    handleRemove(file, fileList) {
      console.log(file, fileList);
    },
    handlePreview(file) {
      console.log(file);
    },
  }
  ,created(){

  }
  ,computed: {
    ...mapGetters('heroStatus',{
      strength: 'getStrength',
      intelligent: 'getIntelligent',
      agile: 'getAgile',
    }),
  }
  ,watch: {
    hero_attr(curVal, oldVal){
      if (curVal !== oldVal) 
        this.hero_id = '';
      // 瞎搞着玩,明明可以3个if的.
      this.hero_list = curVal == 'strength' ? this.strength : 
      ( curVal == 'agile' ? this.agile : this.intelligent);
    },
  }
}
</script>
<style>
  .el-row {
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .el-col {
    border-radius: 4px;
  }
  .grid-content {
    border-radius: 4px;
    min-height: 36px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }
  .selectHero{
    text-align: center;
    width: 100%;
  }
  .ornamentInfo{
    width: 80%;
    margin: 20px auto 0 auto;
  }
  /* 上传封面 */
    .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #409EFF;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 98px;
    height: 98px;
    line-height: 98px;
    text-align: center;
  }
  .avatar {
    width: 98px;
    height: 98px;
    display: block;
  }
</style>