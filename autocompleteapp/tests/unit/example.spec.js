import { expect } from 'chai'
//import { shallowMount } from '@vue/test-utils'
import { mount } from '@vue/test-utils'
import "primeflex/primeflex.css";
import "primevue/resources/themes/saga-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
//import AutoComplete from "primevue/autocomplete";
//import AutoCompleteDemo from '@/App.vue'
import AutoCompleteDemo from '@/components/AutoCompleteDemo.vue'

describe('AutoCompleteDemo.vue', () => {
  it('should not include label ', () => {
    const msg = 'should not include this message'
    const wrapper = mount(AutoCompleteDemo, {})
    //console.log(wrapper);
    expect(wrapper.find('#auto_label').text()).to.not.include(msg)
  }),
  it('should render and include label correctly ', () => {
    const msg = 'Lookup the Calling Code for any Country'
    const wrapper = mount(AutoCompleteDemo, {})
    //console.log(wrapper);
    expect(wrapper.find('#auto_label').text()).to.include(msg)
  })

})
