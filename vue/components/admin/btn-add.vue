<template lang="pug">
    .uk-text-right
        button.btn.btn-default(type="button", @click="emitClick")
            v-icon(name="plus").uk-margin-small-right
            span {{ strings.admin_btn_add }}
</template>

<script>
    import {mapState} from 'vuex';
    import mixins from '../../mixins';

    export default {
        mixins: [mixins],
        computed: {
            ...mapState([
                'strings',
            ]),
        },
        methods: {
            emitClick() {
                this.$emit('click');
            }
        }
    }
</script>
