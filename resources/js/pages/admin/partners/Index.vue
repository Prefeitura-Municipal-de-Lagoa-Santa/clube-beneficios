<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Partner, PaginatedData } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    partners: PaginatedData<Partner>;
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');
let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/parceiros', { search: search.value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

const destroy = (id: number) => {
    if (confirm('Tem certeza que deseja excluir este parceiro?')) {
        router.delete(`/admin/parceiros/${id}`);
    }
};
</script>

<template>
    <Head title="Parceiros — Admin" />

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Parceiros</h1>
        <Link href="/admin/parceiros/create" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <Plus class="h-4 w-4" />
            Novo Parceiro
        </Link>
    </div>

    <div class="mb-4">
        <div class="relative">
            <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
            <input
                v-model="search"
                type="text"
                placeholder="Buscar parceiro..."
                class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none"
            />
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Categoria</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Benefícios</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr v-for="partner in partners.data" :key="partner.id" class="hover:bg-gray-50">
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-sm font-bold text-brand-700">
                                <img v-if="partner.logo_url" :src="`/storage/${partner.logo_url}`" class="h-full w-full rounded-lg object-cover" />
                                <span v-else>{{ partner.name.charAt(0) }}</span>
                            </div>
                            <span class="font-medium text-gray-900">{{ partner.name }}</span>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                        {{ partner.category?.name || '—' }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                        {{ partner.benefits_count || 0 }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span :class="partner.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="rounded-full px-2 py-1 text-xs font-medium">
                            {{ partner.active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <Link :href="`/admin/parceiros/${partner.id}/edit`" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-brand-700">
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button @click="destroy(partner.id)" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div v-if="partners.last_page > 1" class="mt-4 flex justify-center gap-2">
        <Link
            v-for="link in partners.links"
            :key="link.label"
            :href="link.url || ''"
            :class="[
                'rounded-md px-3 py-1 text-sm',
                link.active ? 'bg-brand-700 text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                !link.url ? 'pointer-events-none opacity-50' : ''
            ]"
            v-html="link.label"
        />
    </div>
</template>
