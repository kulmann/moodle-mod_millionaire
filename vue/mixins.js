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
                return 'todo: implement multi param strings.';
            }
        }
    },
    methods: {
        findHighestSeenLevel(levels) {
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
    }
}
