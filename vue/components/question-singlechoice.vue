<template lang="pug">
    #millionaire-question_singlechoice
        p {{ question.mdl_question_content }}
        p todo... shows answers and make them clickable ;-)
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'question'
            ]),
        },
        methods: {

        }
    }
</script>
