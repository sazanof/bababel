<template>
  <VSheet
    color="transparent"
    max-width="1200"
    class="mx-auto"
  >
    <VCol class="dashboard">
      <VRow>
        <VCol
          cols="12"
          md="6"
        >

          <VCard
            hover
            height="340"
            class="d-flex align-center justify-center card-bg"
            cover
            variant="tonal"
          >
            <VCardText
              class="text-center position-relative"
              style="z-index:100"
            >
              <VBtn
                variant="flat"
                color="deep-orange"
                size="x-large"
                prepend-icon="mdi-plus"
                :text="$t('Create meeting')"
                @click="openCreateMeeting"
              />
            </VCardText>
          </VCard>
        </VCol>
        <VCol
          cols="12"
          md="6"
        >
          <VCard
            height="340"
            class="fill-height"
            variant="tonal"
            :title="$t('Meeting records')"
          >
            <template #text>
              <VList height="270">
                <RecordListItem
                  v-for="record in records.items"
                  :key="record.id"
                  :title="record.meeting.name"
                  :record="record"
                />
              </VList>
            </template>
          </VCard>
        </VCol>
        <VCol
          v-for="card in cards"
          :key="card"
          class="fill-height"
          cols="12"
          md="6"
        >
          <DashboardEventsCard :card="card" />
        </VCol>
      </VRow>
    </VCol>
  </VSheet>

</template>

<script>
import moment from 'moment'
import RecordListItem from '../chunks/RecordListItem.vue'
import DashboardEventsCard from '../chunks/DashboardEventsCard.vue'

const m = moment
export default {
  name: 'Dashboard',
  components: { DashboardEventsCard, RecordListItem },
  data() {
    return {
      copied: false,
      dialogMeeting: null,
      dialogInfoOpen: false,
      loading: false,
      cards: [
        {
          title: this.$t('Agenda'),
          subtitle: this.$t('Meetings scheduled for today'),
          items: []
        },
        {
          title: this.$t('Upcoming meetings'),
          subtitle: this.$t('Upcoming meetings in which you take part'),
          items: []
        }
      ],
      recent: [],
      today: [],
      records: []
    }
  },
  computed: {
    user() {
      return this.$store.getters['getUser']
    }
  },
  async created() {
    this.loading = true
    const all = await this.$store.dispatch('getDashboardMeetings')
      .catch(() => {

      })
      .finally(() => {
        this.loading = false
      })
    if (all) {
      this.today = all.today
      this.recent = all.recent
      this.records = all.records
      this.cards[0].items = this.today
      this.cards[1].items = this.recent
    }
  },
  methods: {
    openCreateMeeting() {
      this.$store.commit('clearMeetingState')
      this.$router.push({ name: 'create_meeting' })
    }


  }
}
</script>

<style lang="scss" scoped>
.card-bg {
    &:after {
        content: "";
        position: absolute;
        top: -10px;
        right: -10px;
        left: -10px;
        bottom: -10px;
        z-index: 11;
        background-image: url("../../img/meeting-bg.jpg");
        background-position: center;
        background-size: cover;
        overflow: hidden;
        opacity: 0.7;
    }
}

.dashboard {
    height: 100%;
    max-height: 800px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.title {
    font-weight: bold;
    color: var(--color-orange);
}

.datetime {
    font-size: 12px;
    display: flex;
    align-items: center;
}

.organizer {
    font-weight: bold;
}
</style>
