<template lang="pug">
    #millionaire-help
        .uk-card.uk-card-default
            .uk-card-header
                h3 {{ strings.game_help_headline }}
            .uk-card-body
                p(v-html="strings.game_help_message")
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings'
            ]),
        },
    }
</script>
