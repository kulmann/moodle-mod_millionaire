<template lang="pug">
    .uk-alert.uk-alert-warning(uk-alert)
        p
            v-icon(name="users").uk-margin-small-right
            b {{ strings.game_joker_audience_title }}
            br
            table.uk-table.uk-table-small.uk-margin-remove
                tr(v-for="bar in bars").progress-container
                    td.uk-table-shrink
                        b {{ bar.label }}:
                    td.uk-table-auto
                        .progress(:data-label="bar.value + '%'")
                            span.value(:style="'width:' + bar.value + '%;'")
</template>

<script>
    import {mapState} from 'vuex';
    import _ from 'lodash';

    export default {
        props: {
            joker: Object
        },
        computed: {
            ...mapState([
                'strings',
                'mdl_answers'
            ]),
            bars() {
                let jokerData = _.split(this.joker.joker_data, ',');
                let items = _.map(jokerData, function (item) {
                    let tmp = _.split(item, '=');
                    return {
                        answerId: parseInt(tmp[0]),
                        value: parseFloat(tmp[1]),
                    }
                });
                _.forEach(this.mdl_answers, function (mdl_answer) {
                    let item = _.find(items, function (item) {
                        return item.answerId === mdl_answer.id;
                    });
                    if (item) {
                        item['label'] = mdl_answer.label;
                    }
                });
                return _.sortBy(items, function (item) {
                    return item.label;
                });
            }
        },
    }
</script>

<style lang="scss" scoped>
    .progress {
        height: 1.7em;
        width: 100%;
        background-color: #c9c9c9;
        border-radius: 5px;
        border: 1px solid #999;
        position: relative;
        margin-top: 2px;

        & > .value {
            background-color: #1177d1;
            display: inline-block;
            height: 100%;
        }

        &:before {
            content: attr(data-label);
            color: #fff;
            font-size: 1em;
            font-weight: bold;
            position: absolute;
            text-align: left;
            top: 1px;
            left: 5px;
            right: 0;
        }
    }
    .progress-container {
        & > td {
            padding-bottom: 0;
            padding-left: 0;
        }
        & > td:last-child {
            padding-right: 0;
        }
    }
</style>
