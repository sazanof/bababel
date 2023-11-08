<template>
    <v-col
        v-for="card in cards"
        :key="card"
        cols="12">
        <v-card
            :loading="loading"
            :title="card.title"
            :subtitle="card.subtitle">
            <v-list>
                <template
                    v-for="n in 3"
                    :key="n">
                    <v-list-item>
                        <v-list-item-title>
                            <div class="datetime blue-grey-lighten-1">
                                <span>02.12.2022 14:54</span>
                            </div>
                            <div class="title mb-1">
                                Meeting #{{ n }}
                            </div>
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            <div class="subtitle mb-2">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil repellendus distinctio
                                similique
                            </div>
                        </v-list-item-subtitle>
                        <template #append>
                            <v-btn
                                variant="text"
                                icon="mdi-chevron-right" />
                        </template>
                    </v-list-item>

                    <v-divider
                        v-if="n !== 3"
                        :key="`divider-${n}`"
                        inset />
                </template>
            </v-list>
            <v-card-actions>
                <v-btn>{{ $t('More') }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-col>
</template>

<script>
export default {
    name: 'Dashboard',
    data() {
        return {
            loading: false,
            cards: [
                {
                    title: this.$t('Agenda'),
                    subtitle: this.$t('Meetings scheduled for today')
                },
                {
                    title: this.$t('Upcoming meetings'),
                    subtitle: this.$t('Upcoming meetings in which you take part')
                }
            ]
        }
    }
}
</script>

<style lang="scss" scoped>
.title {
    font-weight: bold;
    color: var(--color-orange);
}

.datetime {
    font-size: 12px;
    display: flex;
    align-items: center;
}
</style>
