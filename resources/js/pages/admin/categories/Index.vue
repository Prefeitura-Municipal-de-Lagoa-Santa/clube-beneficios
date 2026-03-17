<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Category, PaginatedData } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    categories: PaginatedData<Category>;
}>();

const destroy = (id: number) => {
    if (confirm('Tem certeza que deseja excluir esta categoria?')) {
        router.delete(`/admin/categorias/${id}`);
    }
};
</script>

<template>
    <Head title="Categorias — Admin" />

    <Link href="/admin" class="mb-4 inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
        <ArrowLeft class="h-4 w-4" />
        Voltar
    </Link>

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Categorias</h1>
        <Link href="/admin/categorias/create" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <Plus class="h-4 w-4" />
            Nova Categoria
        </Link>
    </div>

    <!-- Categories cards (mobile) -->
    <div class="space-y-3 md:hidden">
        <div v-for="category in categories.data" :key="'card-' + category.id" class="rounded-lg bg-white p-4 shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-medium text-gray-900">{{ category.name }}</p>
                    <p class="text-sm text-gray-500">Ordem: {{ category.sort_order }}</p>
                    <p class="text-sm text-gray-500">{{ category.partners_count || 0 }} parceiros</p>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="`/admin/categorias/${category.id}/edit`" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-brand-700">
                        <Pencil class="h-4 w-4" />
                    </Link>
                    <button @click="destroy(category.id)" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <span :class="category.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="rounded-full px-2 py-1 text-xs font-medium">
                    {{ category.active ? 'Ativa' : 'Inativa' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Categories table (desktop) -->
    <div class="hidden overflow-hidden rounded-lg bg-white shadow md:block">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ordem</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Parceiros</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr v-for="category in categories.data" :key="category.id" class="hover:bg-gray-50">
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ category.sort_order }}</td>
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">{{ category.name }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ category.partners_count || 0 }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span :class="category.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="rounded-full px-2 py-1 text-xs font-medium">
                            {{ category.active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        <Link :href="`/admin/categorias/${category.id}/edit`" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-brand-700">
                            <Pencil class="h-4 w-4" />
                        </Link>
                        <button @click="destroy(category.id)" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
