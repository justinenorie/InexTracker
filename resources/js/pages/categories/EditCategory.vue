<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import CategoryController from '@/actions/App/Http/Controllers/CategoryController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Category = {
    id: string;
    name: string;
    type: 'income' | 'expense' | 'both' | string;
    color?: string | null;
};

type Props = {
    category: Category;
};

const props = defineProps<Props>();

const isOpen = ref(false);

const nameValue = ref(props.category.name ?? '');
const typeValue = ref<'income' | 'expense' | 'both'>(
    (props.category.type as any) === 'income'
        ? 'income'
        : (props.category.type as any) === 'expense'
          ? 'expense'
          : 'both',
);
const colorValue = ref(props.category.color ?? '');

watch(
    () => props.category,
    (c) => {
        nameValue.value = c.name ?? '';
        typeValue.value =
            (c.type as any) === 'income'
                ? 'income'
                : (c.type as any) === 'expense'
                  ? 'expense'
                  : 'both';
        colorValue.value = c.color ?? '';
    },
);

const onSuccess = () => {
    isOpen.value = false;
};
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
                    <select
                        v-model="typeValue"
                        name="type"
                        class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        :disabled="processing"
                        required
                    >
                        <option value="both">Both</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
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
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="processing"
                        >
                            Cancel
                        </Button>
                    </DialogClose>
                    <Button type="submit" :disabled="processing"
                        >Save changes</Button
                    >
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
