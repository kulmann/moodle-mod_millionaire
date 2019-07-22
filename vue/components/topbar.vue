<template lang="pug">
    .uk-grid.uk-grid-collapse.top-bar(uk-grid).uk-flex-middle
        .uk-width-expand
            button.btn.uk-margin-small-left(@click="restartGame", :class="{'btn-primary': isGameOver, 'btn-default': !isGameOver}")
                v-icon(name="redo").uk-margin-small-right
                span {{ strings.game_btn_restart }}
            button.btn.btn-default.uk-margin-small-left(@click="showStats", v-if="statsButtonVisible")
                v-icon(name="chart-line").uk-margin-small-right
                span {{ strings.game_btn_stats }}
            button.btn.btn-default.uk-margin-small-left(@click="showGame", v-if="gameButtonVisible")
                v-icon(name="gamepad").uk-margin-small-right
                span {{ strings.game_btn_game }}
        .uk-width-auto
            button.btn.btn-default.uk-margin-small-right(@click="showAdmin", v-if="adminButtonVisible")
                v-icon(name="cogs")
            button.btn.btn-default.uk-margin-small-right(@click="showHelp")
                v-icon(name="regular/question-circle")
</template>

<script>
    import {mapActions, mapMutations, mapState} from 'vuex';
    import {GAME_PROGRESS, MODE_HELP, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, MODE_STATS} from "../constants";
    import _ from 'lodash';

    export default {
        computed: {
            ...mapState([
                'strings',
                'game',
                'gameMode',
                'gameSession',
                'question'
            ]),
            isAdminUser() {
                return this.game.mdl_user_teacher;
            },
            isAdminScreen() {
                return this.$route.name === 'admin-screen';
            },
            isGameScreen() {
                return this.$route.name === 'game-screen';
            },
            isGameOver() {
                return this.gameSession === null || this.gameSession.state !== GAME_PROGRESS;
            },
            statsButtonVisible() {
                return this.gameMode !== MODE_STATS || !this.isGameScreen;
            },
            gameButtonVisible() {
                if (this.gameSession === null || this.question === null) {
                    return false;
                }
                let modes = [MODE_STATS, MODE_HELP];
                return _.includes(modes, this.gameMode) || !this.isGameScreen;
            },
            adminButtonVisible() {
                return this.isAdminUser && !this.isAdminScreen;
            }
        },
        methods: {
            ...mapActions([
                'createGameSession'
            ]),
            ...mapMutations([
                'setGameMode'
            ]),
            restartGame() {
                this.createGameSession();
                this.goToGameRoute();
            },
            showStats() {
                this.setGameMode(MODE_STATS);
                this.goToGameRoute();
            },
            showGame() {
                if (this.question.finished) {
                    this.setGameMode(MODE_QUESTION_ANSWERED);
                } else {
                    this.setGameMode(MODE_QUESTION_SHOWN)
                }
                this.goToGameRoute();
            },
            showAdmin() {
                this.goToAdminRoute();
            },
            showHelp() {
                this.setGameMode(MODE_HELP);
                this.goToGameRoute();
            },
            goToAdminRoute() {
                if (!this.isAdminScreen) {
                    this.$router.push({name: 'admin-screen'});
                }
            },
            goToGameRoute() {
                if (!this.isGameScreen) {
                    this.$router.push({name: 'game-screen'});
                }
            }
        },
    }
</script>

<style scoped>
    .top-bar {
        background-color: #f8f8f8;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        border: 1px solid #ccc;
        height: 50px;
    }
</style>
