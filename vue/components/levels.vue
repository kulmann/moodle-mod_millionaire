<template lang="pug">
    #millionaire-levels
        table.uk-table.uk-table-small.uk-table-striped
            tbody
                tr.level.uk-text-nowrap(v-for="level in sortedLevels" :key="level.position", @click="showLevel(level)"
                    :class="{'_pointer': isDoneOrUpcoming(level), 'upcoming-level': isUpcoming(level), 'won-level': isWon(level), 'lost-level': isLost(level)}")
                    td.uk-table-shrink.uk-text-center
                        span {{ level.position + 1 }}
                    td.uk-table-shrink.uk-preserve-width.uk-text-center
                        v-icon(v-if="hasIcon(level)", :name="getIconName(level)", :scale="getIconScale(level)")
                    td.uk-table-shrink.uk-text-right
                        span(v-if="level.name") {{ level.name }}
                        span(v-else) {{ level.score | formatCurrency(level.currency) }}
</template>

<script>
    import {mapState, mapActions} from 'vuex';
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
            ...mapActions([
                'showQuestionForLevel',
            ]),
            isDoneOrUpcoming(level) {
                return this.isDone(level) || this.isUpcoming(level);
            },
            isDone(level) {
                return level.finished;
            },
            isUpcoming(level) {
                return level.position === this.gameSession.current_level;
            },
            isWon(level) {
                return this.isDone(level) && level.correct;
            },
            isLost(level) {
                return this.isDone(level) && !level.correct;
            },
            hasIcon(level) {
                return this.getIconName(level) !== null;
            },
            getIconName(level) {
                if (this.isDone(level)) {
                    if (level.safe_spot) {
                        return 'star';
                    } else if (this.isWon(level)) {
                        return 'check';
                    } else if(this.isLost(level)) {
                        return 'cross';
                    } else {
                        return 'circle';
                    }
                } else if(level.safe_spot) {
                    return 'regular/star';
                } else if(this.isUpcoming(level)) {
                    return 'circle';
                } else {
                    return null;
                }
            },
            getIconScale(level) {
                if (this.getIconName(level) === 'circle') {
                    return 0.4;
                } else {
                    return 0.7;
                }
            },
            showLevel(level) {
                if (this.isDoneOrUpcoming(level)) {
                    this.showQuestionForLevel(level.position);
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

    .upcoming-level {
        font-weight: bold;
        & > td {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
    }

    .won-level {
        & > td {
            background-color: #edfbf6;
        }
    }

    .lost-level {
        & > td {
            background-color: #fef4f6;
        }
    }
</style>
