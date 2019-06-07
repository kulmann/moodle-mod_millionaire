<template lang="pug">
    #millionaire-game_finished
        .uk-alert(uk-alert, :class="{'uk-alert-success': isOver && isWon, 'uk-alert-danger': isOver && !isWon}")
            p.uk-h3.uk-text-bold.uk-text-center
                v-icon(:name="headlineIcon", :scale="2").uk-margin-small-right
                span {{ headlineText }}
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'gameSession'
            ]),
            isOver() {
                return this.gameSession.state !== 'process';
            },
            isWon() {
                return this.gameSession.won;
            },
            headlineText() {
                if (!this.isOver) {
                    return 'Game is not over. You should not see this screen.'
                }
                if (this.isWon) {
                    return this.strings.game_won_headline;
                } else {
                    return this.strings.game_lost_headline;
                }
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
