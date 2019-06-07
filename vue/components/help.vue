<template lang="pug">
    #millionaire-help
        h2.uk-text-center {{ strings.game_help_headline }}
        p(v-html="strings.game_help_message")
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings'
            ]),
        },
    }
</script>
