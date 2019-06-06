<template lang="pug">
    #millionaire-question_singlechoice
        .uk-card.uk-card-default.uk-card-body
            p._question {{ mdl_question.questiontext }}
        vk-grid.uk-margin-top(matched)
            div(v-for="(answer, index) in mdl_answers", :key="answer.id", class="uk-width-1-1@s uk-width-1-2@m uk-width-1-4@l")
                .uk-alert.uk-alert-default._answer(uk-alert, @click="selectAnswer(answer)", :class="getAnswerClasses(answer)")
                    vk-grid.uk-grid-small
                        span.uk-width-auto.uk-text-bold {{ getAnswerLetter(index) }}
                        span.uk-width-expand.uk-text-center {{ answer.answer }}
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import mixins from '../mixins';
    import VkGrid from "vuikit/src/library/grid/components/grid";
    import _ from 'lodash';

    export default {
        mixins: [mixins],
        data () {
            return {
                mostRecentQuestionId: null,
                clickedAnswerId: null,
            }
        },
        computed: {
            ...mapState([
                'strings',
                'question',
                'mdl_question',
                'mdl_answers'
            ]),
            correctAnswerId () {
                let correct = _.find(this.mdl_answers, function(mdl_answer) {
                    return mdl_answer.fraction === 1;
                });
                return correct ? correct.id : null;
            },
            isAnyAnswerGiven () {
                return this.clickedAnswerId !== null;
            }
        },
        methods: {
            ...mapActions([
                'submitAnswer'
            ]),
            selectAnswer (answer) {
                if (this.isAnyAnswerGiven) {
                    // don't allow another submission
                    return;
                }
                this.mostRecentQuestionId = this.question.id;
                this.clickedAnswerId = answer.id;
                this.submitAnswer({
                    'gamesessionid': this.question.gamesession,
                    'levelid': this.question.level,
                    'questionid': this.question.id,
                    'mdlanswerid': this.clickedAnswerId,
                });
            },
            getAnswerLetter (index) {
                let alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                return alphabet[index];
            },
            getAnswerClasses (answer) {
                let result = [];
                if (this.isAnyAnswerGiven) {
                    if (this.isCorrectAnswer(answer)) {
                        result.push('uk-alert-success');
                    }
                    if (this.isClickedAnswer(answer)) {
                        result.push('_thick-border');
                        if (this.isWrongAnswer(answer)) {
                            result.push('uk-alert-danger');
                        }
                    }
                } else {
                    result.push('_pointer');
                }
                return result.join(' ');
            },
            isClickedAnswer (answer) {
                return this.isAnyAnswerGiven && this.clickedAnswerId === answer.id;
            },
            isWrongAnswer (answer) {
                return this.correctAnswerId !== answer.id;
            },
            isCorrectAnswer (answer) {
                return this.correctAnswerId === answer.id;
            },
            initQuestion() {
                this.mostRecentQuestionId = this.question.id;
                this.clickedAnswerId = (this.question.mdl_answer > 0) ? this.question.mdl_answer : null;
            }
        },
        mounted () {
            if  (this.question) {
                this.initQuestion();
            }
        },
        watch: {
            question (question) {
                if (this.mostRecentQuestionId !== question.id) {
                    this.initQuestion();
                }
            }
        },
        components: {
            VkGrid
        }
    }
</script>

<style lang="scss" scoped="scoped">
    ._thick-border {
        border-width: 1px;
        border-style: solid;
    }
    ._answer {
        border-radius: 5px;
        font-size: 1.2em;
    }
    ._question {
        font-size: 1.4em;
    }
</style>
