<template lang="pug">
    .joker-container.uk-flex.uk-flex-center.uk-flex-middle
        button.btn.btn-primary(v-for="(type,index) in jokerTypes", :key="type", @click="selectJoker(type)", :disabled="isJokerDisabled(type)", :class="getButtonClasses(index)")
            v-icon(:name="getJokerIcon(type)")
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import {GAME_PROGRESS, JOKER_ICONS, MODE_QUESTION_ANSWERED, MODE_QUESTION_SHOWN, VALID_JOKERS} from "../../../constants";
    import _ from 'lodash';

    export default {
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'usedJokers',
                'question',
                'gameMode',
            ]),
            jokerTypes() {
                return VALID_JOKERS;
            },
        },
        methods: {
            ...mapActions([
                'submitJoker'
            ]),
            getButtonClasses(index) {
                let classes = [];
                if (index !== 0) {
                    classes.push('uk-margin-small-right');
                }
                if (index !== VALID_JOKERS.length - 1) {
                    classes.push('uk-margin-small-left');
                }
                return classes.join(' ');
            },
            isInGame() {
                let modes = [MODE_QUESTION_SHOWN, MODE_QUESTION_ANSWERED];
                return _.includes(modes, this.gameMode) && this.gameSession.state === GAME_PROGRESS;
            },
            isJokerValid(type) {
                return _.includes(VALID_JOKERS, type);
            },
            isJokerDisabled(type) {
                if (!this.isInGame() || !this.isJokerValid(type)) {
                    return true;
                }
                let joker = _.find(this.usedJokers, function (joker) {
                    return joker.joker_type === type;
                });
                return !!joker;
            },
            selectJoker(type) {
                if (this.isJokerDisabled(type)) {
                    return;
                }
                if (this.question.finished) {
                    // show joker as available, but ignore it when the current question is already answered.
                    return;
                }
                let args = {
                    gamesessionid: this.gameSession.id,
                    questionid: this.question.id,
                    jokertype: type,
                };
                this.submitJoker(args);
            },
            getJokerIcon(type) {
                return JOKER_ICONS[type];
            }
        },
    }
</script>

<style scoped>
    .joker-container {
        background-color: #f8f8f8;
        border: 1px solid #ccc;
        height: 50px;
    }
</style>
