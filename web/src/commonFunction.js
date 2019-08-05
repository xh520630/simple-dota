import store from '@/store'
import axios from 'axios'
import GLOBAL from '@/components/Global'


export default {
    getHeroList(){
        axios(GLOBAL.api_url + '/heroes_info').then((res)=>{  
                store.dispatch('heroStatus/updateAgile',       res.data.data.data[1]);
                store.dispatch('heroStatus/updateStrength',    res.data.data.data[0]);
                store.dispatch('heroStatus/updateIntelligent', res.data.data.data[2]);
            }
        )
    }
}