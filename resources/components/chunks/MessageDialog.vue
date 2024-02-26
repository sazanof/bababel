<template>
    <v-dialog
        v-model="show"
        :persistent="true"
        max-width="500">
        <template #default="{ isActive }">
            <v-card
                v-bind="isActive"
                color="orange-lighten-4"
                :title="title">
                <v-card-text>
                    {{ text }}
                </v-card-text>
                <v-card-actions>
                    <v-spacer />

                    <v-btn
                        width="100%"
                        color="danger"
                        :prepend-icon="btnIcon"
                        :text="btnText"
                        @click="isActive.value = false" />
                </v-card-actions>
            </v-card>
        </template>
    </v-dialog>
</template>

<script>
export default {
    name: 'MessageDialog',
    props: {
        model: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            required: true
        },
        text: {
            type: String,
            required: true
        },
        btnText: {
            type: String,
            required: true
        },
        btnIcon: {
            type: String,
            default: 'mdi-alert'
        }
    },
    data() {
        return {
            show: this.model
        }
    }
}
</script>

<style scoped>

</style>
