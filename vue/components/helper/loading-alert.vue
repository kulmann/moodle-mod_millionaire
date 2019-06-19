<template lang="pug">
    .uk-alert.uk-alert-primary(uk-alert)
        p
            v-icon(v-if="icon", :name="icon").uk-margin-small-right
            span {{ message }}
                span._loader
                    span
                    span
                    span
</template>

<script>
    export default {
        props: {
            icon: String,
            message: String
        }
    }
</script>
