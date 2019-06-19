<template lang="pug">
    #millionaire-question_error
        .uk-alert.uk-alert-danger(uk-alert)
            p {{ strings.game_qtype_not_supported | stringParams(this.question.mdl_question_type) }}
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../../../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'question'
            ]),
        },
    }
</script>
