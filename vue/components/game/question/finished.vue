<template lang="pug">
    #millionaire-game_finished
        .uk-alert(uk-alert, :class="{'uk-alert-success': isOver && isWon, 'uk-alert-danger': isOver && !isWon}")
            p.uk-h3.uk-text-bold.uk-text-center
                v-icon(:name="headlineIcon", :scale="2").uk-margin-small-right
                span {{ strings.game_over_score | stringParams(gameSession.score_name) }}
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../../../mixins';
    import {GAME_PROGRESS} from "../../../constants";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'gameSession'
            ]),
            isOver() {
                return this.gameSession.state !== GAME_PROGRESS;
            },
            isWon() {
                return this.gameSession.won;
            },
            headlineIcon() {
                if (!this.isOver) {
                    return 'bug';
                }
                if (this.isWon) {
                    return 'glass-cheers';
                } else {
                    return 'regular/sad-tear';
                }
            }
        },
    }
</script>
