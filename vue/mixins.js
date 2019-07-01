import _ from 'lodash';
export default {
    filters: {
        formatCurrency: formatCurrencyInternal,
        stringParams(translation, params) {
            let tmp = translation;
            if (translation.includes('{$a}')) {
                return _.replace(tmp, '{$a}', params);
            } else if (translation.includes('{$a->')) {
                _.forEach(params, function(value, key) {
                    tmp = _.replace(tmp, '{$a->' + key + '}', value);
                });
                return tmp;
            }
        }
    },
    methods: {
        findHighestSeenLevel(levels) {
            if (levels.length === 0) {
                return null;
            }
            let sortedLevels = _.sortBy(levels, ['position']);
            let seenLevels = _.filter(sortedLevels, function (level) {
                return level.seen;
            });
            if (seenLevels.length === 0) {
                return _.first(sortedLevels);
            } else {
                return _.last(seenLevels);
            }
        },
        formatCurrency: formatCurrencyInternal,
    }
}
function formatCurrencyInternal(amount, currencySign) {
    return amount.toLocaleString() + " " + currencySign;
}
