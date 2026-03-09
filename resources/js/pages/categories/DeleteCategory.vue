<script setup lang="ts">
import { Form } from '@inertiajs/vue3'
import { ref } from 'vue'
import CategoryController from '@/actions/App/Http/Controllers/CategoryController'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import type { Category } from '@/types'

type Props = {
  category: Category
}
const props = defineProps<Props>()

const isOpen = ref(false)

const onSuccess = () => {
  isOpen.value = false
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger as-child>
      <Button type="button" variant="destructive" size="sm">Delete</Button>
    </DialogTrigger>

    <DialogContent class="sm:max-w-[480px]">
      <DialogHeader>
        <DialogTitle>Delete category?</DialogTitle>
        <DialogDescription>
          This will soft-delete the category. Transactions will still reference
          it.
        </DialogDescription>
      </DialogHeader>

      <div class="text-sm">
        <span class="text-muted-foreground">Selected:</span>
        <span class="ml-2 font-medium">{{ props.category.name }}</span>
        <span class="mx-2 text-muted-foreground">•</span>
        <span class="font-medium capitalize">{{ props.category.type }}</span>
      </div>

      <Form
        :action="
          CategoryController.destroy({
            category: props.category.id as any,
          }).url
        "
        method="delete"
        :options="{ preserveScroll: true }"
        @success="onSuccess"
        v-slot="{ processing }"
      >
        <DialogFooter class="gap-2">
          <DialogClose as-child>
            <Button type="button" variant="outline" :disabled="processing">
              Cancel
            </Button>
          </DialogClose>
          <Button type="submit" variant="destructive" :disabled="processing">
            Delete
          </Button>
        </DialogFooter>
      </Form>
    </DialogContent>
  </Dialog>
</template>
