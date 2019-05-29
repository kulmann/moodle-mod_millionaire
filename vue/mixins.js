import _ from 'lodash';
export default {
    filters: {
        formatCurrency(amount, currencySign) {
            return amount.toLocaleString() + " " + currencySign;
        },
        stringParams(translation, params) {
            let tmp = translation;
            if (translation.includes('{$a}')) {
                return _.replace(tmp, '{$a}', params);
            } else {
                // figure this out when it's needed
                return 'yo! implement multi param strings.';
            }
        }
    },
    methods: {

    }
}
