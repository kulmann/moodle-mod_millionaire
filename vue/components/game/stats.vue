<template lang="pug">
    #millionaire-stats
        .uk-card.uk-card-default
            .uk-card-header
                h3 {{ strings.game_btn_stats }}
            .uk-card-body
                loadingAlert(v-if="loading", :message="strings.game_loading_stats")
                failureAlert(v-else-if="failed", :message="strings.game_loading_stats_failed")
                table.uk-table.uk-table-small.uk-table-striped(v-else-if="filteredScores.length > 0")
                    thead
                        tr
                            th.uk-table-shrink {{ strings.game_stats_rank }}
                            th.uk-table-auto {{ strings.game_stats_user }}
                            th.uk-table-shrink {{ strings.game_stats_score }}
                            th.uk-table-shrink {{ strings.game_stats_sessions }}
                    tbody
                        tr(v-for="score in mainScores", :key="score.mdl_user", :class="getScoreClasses(score)")
                            td {{ score.rank }}
                            td
                                a(:href="getProfileUrl(score.mdl_user)", target="_blank") {{ score.mdl_user_name }}
                            td.uk-text-right(v-html="formatScore(score.score)")
                            td.uk-text-right {{ score.sessions }}
                        tr(v-if="loserScores.length > 0")
                            td.uk-text-center(colspan="4")
                                v-icon(name="ellipsis-v")
                        tr(v-for="score in loserScores", :key="score.mdl_user", :class="getScoreClasses(score)")
                            td {{ score.rank }}
                            td
                                a(:href="getProfileUrl(score.mdl_user)", target="_blank") {{ score.mdl_user_name }}
                            td.uk-text-right(v-html="formatScore(score.score)")
                            td.uk-text-right {{ score.sessions }}
                infoAlert(v-else, :message="strings.game_stats_empty")
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import mixins from '../../mixins';
    import infoAlert from '../helper/info-alert';
    import loadingAlert from '../helper/loading-alert';
    import failureAlert from '../helper/failure-alert';
    import _ from 'lodash';

    export default {
        mixins: [mixins],
        data() {
            return {
                loading: true,
                failed: false,
            };
        },
        computed: {
            ...mapState([
                'strings',
                'game',
                'scores',
            ]),
            maxRows() {
                return this.game.highscore_count;
            },
            filteredScores() {
                let ownUserId = this.game.mdl_user;
                let ownScore = _.find(this.scores, function(score) {
                    return parseInt(score.mdl_user) === ownUserId;
                });
                return _.filter(this.scores, function(score) {
                    // show first x rows in any case
                    if (score.rank <= this.maxRows) {
                        return true;
                    }
                    // if own score not within first x: show one before and one after own score as well
                    if (ownScore) {
                        if(_.inRange(score.rank, ownScore.rank - 1, ownScore.rank + 2)) {
                            return true;
                        }
                    }
                    return false;
                }.bind(this));
            },
            firstLoser () {
                return _.find(this.filteredScores, function(score, index) {
                    let prev = (index === 0) ? null : this.filteredScores[index - 1];
                    return prev !== null && prev.rank < score.rank - 1;
                }.bind(this));
            },
            mainScores() {
                if (this.firstLoser) {
                    return _.filter(this.filteredScores, function(score) {
                        return score.rank < this.firstLoser.rank;
                    }.bind(this));
                } else {
                    return this.filteredScores;
                }
            },
            loserScores() {
                if (this.firstLoser) {
                    return _.filter(this.filteredScores, function(score) {
                        return score.rank >= this.firstLoser.rank;
                    }.bind(this));
                } else {
                    return [];
                }
            },
        },
        methods: {
            ...mapActions([
                'fetchScores'
            ]),
            formatScore(score) {
                let str = score.toFixed(0);
                if (this.game.currency_for_levels) {
                    str += '&nbsp;' + this.game.currency_for_levels;
                }
                return str;
            },
            getScoreClasses(score) {
                let result = [];
                if (score.mdl_user === this.game.mdl_user) {
                    result.push('uk-text-bold');
                }
                return result.join(' ');
            },
            getProfileUrl(userId) {
                const baseUrl = window.location.origin;
                return `${baseUrl}/user/profile.php?id=${userId}`;
            }
        },
        mounted() {
            this.fetchScores().then(() => {
                this.loading = false;
            }).catch(() => {
                this.loading = false;
                this.failed = true;
            });
        },
        components: {
            infoAlert,
            loadingAlert,
            failureAlert,
        }
    }
</script>
