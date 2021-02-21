<template>
    <section class="flexbox">
        <div class="stretch">
            <h2 id="auto_label">Lookup the Calling Code for any Country</h2>
            <AutoComplete v-model="selectedCountry1" :suggestions="filteredCountries" @complete="searchCountry($event)" field="displayname" placeholder="search.." />
        </div>
    </section>
</template>

<script>
import CountryService from '../service/CountryService';
export default {
    data() {
        return {
            countries: null,
            selectedCountry1: null,
            selectedCountry2: null,
            filteredCountries: null,
            selectedCountries: []
        }
    },
    countryService: null,
    created() {
        try {
            this.countryService = new CountryService();
        } catch (err) {
            // To do: Log error to Sentry logging service for triage and notification
            // fallback to to console logging for this demo
            console.log(err);
        }
    },
    mounted() {
    },
    methods: {
        searchCountry(event) {
            try {
                if (event.query && event.query.trim().length) {
                    setTimeout(() => {
                        this.countryService.getCountries(event.query.trim()).then( (data) => {
                            if(data == '[]'){
                                return 0;
                            }else{
                                this.filteredCountries =  data.filter((country) => {
                                    return country.nicename.toLowerCase();
                                });
                            }
                        });    
                    }, 100);
                }
            } catch (err) {
                // To do: Log error to Sentry logging service for triage and notification
                // fallback to to console logging for this demo
                console.log(err);
            }
        }
    }
}
</script>
<style >
.flexbox { display: flex; }
.flexbox .stretch { flex: 1; }
.flexbox .normal { flex: 0; margin: 0 0 0 1rem; }
.flexbox div input { padding: .5em 1em; width: 100%; }
h2 {
  margin: 40px 5px 20px 5px;
  font-family: Avenir, Helvetica, Arial, sans-serif;
}

.p-component {width:90%;}
</style>