export default {
    filters: {
        formatCurrency(amount, currencySign) {
            return amount.toLocaleString() + " " + currencySign;
        }
    },
    methods: {

    }
}
