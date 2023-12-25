<template>
    <v-dialog
        v-model="opened"
        max-width="80%"
        width="700px">
        <v-card>
            <template #actions>
                <v-btn
                    prepend-icon="mdi-content-save"
                    color="deep-orange"
                    @click="savePhoto">
                    {{ $t('Save') }}
                </v-btn>
            </template>
            <template #default>
                <Cropper
                    v-if="file"
                    :src="url"
                    :stencil-props="{
                        aspectRatio: 10/10
                    }"
                    @change="change" />
            </template>
        </v-card>
    </v-dialog>
</template>

<script>
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

export default {
    name: 'ImageCropper',
    components: {
        Cropper
    },
    props: {

        file: {
            type: Blob,
            required: true
        }
    },
    emits: [ 'on-save' ],
    data() {
        return {
            opened: false,
            coordinates: null,
            canvas: null
        }
    },
    computed: {
        url() {
            return URL.createObjectURL(this.file)
        }
    },
    methods: {
        open() {
            this.opened = true
        },
        close() {
            this.opened = false
        },
        change({ coordinates, canvas }) {
            console.log(coordinates, canvas)
            this.coordinates = coordinates
            this.canvas = canvas
        },
        savePhoto() {
            this.$emit('on-save', {
                coordinates: this.coordinates,
                canvas: this.canvas
            })
            this.close()
        }
    }
}
</script>

<style scoped>

</style>
