<script>
export default {
  name: 'TimePicker',
  props: {
    label: {
      type: String,
      default: null
    },
    modelValue: {
      type: String,
      default: null
    }
  },
  emits: [ 'update:model-value' ],
  data() {
    return {
      time: null
    }
  },
  watch: {
    modelValue(v) {
      this.time = v
    },
    time(v) {
      this.$emit('update:model-value', v)
    }
  },
  created() {
    this.time = this.modelValue
  }
}
</script>

<template>
  <VMenu :close-on-content-click="false">
    <template #activator="{props }">
      <slot
        name="trigger"
        :props="props"
      >
        <VTextField
          clearable
          :label="label"
          prepend-inner-icon="mdi-clock"
          v-bind="props"
          :model-value="time"
          @update:model-value="time = $event"
        />
      </slot>
    </template>
    <VTimePicker
      v-model="time"
      format="24hr"
    />
  </VMenu>
</template>

<style scoped lang="scss">

</style>
