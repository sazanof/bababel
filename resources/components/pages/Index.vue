<template>
  <VApp
    v-if="user !== undefined"
    id="inspire"
    class="bg-blue-grey-lighten-5"
    :full-height="true"
  >
    <Sidebar
      :user="user"
      :rail="rail"
    />
    <AppHeader @on-menu-click="onMenuClick" />
    <VMain>
      <VSheet
        class="ma-auto fill-height pb-4 position-relative"
        color="transparent"
      >
        <VSheet
          color="transparent"
          class="fill-height ma-auto"
        >
          <VContainer
            fluid
            class="fill-height"
          >
            <VRow class="fill-height">
              <router-view />
            </VRow>
          </VContainer>
        </VSheet>
      </VSheet>
    </VMain>
  </VApp>
</template>

<script>
import Sidebar from '../chunks/Sidebar.vue'
import AppHeader from '../chunks/AppHeader.vue'

export default {
  name: 'Index',
  components: {
    AppHeader,
    Sidebar
  },
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      rail: true
    }
  },
  watch: {
    rail() {
      this.calculateBbbWindowLeft()
    }
  },
  created() {
    this.rail = this.$route.path !== '/bbb'
    this.$store.commit('setRail', this.rail)
    this.calculateBbbWindowLeft()
  },
  methods: {
    openCreateMeeting() {
      this.$store.commit('clearMeetingState')
      this.$router.push({ name: 'create_meeting' })
    },
    onMenuClick() {
      this.rail = !this.rail
      this.$store.commit('setRail', this.rail)
    },
    calculateBbbWindowLeft() {
      setTimeout(() => {
        const sidebar = document.getElementById('sidebar')
        if (sidebar !== null) {
          const coords = sidebar.getBoundingClientRect()
          this.$store.commit('setBbbWindowLeft', coords.right)
        }
      }, 700)

    }
  }

}
</script>
<style lang="scss" scoped>
.create-menu {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 10;
}
</style>
