<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Benefit } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    benefit?: Benefit;
    partners: { id: number; name: string }[];
    partnerId?: number;
}>();

const isEditing = !!props.benefit;

const form = useForm({
    partner_id: props.benefit?.partner_id || props.partnerId || '',
    title: props.benefit?.title || '',
    description: props.benefit?.description || '',
    active: props.benefit?.active ?? true,
    sort_order: props.benefit?.sort_order ?? 0,
});

const submit = () => {
    if (isEditing) {
        form.put(`/admin/beneficios/${props.benefit!.id}`);
    } else {
        form.post('/admin/beneficios');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Benefício' : 'Novo Benefício'" />

    <Link href="/admin/beneficios" class="mb-6 inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
        <ArrowLeft class="h-4 w-4" />
        Voltar
    </Link>

    <div class="mx-auto max-w-lg">
        <h1 class="mb-6 text-2xl font-bold text-gray-900">
            {{ isEditing ? 'Editar Benefício' : 'Novo Benefício' }}
        </h1>

        <form @submit.prevent="submit" class="space-y-6 rounded-lg bg-white p-6 shadow">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Parceiro *</label>
                <select v-model="form.partner_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="" disabled>Selecione...</option>
                    <option v-for="p in partners" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
                <p v-if="form.errors.partner_id" class="mt-1 text-sm text-red-600">{{ form.errors.partner_id }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Título *</label>
                <input v-model="form.title" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Descrição *</label>
                <textarea v-model="form.description" rows="4" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Ordem</label>
                <input v-model.number="form.sort_order" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input v-model="form.active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    <span class="text-sm font-medium text-gray-700">Ativo</span>
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <Link href="/admin/beneficios" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                    {{ isEditing ? 'Salvar' : 'Criar' }}
                </button>
            </div>
        </form>
    </div>
</template>
