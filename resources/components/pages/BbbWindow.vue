<template>
    <div
        v-if="joinInfo"
        class="link">
        <div
            class="auto-height"
            :style="`left: ${bbbWindowLeft}px`">
            <iframe
                v-if="!hideFrame"
                ref="frame"
                width="100%"
                height="100%"
                allow="camera *;microphone *;display-capture *;"
                frameborder="0"
                allowfullscreen
                :src="joinInfo.join.url" />
        </div>
    </div>
    <v-alert
        v-else
        type="warning"
        :text="$t('Unable to get information about the meeting, please contact technical support.')" />
</template>

<script>
export default {
    name: 'BbbWindow',
    props: {
        pid: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            left: 0,
            joinInfo: null,
            hideFrame: false
        }
    },
    computed: {
        bbbWindowLeft() {
            return this.$store.getters['getBbbWindowLeft']
        }
    },
    async created() {
        const that = this
        const info = await this.getJoinInfo()
        if (info) {
            this.joinInfo = this.$store.getters['getJoinInfo']
            this.$store.commit('resetActiveJoinInfo')
        }
        if (this.joinInfo === null) {
            this.$router.push({ name: 'meeting_page', params: { id: info.meetingId } })
        }
        window.addEventListener('beforeunload', function (event) {
            event.preventDefault()
            return false
        }, { capture: true })
        window.addEventListener('message', function (e) {
            console.log(e)
            if (typeof e.data === 'object') {
                if (e.data?.fromIFrame) {
                    that.hideFrame = true
                    that.$router.go(-1)
                }
            }
        }, false)
    },
    methods: {
        async getJoinInfo() {
            return await this.$store.dispatch('getJoinInfo', this.pid)
        }
        // need to reload page like /bbb/1234
    }
}
</script>

<style lang="scss" scoped>
.auto-height {
    position: absolute;
    top: 65px;
    right: 0;
    bottom: 0;

    iframe {
        width: 100%;
        height: 100%;
    }
}
</style>
