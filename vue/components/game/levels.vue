<template lang="pug">
    table.uk-table.uk-table-small.uk-table-striped
        tbody
            tr.level.uk-text-nowrap(v-for="level in sortedLevels" :key="level.position", @click="selectLevel(level)"
                :class="{'_pointer': isDone(level) || isUpcoming(level), 'upcoming-level': isUpcoming(level) && isSeen(level), 'won-level': isWon(level), 'lost-level': isLost(level)}")
                td.uk-table-shrink.uk-preserve-width
                    v-icon(v-if="isSelected(level) && isWon(level)", name="flag-checkered", :scale="0.7")
                    v-icon(v-else-if="isSelected(level)", name="flag", :scale="0.7")
                td.uk-table-shrink.uk-text-center
                    span {{ level.position + 1 }}
                td.uk-table-shrink.uk-preserve-width.uk-text-center
                    v-icon(v-if="hasIcon(level)", :name="getIconName(level)", :scale="getIconScale(level)")
                td.uk-table-auto.uk-text-right {{ level.title }}
                td.uk-table-shrink.uk-preserve-width.uk-text-center(v-if="usedJokers.length > 0")
                    v-icon(v-for="jokerIcon in getUsedJokerIcons(level)", :key="jokerIcon", :name="jokerIcon", :scale="0.7").joker-icon
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import _ from 'lodash';
    import mixins from '../../mixins';
    import {GAME_PROGRESS, JOKER_ICONS} from "../../constants";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'levels',
                'gameSession',
                'question',
                'usedJokers',
            ]),
            sortedLevels() {
                return _.reverse(_.sortBy(this.levels, ['position']));
            }
        },
        methods: {
            ...mapActions([
                'showQuestionForLevel',
            ]),
            isGameOver() {
                return this.gameSession.state !== GAME_PROGRESS;
            },
            isSeen(level) {
                return level.seen;
            },
            isDone(level) {
                return level.finished;
            },
            isUpcoming(level) {
                return level.position === this.gameSession.current_level && !this.isGameOver();
            },
            isWon(level) {
                return this.isDone(level) && level.correct;
            },
            isLost(level) {
                return this.isDone(level) && !level.correct;
            },
            isSafeSpot(level) {
                return level.safe_spot && !this.gameSession.continue_on_failure;
            },
            isSelected(level) {
                if (this.question) {
                    return this.question.level === level.id;
                } else {
                    return false;
                }
            },
            hasIcon(level) {
                return this.getIconName(level) !== null;
            },
            getIconName(level) {
                if (this.isDone(level)) {
                    if (this.isSafeSpot(level)) {
                        return 'star';
                    } else if (this.isWon(level)) {
                        return 'check';
                    } else if (this.isLost(level)) {
                        return 'times';
                    } else if (this.isGameOver()) {
                        return 'lock';
                    } else {
                        return null;
                    }
                } else if (this.isGameOver()) {
                    return 'lock';
                } else if (this.isSafeSpot(level)) {
                    return 'regular/star';
                } else if (this.isUpcoming(level) && this.isSeen(level)) {
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
            selectLevel(level) {
                if (this.isDone(level) || this.isUpcoming(level)) {
                    this.showQuestionForLevel(level.position);
                }
            },
            getUsedJokerIcons(level) {
                let icons = [];
                _.forEach(this.usedJokers, function(usedJoker) {
                    if (usedJoker.level_id === level.id) {
                        icons.push(JOKER_ICONS[usedJoker.joker_type]);
                    }
                });
                return icons;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .level {
        & > td {
            padding: 0 0 0 5px;
        }
        & > td:last-child {
            padding-right: 5px;
        }
    }

    .upcoming-level {
        font-weight: bold;

        & > td {
            background-color: #fff6ee;
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

    .joker-icon {
        margin-left: 2px;
        margin-right: 2px;
    }
</style>
