<template lang="pug">
    #millionaire-question
        template(v-if="question")
            .uk-alert.uk-alert-primary(uk-alert, v-if="mdl_question === null")
                p {{ strings.game_loading_question }}
                    span._loader
                        span
                        span
                        span
            template(v-else)
                finished(v-if="isGameFinished")
                div(:is="componentByType", :levels="levels", :question="question", :mdl_question="mdl_question", :mdl_answers="mdl_answers", :usedJokers="usedJokers")
                actions(v-if="isCurrentQuestion && !isGameOver").uk-margin-small-top
        .uk-alert.uk-alert-primary(uk-alert, v-else)
            p Show info about level selection if not dead. If dead, show stats?!
</template>

<script>
    import {mapState} from 'vuex';
    import gameFinished from './game-finished';
    import mixins from '../mixins';
    import questionActions from './question-actions';
    import questionError from './question-error';
    import questionSingleChoice from './question-singlechoice';
    import {GAME_FINISHED, GAME_PROGRESS} from "../constants";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'levels',
                'usedJokers',
                'question',
                'mdl_question',
                'mdl_answers'
            ]),
            componentByType() {
                switch (this.question.mdl_question_type) {
                    case 'qtype_multichoice_single_question':
                        return 'singlechoice';
                    default:
                        return 'error';
                }
            },
            highestSeenLevel() {
                return this.findHighestSeenLevel(this.levels);
            },
            isCurrentQuestion() {
                return this.highestSeenLevel.position === this.question.index;
            },
            isGameOver() {
                return this.gameSession.state !== GAME_PROGRESS;
            },
            isGameFinished() {
                return this.gameSession.state === GAME_FINISHED;
            }
        },
        components: {
            'actions': questionActions,
            'error': questionError,
            'finished': gameFinished,
            'singlechoice': questionSingleChoice,
        }
    }
</script>
