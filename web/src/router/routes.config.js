import Main from '@/components/Main'
import HeroInfo from '@/components/HeroInfo'
import Ornament from '@/components/Ornament'
import AddOrnament from '@/components/AddOrnament'
import MessageBoard from '@/components/MessageBoard'
import UpdateLog from '@/components/UpdateLog'


export default [
    {
        path: '*',
        redirect: '/',
    }, {
        path: '/',
        name: 'Main',
        component: Main,
        meta: { title : 'DOTA2饰品展示厅(๑•́ ₃ •̀๑)' }
    }, {
        path: '/hero',
        name: 'Hero',
        component: HeroInfo,
        meta: { title : '勤劳的不朽们' }
    }, {
        path: '/ornament',
        name: 'Ornament',
        component: Ornament,
        meta: { title : 'DOTA2饰品展示厅(๑•́ ₃ •̀๑)' }
    }, {
        path: '/ornament/addOrnament',
        name: 'AaddOrnament',
        component: AddOrnament,
        meta: { title : '添加饰品' }
    }, {
        path: '/message_board',
        name: 'MessageBoard',
        component: MessageBoard,
        meta: { title : '留言板' }
    }, {
        path: '/update_log',
        name: 'UpdateLog',
        component: UpdateLog,
        meta: { title : '更新日志' }
    },
]
