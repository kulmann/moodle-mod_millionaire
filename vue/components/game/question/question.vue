<template lang="pug">
    #millionaire-question
        template(v-if="question")
            loadingAlert(v-if="mdl_question === null", :message="strings.game_loading_question")
            template(v-else)
                finished(v-if="isGameFinished")
                jokerAudience(v-if="usedJokerAudience", :joker="usedJokerAudience")
                jokerFeedback(v-if="usedJokerFeedback", :joker="usedJokerFeedback")
                div(:is="componentByType", :levels="levels", :gameSession="gameSession", :question="question", :mdl_question="mdl_question", :mdl_answers="mdl_answers", :usedJokers="usedJokers")
                actions(v-if="areActionsAllowed").uk-margin-small-top
</template>

<script>
    import {mapState} from 'vuex';
    import finished from './finished';
    import mixins from '../../../mixins';
    import jokerAudience from '../joker/joker-audience';
    import jokerFeedback from '../joker/joker-feedback';
    import questionActions from './question-actions';
    import questionError from './question-error';
    import questionSingleChoice from './question-singlechoice';
    import {
        GAME_FINISHED,
        GAME_PROGRESS,
        JOKER_AUDIENCE,
        JOKER_FEEDBACK,
        MODE_QUESTION_ANSWERED,
        MODE_QUESTION_SHOWN
    } from "../../../constants";
    import loadingAlert from '../../helper/loading-alert';
    import _ from 'lodash';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'gameMode',
                'levels',
                'usedJokers',
                'question',
                'mdl_question',
                'mdl_answers'
            ]),
            ...mapState({
                usedJokerFeedback: (state, getters) => getters.getUsedJokerByTypeAndQuestion(JOKER_FEEDBACK, state.question),
                usedJokerAudience: (state, getters) => getters.getUsedJokerByTypeAndQuestion(JOKER_AUDIENCE, state.question),
            }),
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
                return this.highestSeenLevel !== null && this.highestSeenLevel.position === this.question.index;
            },
            isGameOver() {
                return this.gameSession.state !== GAME_PROGRESS;
            },
            isGameFinished() {
                return this.gameSession.state === GAME_FINISHED;
            },
            areActionsAllowed() {
                if (!this.isCurrentQuestion || this.isGameOver) {
                    return false;
                }
                let allowedModes = [MODE_QUESTION_SHOWN, MODE_QUESTION_ANSWERED];
                return _.includes(allowedModes, this.gameMode);
            }
        },
        components: {
            jokerAudience,
            jokerFeedback,
            loadingAlert,
            finished,
            'actions': questionActions,
            'error': questionError,
            'singlechoice': questionSingleChoice,
        }
    }
</script>
