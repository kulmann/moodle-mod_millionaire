<template lang="pug">
    #millionaire-question_singlechoice
        .uk-card.uk-card-default.uk-card-body
            p {{ mdl_question.questiontext }}
        vk-grid.uk-text-center.uk-margin-top(matched)
            div(v-for="answer in mdl_answers", :key="answer.id", class="uk-width-1-1@s uk-width-1-2@m uk-width-1-4@l")
                .uk-card.uk-card-default.uk-card-body
                    p {{ answer.answer }}
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
