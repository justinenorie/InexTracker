<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import DatePicker from '@/components/DatePicker.vue'
import Button from '@/components/ui/button/Button.vue'

type Props = {
  title?: string
  from?: string | null
  to?: string | null
  submitUrl: (params: { from?: string; to?: string }) => string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Date Range',
  from: null,
  to: null,
})

const fromValue = ref<string>(props.from ?? '')
const toValue = ref<string>(props.to ?? '')

watch(
  () => props.from,
  (v) => {
    fromValue.value = v ?? ''
  },
)

watch(
  () => props.to,
  (v) => {
    toValue.value = v ?? ''
  },
)

const apply = () => {
  router.get(
    props.submitUrl({
      from: fromValue.value || undefined,
      to: toValue.value || undefined,
    }),
    {},
    { preserveScroll: true, preserveState: true },
  )
}

const clear = () => {
  fromValue.value = ''
  toValue.value = ''
  apply()
}
</script>

<template>
  <div class="flex flex-col gap-3 rounded-xl border bg-card p-4">
    <div class="text-sm font-medium">
      {{ title }}
    </div>

    <div class="flex flex-col gap-3 md:flex-row md:items-end">
      <div class="flex-1">
        <label class="mb-1 block text-xs text-muted-foreground">From</label>
        <DatePicker v-model="fromValue" placeholder="Select start date" />
      </div>
      <div class="flex-1">
        <label class="mb-1 block text-xs text-muted-foreground">To</label>
        <DatePicker v-model="toValue" placeholder="Select end date" />
      </div>
      <div class="flex gap-2">
        <Button type="button" @click="apply">Apply</Button>
        <Button type="button" variant="outline" @click="clear"> Clear </Button>
      </div>
    </div>
  </div>
</template>
