<template lang="pug">
    #millionaire-question_singlechoice
        template(v-if="mdl_question")
            .uk-card.uk-card-default
                p {{ mdl_question.questiontext }}
            vk-grid
                .uk-card.uk-card-default(v-for="answer in mdl_answers", :key="answer.id")
                    p {{ answer.answer }}
        .uk-alert.uk-alert-primary(uk-alert, v-else)
            p Loading question details
                span._loader
                    span
                    span
                    span
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../mixins';
    import VkGrid from "vuikit/src/library/grid/components/grid";

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
                'mdl_question',
                'mdl_answers'
            ]),
        },
        components: {
            VkGrid
        }
    }
</script>
