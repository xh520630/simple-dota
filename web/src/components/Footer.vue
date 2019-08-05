<template>
  <el-footer style='margin-top:40px;border-top: 1px solid #999;text-align:center;'>
    <div class="" style="margin-top: 10px">
      <el-image
      style="width: 100px; height: 100px; margin-right:-6px;"
      :src="imgArr[footerImages][0]"
      fit="fit"></el-image>
      <el-image
      v-show="imgArr[footerImages][1]"
      style="width: 100px; height: 100px"
      :src="imgArr[footerImages][1]"
      fit="fit"></el-image>
      <el-image
      v-show="imgArr[footerImages][2]"
      style="width: 100px; height: 100px"
      :src="imgArr[footerImages][2]"
      fit="fit"></el-image>
    </div>
    <span class='img_from' v-if='showDesc'>
      头像均来自小姐姐<a href="https://weibo.com/u/7033785014" target="_blank" style="color:#333;border-bottom:1px solid #999">@tidy和两只猫</a><br>
      有意见建议可以<a href="https://weibo.com/u/2386733455" target="_blank" style="color:#333;border-bottom:1px solid #999">联系作者</a><br>
      <!-- <span style='color:#777'>至于为什么没有TA的头像....<br></span> -->
      <!-- <span style='color:#999'>大概是发漏了吧,咱也不知道咱也不敢问~<br></span> -->
    </span>
  </el-footer>
</template>

<script>
export default {
  name: 'HelloWorld',
  data () {
    return {
      msg: 'Welcome to Your Vue.js App',
      footerImages: 0, // 底部image数组
      imgArr: {0 : { '0' : 'http://qiniu.cs704.cn/images/avatar/%E5%A4%A9%E6%80%92%E6%B3%95%E5%B8%88.jpg',
                  '1' : 'http://qiniu.cs704.cn/images/avatar/%E5%A4%8D%E4%BB%87%E4%B9%8B%E9%AD%82.jpg'},
                1 : { '0' : 'http://qiniu.cs704.cn/images/avatar/%E6%B0%B4%E6%99%B6%E5%AE%A4%E5%A5%B3.jpg',
                  '1' : 'http://qiniu.cs704.cn/images/avatar/%E6%8B%8D%E6%8B%8D%E7%86%8A.jpg',
                  '2' : 'http://qiniu.cs704.cn/images/avatar/%E5%89%A7%E6%AF%92.jpg'},
                2 : { '0' : 'http://qiniu.cs704.cn/images/avatar/%E5%A4%A9%E6%80%92%E6%B3%95%E5%B8%88.jpg',
                  '1' : 'http://qiniu.cs704.cn/images/avatar/%E5%A4%8D%E4%BB%87%E4%B9%8B%E9%AD%82.jpg'},
              },
      showDesc: true,
    }
  },methods:{
    changeFooter(){
      // 这里是不是可以用个状态模式..
      if (this.$route.path == '/')
      {
        this.footerImages = 0;
        this.showDesc = true;
      }
      if (this.$route.path == '/hero')
      {
        this.footerImages = 1;
        this.showDesc = false;
      }
      if (this.$route.path == '/ornament')
      {
        this.footerImages = 2;
        this.showDesc = false;
      }    
    }
  },watch:{
    '$route': 'changeFooter',
  }
}
</script>