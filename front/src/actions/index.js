import * as constants from '../constants/index.js';
import { requestForm, requestTree, requestResources } from '../api/index.js';

// SEARCH RESOURCES //

export const fetchResources = (callback, query) => dispatch => {

    dispatch(fetchResourcesRequest());
    return requestResources(callback, query).then(data => dispatch(fetchResourcesSuccess(data)));
};

export const fetchResourcesRequest = () => {
    return {
        type: constants.FETCH_RESOURCES_REQUEST,
    }
};

export const fetchResourcesSuccess = (json) => {
    console.log(json)
    return {
        type: constants.FETCH_RESOURCES_SUCCESS,
        resources: json,
        receivedAt: Date.now()
    }
};


// GET FORM FIELDS ACTIONS //

export const fetchFormIfNeeded = () => (dispatch) => {
        return dispatch(fetchForm())
    }

export const fetchForm = () => dispatch => {
    dispatch(fetchFormRequest());
    return requestForm().then(data => dispatch(fetchFormSuccess(data)));
};

export const fetchFormRequest = () => {
    return {
        type: constants.FETCH_FORM_REQUEST,
    }
};

export const fetchFormSuccess = (json) => {
    return {
        type: constants.FETCH_FORM_SUCCESS,
        form: json,
        receivedAt: Date.now()
    }
};

// TREE ACTIONS //

export const fetchTree = () => dispatch => {
    dispatch(fetchTreeRequest());
    return requestTree().then(data => dispatch(fetchTreeSuccess(data)));
};

export const fetchTreeRequest = () => {
    return {
        type: constants.FETCH_TREE_REQUEST,
    }
};

export const fetchTreeSuccess = (json) => {
    return {
        type: constants.FETCH_TREE_SUCCESS,
        form: json
    }
};


// CLICK ON ITEM IN TREE AND SUBMIT THE FORM  //

export const fetchCategory = (id) => {
    return {
        type: constants.CHOOSE_CATEGORY,
        id
    }
};
