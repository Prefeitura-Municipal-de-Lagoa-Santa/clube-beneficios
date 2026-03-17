<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, MapPin, Phone, Globe } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Category, Partner, PaginatedData } from '@/types';

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    partners: PaginatedData<Partner>;
    categories: Category[];
    filters: { search: string | null; category: string | null };
}>();

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');

let searchTimeout: ReturnType<typeof setTimeout>;

const applyFilters = () => {
    router.get('/beneficios', {
        search: search.value || undefined,
        category: selectedCategory.value || undefined,
    }, { preserveState: true, replace: true });
};

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch(selectedCategory, applyFilters);
</script>

<template>
    <Head title="Parceiros e Benefícios" />

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Parceiros e Benefícios</h1>
        <p class="mt-2 text-gray-600">Encontre os melhores descontos e vantagens exclusivas para servidores.</p>
    </div>

    <!-- Filters -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row">
        <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
            <input
                v-model="search"
                type="text"
                placeholder="Buscar parceiro..."
                class="w-full rounded-lg border border-gray-300 py-3 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
        </div>
        <select
            v-model="selectedCategory"
            class="rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        >
            <option value="">Todas as categorias</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                {{ cat.name }}
            </option>
        </select>
    </div>

    <!-- Partners Grid -->
    <div v-if="partners.data.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <Link
            v-for="partner in partners.data"
            :key="partner.id"
            :href="`/beneficios/parceiro/${partner.id}`"
            class="group overflow-hidden rounded-xl bg-white shadow-md transition-shadow hover:shadow-lg"
        >
            <div class="flex h-40 items-center justify-center bg-gray-100 p-4">
                <img
                    v-if="partner.logo_url"
                    :src="`/storage/${partner.logo_url}`"
                    :alt="partner.name"
                    class="max-h-full max-w-full object-contain"
                />
                <div v-else class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-2xl font-bold text-blue-600">
                    {{ partner.name.charAt(0) }}
                </div>
            </div>
            <div class="p-5">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">
                        {{ partner.name }}
                    </h3>
                    <span v-if="partner.category" class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                        {{ partner.category.name }}
                    </span>
                </div>
                <p v-if="partner.description" class="mb-3 line-clamp-2 text-sm text-gray-500">
                    {{ partner.description }}
                </p>
                <div class="flex flex-wrap gap-3 text-xs text-gray-400">
                    <span v-if="partner.address" class="flex items-center gap-1">
                        <MapPin class="h-3 w-3" />
                        {{ partner.address }}
                    </span>
                    <span v-if="partner.phone" class="flex items-center gap-1">
                        <Phone class="h-3 w-3" />
                        {{ partner.phone }}
                    </span>
                </div>
                <div v-if="partner.benefits_count" class="mt-3 text-sm font-medium text-blue-600">
                    {{ partner.benefits_count }} benefício{{ partner.benefits_count > 1 ? 's' : '' }}
                </div>
            </div>
        </Link>
    </div>

    <div v-else class="py-12 text-center">
        <p class="text-lg text-gray-500">Nenhum parceiro encontrado.</p>
    </div>

    <!-- Pagination -->
    <div v-if="partners.last_page > 1" class="mt-8 flex justify-center gap-2">
        <Link
            v-for="link in partners.links"
            :key="link.label"
            :href="link.url || ''"
            :class="[
                'rounded-md px-4 py-2 text-sm',
                link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50',
                !link.url ? 'pointer-events-none opacity-50' : ''
            ]"
            v-html="link.label"
        />
    </div>
</template>
