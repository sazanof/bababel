<template>
    <VDialog
        v-model="opened"
        :max-width="maxWidth">
        <VCard>
            <template
                v-if="title"
                #title>
                {{ title }}
            </template>
            <template
                v-if="subtitle"
                #subtitle>
                {{ subtitle }}
            </template>
            <template #text>
                <slot />
            </template>
        </VCard>
    </VDialog>
</template>

<script>
export default {
    name: 'AppDialog',
    props: {
        title: {
            type: String,
            default: null
        },
        subtitle: {
            type: String,
            default: null
        },
        maxWidth: {
            type: String,
            default: '500px'
        }
    },
    emits: [ 'on-show', 'on-close' ],
    data() {
        return {
            opened: false
        }
    },
    methods: {
        show() {
            this.opened = true
            this.$emit('on-show')
        },
        close() {
            this.opened = false
            this.$emit('on-close')
        }
    }
}
</script>

<style scoped>

</style>
