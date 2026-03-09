<script setup lang="ts">
import { Form } from '@inertiajs/vue3'
import { ref } from 'vue'
import TransactionController from '@/actions/App/Http/Controllers/TransactionController'
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
import { useToast } from '@/composables/useToast'
import type { Transaction } from '@/types'

type Props = {
  transaction: Transaction
}
const { success } = useToast()
const props = defineProps<Props>()

const isOpen = ref(false)

const onSuccess = () => {
  isOpen.value = false
  success('Transaction deleted successfully')
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger as-child>
      <Button type="button" variant="destructive" size="sm">Delete</Button>
    </DialogTrigger>

    <DialogContent class="sm:max-w-[480px]">
      <DialogHeader>
        <DialogTitle>Delete transaction?</DialogTitle>
        <DialogDescription>
          This will soft-delete the record. You can restore later if you build a
          restore screen.
        </DialogDescription>
      </DialogHeader>

      <div class="text-sm">
        <span class="text-muted-foreground">Selected:</span>
        <span class="ml-2 font-medium capitalize">{{
          props.transaction.type
        }}</span>
        <span class="mx-2 text-muted-foreground">•</span>
        <span class="font-medium tabular-nums">{{
          props.transaction.amount
        }}</span>
        <span class="mx-2 text-muted-foreground">•</span>
        <span class="font-medium">{{ props.transaction.transacted_at }}</span>
      </div>

      <Form
        :action="
          TransactionController.destroy({
            transaction: props.transaction.id as any,
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
