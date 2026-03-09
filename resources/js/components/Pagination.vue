<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Button from '@/components/ui/button/Button.vue'

type PaginationLink = {
  url: string | null
  label: string
  active: boolean
}

type Props = {
  links: PaginationLink[]
}

defineProps<Props>()

const cleanLabel = (label: string) =>
  label
    .replace('&laquo;', '<<')
    .replace('&raquo;', '>>')
    .replace(/<[^>]*>/g, '')
    .trim()
</script>

<template>
  <nav v-if="links.length > 3" class="flex flex-wrap items-center gap-2">
    <template v-for="(link, i) in links" :key="i">
      <Button
        v-if="link.url"
        as-child
        :variant="link.active ? 'default' : 'outline'"
        size="sm"
      >
        <Link :href="link.url" preserve-scroll>
          {{ cleanLabel(link.label) }}
        </Link>
      </Button>
      <Button v-else variant="outline" size="sm" disabled>
        {{ cleanLabel(link.label) }}
      </Button>
    </template>
  </nav>
</template>
