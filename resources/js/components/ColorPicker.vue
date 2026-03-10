<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { computed } from 'vue'
import { Input } from '@/components/ui/input'
import { cn } from '@/lib/utils'

const props = defineProps<{
  modelValue?: string
  name?: string
  disabled?: boolean
  placeholder?: string
  id?: string
  class?: HTMLAttributes['class']
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const isValidHex = (value: string) =>
  /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(value)

const normalizeHex = (value: string) =>
  (value.startsWith('#') ? value : `#${value}`).toLowerCase()

const textValue = computed({
  get: () => props.modelValue ?? '',
  set: (val) => emit('update:modelValue', val),
})

const colorValue = computed({
  get: () => {
    const value = props.modelValue ?? ''
    return isValidHex(value) ? normalizeHex(value) : '#000000'
  },
  set: (val) => emit('update:modelValue', normalizeHex(val)),
})
</script>

<template>
  <div
    :class="cn('flex flex-col gap-3 sm:flex-row sm:items-center', props.class)"
  >
    <input
      v-model="colorValue"
      type="color"
      :disabled="disabled"
      aria-label="Pick a color"
      class="h-10 w-full rounded-md border border-input bg-transparent p-1 shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 sm:h-9 sm:w-12"
    />
    <Input
      :id="id"
      v-model="textValue"
      :name="name"
      :disabled="disabled"
      :placeholder="placeholder"
      class="flex-1"
    />
  </div>
</template>
