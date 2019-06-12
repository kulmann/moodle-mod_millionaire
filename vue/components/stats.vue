<template lang="pug">
    #millionaire-stats
        .uk-card.uk-card-default
            .uk-card-header
                h3 {{ strings.game_btn_stats }}
            .uk-card-body
                loadingAlert(v-if="loading", :message="strings.game_loading_stats")
                failureAlert(v-else-if="failed", :message="strings.game_loading_stats_failed")
                table.uk-table.uk-table-small.uk-table-striped(v-else)
                    thead
                        tr
                            th.uk-table-shrink {{ strings.game_stats_rank }}
                            th.uk-table-expand {{ strings.game_stats_user }}
                            th.uk-table-shrink {{ strings.game_stats_score }}
                    tbody
                        tr(v-for="(score,index) in scores", :key="index")
                            td {{ score.rank }}
                            td {{ score.mdl_user_name }}
                            td.uk-text-right {{ score.score.toFixed(1) }}
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import mixins from '../mixins';
    import loadingAlert from '../helper/loading-alert';
    import failureAlert from '../helper/failure-alert';

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
                'scores',
            ]),
        },
        methods: {
            ...mapActions([
                'fetchScores'
            ])
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
            loadingAlert,
            failureAlert,
        }
    }
</script>
