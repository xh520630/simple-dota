import Main from '@/components/Main'
import HeroInfo from '@/components/HeroInfo'
import Ornament from '@/components/Ornament'


export default [
    {
        path: '*',
        redirect: '/'
    }, {
        path: '/',
        name: 'Main',
        component: Main
    }, {
        path: '/hero',
        name: 'Hero',
        component: HeroInfo
    }, {
        path: '/ornament',
        name: 'Ornament',
        component: Ornament
    },
]
