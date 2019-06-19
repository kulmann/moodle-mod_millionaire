<template lang="pug">
    .uk-alert.uk-alert-warning(uk-alert)
        p
            v-icon(name="regular/lightbulb").uk-margin-small-right
            b {{ strings.game_joker_feedback_title }}
            br
            span {{ joker.joker_data }}
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        props: {
            joker: Object
        },
        computed: {
            ...mapState([
                'strings',
            ]),
        },
    }
</script>

<style scoped>
</style>
