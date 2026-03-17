<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Category } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    category?: Category;
}>();

const isEditing = !!props.category;

const form = useForm({
    name: props.category?.name || '',
    description: props.category?.description || '',
    icon: props.category?.icon || '',
    active: props.category?.active ?? true,
    sort_order: props.category?.sort_order ?? 0,
});

const submit = () => {
    if (isEditing) {
        form.put(`/admin/categorias/${props.category!.id}`);
    } else {
        form.post('/admin/categorias');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Categoria' : 'Nova Categoria'" />

    <Link href="/admin/categorias" class="mb-6 inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
        <ArrowLeft class="h-4 w-4" />
        Voltar
    </Link>

    <div class="mx-auto max-w-lg">
        <h1 class="mb-6 text-2xl font-bold text-gray-900">
            {{ isEditing ? 'Editar Categoria' : 'Nova Categoria' }}
        </h1>

        <form @submit.prevent="submit" class="space-y-6 rounded-lg bg-white p-6 shadow">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Nome *</label>
                <input v-model="form.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Descrição</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none" />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Ícone (nome Lucide)</label>
                <input v-model="form.icon" type="text" placeholder="ex: Heart, ShoppingBag" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none" />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Ordem</label>
                <input v-model.number="form.sort_order" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none" />
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input v-model="form.active" type="checkbox" class="rounded border-gray-300 text-brand-700 focus:ring-brand-500" />
                    <span class="text-sm font-medium text-gray-700">Ativa</span>
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <Link href="/admin/categorias" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50">
                    {{ isEditing ? 'Salvar' : 'Criar' }}
                </button>
            </div>
        </form>
    </div>
</template>
