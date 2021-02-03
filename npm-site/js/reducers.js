import { combineReducers } from 'redux';
import { PLANET_LOADING,FETCH_PLANETS, getUsers, GET_PLANETS } from './actions';

export const storeApp = (state = {}, action) => {

    switch (action.type) {
        case GET_PLANETS:
            console.log('GET GET_PLANETS');
            return Object.assign({}, state, { planets: action.payload });
        // return Object.assign({}, state, { planets: [{name:'ger'}] });
        case PLANET_LOADING:
            return Object.assign({}, state, { planetLoading: action.payload  });
        case FETCH_PLANETS:
            console.log('fetch FETCH_PLANETS');
            return Object.assign({}, state, { planets: action.payload });
            console.log(action.payload);
        default:
            return state;
    }
};

export const userStore = (state = [], action) => {
    if(typeof action !== 'undefined'){

        switch (action.type) {
            case 'GET_USERS':
                const data = Object.assign({}, state, { users: getUsers() });
                return data;
            default:
                return state;
        }
    }
}

export default combineReducers({
    storeApp, userStore
});