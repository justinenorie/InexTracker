<script setup lang="ts">
import { Form } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import CategoryController from '@/actions/App/Http/Controllers/CategoryController'
import InputError from '@/components/InputError.vue'
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
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { useToast } from '@/composables/useToast'
import type { Category } from '@/types'

type Props = {
  category: Category
}

const { success } = useToast()
const props = defineProps<Props>()

const isOpen = ref(false)

const nameValue = ref(props.category.name ?? '')
const typeValue = ref<'income' | 'expense' | 'both'>(
  (props.category.type as any) === 'income'
    ? 'income'
    : (props.category.type as any) === 'expense'
      ? 'expense'
      : 'both',
)
const colorValue = ref(props.category.color ?? '')

watch(
  () => props.category,
  (c) => {
    nameValue.value = c.name ?? ''
    typeValue.value =
      (c.type as any) === 'income'
        ? 'income'
        : (c.type as any) === 'expense'
          ? 'expense'
          : 'both'
    colorValue.value = c.color ?? ''
  },
)

const onSuccess = () => {
  isOpen.value = false
  success('Category updated successfully')
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger as-child>
      <Button type="button" variant="outline" size="sm">Edit</Button>
    </DialogTrigger>

    <DialogContent class="sm:max-w-[520px]">
      <DialogHeader>
        <DialogTitle>Edit category</DialogTitle>
        <DialogDescription>
          Update category name, type, or color.
        </DialogDescription>
      </DialogHeader>

      <Form
        :action="
          CategoryController.update({
            category: props.category.id as any,
          }).url
        "
        method="put"
        :options="{ preserveScroll: true }"
        @success="onSuccess"
        v-slot="{ errors, processing }"
        class="space-y-4"
      >
        <div class="grid gap-2">
          <Label htmlFor="name">Name</Label>
          <Input
            id="name"
            v-model="nameValue"
            name="name"
            :disabled="processing"
            required
          />
          <InputError :message="errors.name" />
        </div>

        <div class="grid gap-2">
          <Label>Type</Label>
          <Select v-model="typeValue" name="type" :disabled="processing">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Select type" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="both">Both</SelectItem>
              <SelectItem value="income">Income</SelectItem>
              <SelectItem value="expense">Expense</SelectItem>
            </SelectContent>
          </Select>
          <InputError :message="errors.type" />
        </div>

        <div class="grid gap-2">
          <Label htmlFor="color">Color (optional)</Label>
          <Input
            id="color"
            v-model="colorValue"
            name="color"
            :disabled="processing"
          />
          <InputError :message="errors.color" />
        </div>

        <DialogFooter class="gap-2">
          <DialogClose as-child>
            <Button type="button" variant="outline" :disabled="processing">
              Cancel
            </Button>
          </DialogClose>
          <Button type="submit" :disabled="processing">Save changes</Button>
        </DialogFooter>
      </Form>
    </DialogContent>
  </Dialog>
</template>
