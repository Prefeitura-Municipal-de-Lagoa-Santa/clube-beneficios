<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Benefit, PaginatedData } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    benefits: PaginatedData<Benefit>;
    partners: { id: number; name: string }[];
    filters: { search?: string; partner_id?: number };
}>();

const search = ref(props.filters.search || '');
const partnerId = ref(props.filters.partner_id || '');
let searchTimeout: ReturnType<typeof setTimeout>;

const applyFilters = () => {
    router.get('/admin/beneficios', {
        search: search.value || undefined,
        partner_id: partnerId.value || undefined,
    }, { preserveState: true, replace: true });
};

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});
watch(partnerId, applyFilters);

const destroy = (id: number) => {
    if (confirm('Tem certeza que deseja excluir este benefício?')) {
        router.delete(`/admin/beneficios/${id}`);
    }
};
</script>

<template>
    <Head title="Benefícios — Admin" />

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Benefícios</h1>
        <Link href="/admin/beneficios/create" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
            <Plus class="h-4 w-4" />
            Novo Benefício
        </Link>
    </div>

    <div class="mb-4 flex gap-4">
        <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
            <input v-model="search" type="text" placeholder="Buscar..." class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none" />
        </div>
        <select v-model="partnerId" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none">
            <option value="">Todos os parceiros</option>
            <option v-for="p in partners" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Parceiro</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr v-for="benefit in benefits.data" :key="benefit.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ benefit.title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ benefit.partner?.name || '—' }}</td>
                    <td class="px-6 py-4">
                        <span :class="benefit.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="rounded-full px-2 py-1 text-xs font-medium">
                            {{ benefit.active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        <Link :href="`/admin/beneficios/${benefit.id}/edit`" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-brand-700">
                            <Pencil class="h-4 w-4" />
                        </Link>
                        <button @click="destroy(benefit.id)" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
