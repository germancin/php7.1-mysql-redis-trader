const axios = require('axios');
/*
 * action types
 */

export const GET_PLANETS = 'GET_PLANETS';
export const PLANET_LOADING = 'PLANET_LOADING';
export const FETCH_PLANETS = 'FETCH_PLANETS';
export const GET_USERS = 'GET_USERS';

/*
 * action creators
 */

export const getDataPlanets = () => dispatch => {
    dispatch({ type: 'PLANET_LOADING', payload:true });
    const planets = axios.get('https://swapi.co/api/planets');
    planets
        .then(response => {
            dispatch({type: 'FETCH_PLANETS', payload:response.data.results});
            dispatch({ type: 'PLANET_LOADING', payload:false });
        })
        .catch(err => {
            // dispatch({type: ERROR_FETCHING_PLANETS, payload: err});
        });
}

export const loadPlanets = () => dispatch => {
    dispatch({ type: 'PLANET_LOADING', payload:true });
    const planets = axios.get('https://swapi.co/api/planets');
    planets
        .then(response => {
            dispatch({type: 'FETCH_PLANETS', payload:response.data.results});
            dispatch({ type: 'PLANET_LOADING', payload:false });
        })
        .catch(err => {
            // dispatch({type: ERROR_FETCHING_PLANETS, payload: err});
        });
};


export function getUsers(){
    let usersObj = [];

    usersObj = [
        {name: "German", age: "21"},
        {name: "Yamelin", age: "24"}
    ];
    return usersObj;
}