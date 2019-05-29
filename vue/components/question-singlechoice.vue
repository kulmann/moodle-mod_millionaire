<template lang="pug">
    #millionaire-question_singlechoice
        .uk-card.uk-card-default
            p {{ mdl_question.questiontext }}
        vk-grid
            .uk-card.uk-card-default(v-for="answer in mdl_answers", :key="answer.id")
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
