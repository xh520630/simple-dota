import Main from '@/components/Main'
import Heroes from '@/components/Heroes'
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
        component: Heroes
    }, {
        path: '/banner',
        name: 'Banner',
        component: Banner
    },
]
