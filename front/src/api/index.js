const RESOURCE_SEARCH_PREFIX = 'https://127.0.0.1:8000/en/api/search';
const TREE_PREFIX = 'https://127.0.0.1:8000/en/api/tree';
const FORM_PREFIX = 'https://localhost:8000/en/api/form';

export const requestForm = () => fetch(`${FORM_PREFIX}`,
    {
        method: "GET",
    }
).then(response => response.json())


export const requestResources = (jsonToFormData, values) =>
    fetch(`${RESOURCE_SEARCH_PREFIX}`, {
        method: "POST",
        body: jsonToFormData(values)
    }).then(response => response.json())


// bodyFormData.append('search_resource[_token]', FORM_TOKEN);

export const requestTree = () => fetch(`${TREE_PREFIX}`,
    {
        method: "GET",
    }
).then(response => response.json())
