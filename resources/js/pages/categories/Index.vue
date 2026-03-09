<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import CreateCategory from '@/pages/categories/CreateCategory.vue';
import DeleteCategory from '@/pages/categories/DeleteCategory.vue';
import EditCategory from '@/pages/categories/EditCategory.vue';
import { index } from '@/routes/categories';
import type { BreadcrumbItem, Category } from '@/types';

type Props = {
    categories: Category[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Categories', href: index() }];
</script>

<template>
    <Head title="Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
            <Heading
                title="Categories"
                description="Manage categories used to group your income and expenses."
            />

            <div class="rounded-xl border bg-card p-4">
                <div
                    class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="text-sm font-medium">Categories</div>
                    <CreateCategory class="w-full sm:w-auto" />
                </div>

                <div
                    v-if="props.categories.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    No categories yet.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full min-w-[520px] text-sm">
                        <thead class="text-xs text-muted-foreground">
                            <tr>
                                <th class="px-2 py-2 text-left font-medium">
                                    Name
                                </th>
                                <th class="px-2 py-2 text-left font-medium">
                                    Type
                                </th>
                                <th class="px-2 py-2 text-left font-medium">
                                    Color
                                </th>
                                <th class="px-2 py-2 text-right font-medium">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="c in props.categories"
                                :key="c.id"
                                class="hover:bg-muted/50"
                            >
                                <td class="px-2 py-2 font-medium">
                                    {{ c.name }}
                                </td>
                                <td class="px-2 py-2">
                                    <Badge
                                        :variant="
                                            c.type === 'income'
                                                ? 'success'
                                                : c.type === 'expense'
                                                  ? 'destructive'
                                                  : 'warning'
                                        "
                                        class="capitalize"
                                    >
                                        {{ c.type }}
                                    </Badge>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="h-3 w-3 rounded-full border"
                                            :style="{
                                                backgroundColor:
                                                    c.color ?? 'transparent',
                                            }"
                                        />
                                        <span
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ c.color ?? '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="flex justify-end gap-2">
                                        <EditCategory :category="c" />
                                        <DeleteCategory :category="c" />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
