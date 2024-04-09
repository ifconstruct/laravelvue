import axios from 'axios';

const instance = axios.create({
    baseURL: 'http://laravelvue/', // Your API base URL
});

export default instance;
