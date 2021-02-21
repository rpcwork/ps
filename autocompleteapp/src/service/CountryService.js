import axios from 'axios';


export default class CountryService {

    getCountries(query) {
        /**
         * Static end points for testing:
         * return axios.get(`http://localhost:80/data/datatest.json?q=${query}`)
         * return axios.get(`http://localhost:80/data/empty.json?q=${query}`)
         * 
         * */

        return axios.get(`http://${process.env.VUE_APP_API_HOST}:8081/search?q=${query}`)
        .then((response) => {
            return response.data.data;
        })
        .catch(function (err) {
            if (err.response) {
                // client received an error response (5xx, 4xx)
                // To do: Log error to Sentry logging service for triage and notification
                // fallback to to console logging for this demo
                console.log(err);
            } else if (err.request) {
                // We never received a response or the request never left
                // To do: Log error to Sentry logging service for triage and notification
                // fallback to to console logging for this demo
                console.log(err);
            } else {
                // To do: Log error to Sentry logging service for triage and notification
                // fallback to to console logging for this demo
                console.log(err);
            }
        });
    }
}    
    
