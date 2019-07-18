import Main from '@/components/Main'
import HeroInfo from '@/components/HeroInfo'
import Ornament from '@/components/Ornament'
import AddOrnament from '@/components/AddOrnament'


export default [
    {
        path: '*',
        redirect: '/',
    }, {
        path: '/',
        name: 'Main',
        component: Main,
        meta: { title : '你终于来啦(๑•́ ₃ •̀๑)' }
    }, {
        path: '/hero',
        name: 'Hero',
        component: HeroInfo,
        meta: { title : '勤劳的不朽们' }
    }, {
        path: '/ornament',
        name: 'Ornament',
        component: Ornament,
        meta: { title : 'do.cs704.cn' }
    }, {
        path: '/ornament/addOrnament',
        name: 'AaddOrnament',
        component: AddOrnament,
        meta: { title : '添加饰品' }
    },
]
