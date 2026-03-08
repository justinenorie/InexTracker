<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';
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

const isOpen = ref(false);

const nameValue = ref('');
const typeValue = ref<'income' | 'expense' | 'both'>('both');
const colorValue = ref('');

const reset = () => {
    nameValue.value = '';
    typeValue.value = 'both';
    colorValue.value = '';
};

const onSuccess = () => {
    isOpen.value = false;
    reset();
};
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button type="button">Add Category</Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-[520px]">
            <DialogHeader>
                <DialogTitle>New category</DialogTitle>
                <DialogDescription>
                    Categories help you group transactions.
                </DialogDescription>
            </DialogHeader>

            <Form
                :action="CategoryController.store().url"
                method="post"
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
                        placeholder="e.g. Food"
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
                        placeholder="#16a34a"
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
                    <Button type="submit" :disabled="processing">Save</Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
