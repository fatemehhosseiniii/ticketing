import router from "./router";
import {showToast} from "./toast";

export const myFetch = async (url, useToken, method = 'GET', body = false,setContent=false) => {
    const headers = {'Accept': 'application/json'}
    if (useToken)
        headers.Authorization = `Bearer ${localStorage.getItem('token')}`
    if (setContent)
        headers['Content-Type'] = 'application/json';
    //set options for request
    const options = {
        method,
        headers
    }

    if (body && method !== 'GET')
        options.body = body

    //send Request
    const response = await fetch(url, options)
    const data = await response.json();

    if (response.status === 401) {
        showToast(data.message ?? 'Server Error', 'error')
        localStorage.removeItem('token')
        router.push('/')
        return false;
    }

    if (response.status === 403) {
        showToast(data.message ?? 'Server Error', 'error')
        return false
    }



    return data
}
