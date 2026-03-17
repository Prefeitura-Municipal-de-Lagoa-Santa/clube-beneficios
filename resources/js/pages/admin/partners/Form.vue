<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Partner, Category } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    partner?: Partner;
    categories: Category[];
}>();

const isEditing = !!props.partner;

const form = useForm({
    category_id: props.partner?.category_id || '',
    name: props.partner?.name || '',
    cnpj: props.partner?.cnpj || '',
    logo: null as File | null,
    address: props.partner?.address || '',
    phone: props.partner?.phone || '',
    email: props.partner?.email || '',
    website: props.partner?.website || '',
    description: props.partner?.description || '',
    active: props.partner?.active ?? true,
});

const logoPreview = ref(props.partner?.logo_url ? `/storage/${props.partner.logo_url}` : '');

const handleLogoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    if (isEditing) {
        form.post(`/admin/parceiros/${props.partner!.id}`, {
            forceFormData: true,
            _method: 'PUT',
        } as any);
    } else {
        form.post('/admin/parceiros', { forceFormData: true });
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar Parceiro' : 'Novo Parceiro'" />

    <Link href="/admin/parceiros" class="mb-6 inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
        <ArrowLeft class="h-4 w-4" />
        Voltar
    </Link>

    <div class="mx-auto max-w-2xl">
        <h1 class="mb-6 text-2xl font-bold text-gray-900">
            {{ isEditing ? 'Editar Parceiro' : 'Novo Parceiro' }}
        </h1>

        <form @submit.prevent="submit" class="space-y-6 rounded-lg bg-white p-6 shadow">
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nome *</label>
                    <input v-model="form.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Categoria *</label>
                    <select v-model="form.category_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="" disabled>Selecione...</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">CNPJ</label>
                    <input v-model="form.cnpj" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Logo</label>
                    <input type="file" accept="image/*" @change="handleLogoChange" class="w-full text-sm" />
                    <img v-if="logoPreview" :src="logoPreview" class="mt-2 h-20 w-20 rounded-lg object-cover" />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Endereço</label>
                    <input v-model="form.address" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Telefone</label>
                    <input v-model="form.phone" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Website</label>
                    <input v-model="form.website" type="url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2">
                        <input v-model="form.active" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm font-medium text-gray-700">Ativo</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link href="/admin/parceiros" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                    {{ isEditing ? 'Salvar' : 'Cadastrar' }}
                </button>
            </div>
        </form>
    </div>
</template>
