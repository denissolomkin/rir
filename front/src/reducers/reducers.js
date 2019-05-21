import {combineReducers} from 'redux';
import * as constants from "../constants/index";

// RESOURCES SEARCH

const resources = (state = {
    isFetching: false,
    didInvalidate: false,
    items: []
}, action) => {
    switch (action.type) {
        case constants.FETCH_RESOURCES_REQUEST:
            return {
                ...state,
                isFetching: true,
                didInvalidate: false
            };
        case constants.FETCH_RESOURCES_SUCCESS:
            return {
                ...state,
                isFetching: false,
                didInvalidate: false,
                items: action.resources,
                lastUpdated: action.receivedAt
            };
        default:
            return state
    }
};

// TREE

const tree = (state = {
    isFetching: false,
    didInvalidate: false,
    items: []
}, action) => {
    switch (action.type) {
        case constants.FETCH_TREE_REQUEST:
            return {
                ...state,
                isFetching: true,
                didInvalidate: false
            };
        case constants.FETCH_TREE_SUCCESS:
            return {
                ...state,
                items: action.form,
                isFetching: false,
                didInvalidate: false
            };
        default:
            return state
    }
};

// FORM

const search = (state = {
    isFetching: false,
    didInvalidate: false,
    items: []
}, action) => {
    switch (action.type) {
        case constants.FETCH_FORM_REQUEST:
            return {
                ...state,
                isFetching: true,
                didInvalidate: false,
            };
        case constants.FETCH_FORM_SUCCESS:
            return {
                ...state,
                items: action.form,
                isFetching: false,
                didInvalidate: false,
                lastUpdated: action.receivedAt
            };
        default:
            return state
    }
};

// CATEGORY

const category = (state = {
    id: ''
}, action) => {
    switch (action.type) {
        case constants.CHOOSE_CATEGORY:
            return {
                ...state,
                id: action.id,
            };
        default:
            return state
    }
};

const rootReducer = combineReducers({
    resources,
    tree,
    search,
    category
});

export default rootReducer;