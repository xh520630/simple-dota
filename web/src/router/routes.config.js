import Main from '@/components/Main'
import HeroInfo from '@/components/HeroInfo'
import Banner from '@/components/Banner'


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
        path: '/banner',
        name: 'Banner',
        component: Banner
    },
]
