<template lang="pug">
    #millionaire-question
        .uk-card.uk-card-default
            .uk-card-body
                template(v-if="question")
                    .uk-alert.uk-alert-primary(uk-alert, v-if="mdl_question === null")
                        p {{ strings.game_loading_question }}
                            span._loader
                                span
                                span
                                span
                    div(v-else, :is="componentByType")
                .uk-alert.uk-alert-primary(uk-alert, v-else)
                    p Show info about level selection if not dead. If dead, show stats?!
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../mixins';
    import questionError from './question-error';
    import questionSingleChoice from './question-singlechoice';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'gameSession',
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
            }
        },
        components: {
            'error': questionError,
            'singlechoice': questionSingleChoice,
        }
    }
</script>
