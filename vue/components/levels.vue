<template lang="pug">
    #millionaire-levels
        table.uk-table.uk-table-small.uk-table-striped
            tbody
                tr.level.uk-text-nowrap(v-for="level in sortedLevels" :key="level.position", @click="setCurrentLevel(level)"
                    :class="{'current-level': isCurrent(level)}")
                    td.uk-table-shrink.uk-text-center
                        span {{ level.position + 1 }}
                    td.uk-table-shrink.uk-preserve-width.uk-text-center
                        v-icon(v-if="level.safe_spot", :name="getStarIconName(level)", scale="0.7")
                        v-icon(v-else-if="isDoneOrCurrent(level)", name="circle", scale="0.4")
                    td.uk-table-shrink.uk-text-right
                        span(v-if="level.name") {{ level.name }}
                        span(v-else) {{ level.score | formatCurrency(level.currency) }}
</template>

<script>
    import {mapState} from 'vuex';
    import _ from 'lodash';
    import mixins from '../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'levels',
                'gameSession'
            ]),
            sortedLevels() {
                return _.reverse(this.levels);
            }
        },
        methods: {
            isDoneOrCurrent(level) {
                return this.isDone(level) || this.isCurrent(level);
            },
            isDone(level) {
                return level.position < this.gameSession.current_level;
            },
            isCurrent(level) {
                return level.position === this.gameSession.current_level;
            },
            getStarIconName(level) {
                return this.isDone(level) ? 'star' : 'regular/star';
            },
            setCurrentLevel(level) {
                if (this.isCurrent(level)) {
                    this.$emit('setCurrentLevel');
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .level {
        & > td {
            padding-top: 0;
            padding-bottom: 0;
        }
    }

    .current-level {
        cursor: pointer;
        font-weight: bold;

        & > td {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
    }
</style>
