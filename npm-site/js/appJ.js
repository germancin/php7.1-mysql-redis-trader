import { applyMiddleware } from 'redux';
import * as action from './actions';
import storesApp from './reducers';
import thunk from 'redux-thunk';
const store = Redux.createStore(storesApp, applyMiddleware(thunk));
const axios = require('axios');

// store.dispatch(action.loadPlanets());

function render() {
    const { storeApp, userStore } = store.getState();
    const planets = storeApp.planets;
    const users = userStore.users;

    if(typeof planets !== 'undefined'){

        let str = '<ul>';
        Object.keys(planets).map(function(key) {
            str += '<li>'+ planets[key].name + '</li>';
        });
        str += '</ul>';

        localStorage.setItem('planets', JSON.stringify(planets));
        document.getElementById("planets_results").innerHTML = str;
    }

    if(typeof users !== 'undefined'){
        let str = '<ul>';
        Object.keys(users).map(function(key) {
            str += '<li> name: '+ users[key].name + ' age:' + users[key].age + '</li>';
        });
        str += '</ul>';

        localStorage.setItem('users', JSON.stringify(users));
        document.getElementById("users_results").innerHTML = str;
    }

    console.log('store');
    console.log(store.getState());
}

store.subscribe(() => {
    render();
});

render();


document.getElementById('planetsBtn')
    .addEventListener('click', function () {
        store.dispatch(action.getDataPlanets());
    });

document.getElementById('usersBtn')
    .addEventListener('click', function () {
        store.dispatch({ type: action.GET_USERS });
    });