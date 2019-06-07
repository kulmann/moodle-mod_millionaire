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
                div(:is="componentByType")
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
    import _ from 'lodash';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'gameSession',
                'levels',
                'question',
                'mdl_question'
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
                let seenLevels = _.filter(this.levels, function (level) {
                    return level.seen;
                });
                if (seenLevels.length === 0) {
                    return _.first(this.levels);
                } else {
                    let sortedSeenLevels = _.sortBy(seenLevels, ['position']);
                    return _.last(sortedSeenLevels);
                }
            },
            isCurrentQuestion() {
                return this.highestSeenLevel.position === this.question.index;
            },
            isGameOver() {
                return this.gameSession.state !== 'progress';
            },
            isGameFinished() {
                return this.gameSession.state === 'finished';
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
