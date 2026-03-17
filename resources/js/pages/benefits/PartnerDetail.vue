<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, MapPin, Phone, Mail, Globe, CheckCircle } from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { Partner } from '@/types';

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    partner: Partner;
}>();
</script>

<template>
    <Head :title="partner.name" />

    <Link href="/beneficios" class="mb-6 inline-flex items-center gap-2 text-sm text-brand-700 hover:text-brand-800">
        <ArrowLeft class="h-4 w-4" />
        Voltar para parceiros
    </Link>

    <div class="overflow-hidden rounded-xl bg-white shadow-lg">
        <!-- Header -->
        <div class="flex flex-col items-center gap-6 border-b bg-gray-50 p-8 sm:flex-row">
            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white shadow-md">
                <img
                    v-if="partner.logo_url"
                    :src="`/storage/${partner.logo_url}`"
                    :alt="partner.name"
                    class="max-h-full max-w-full object-contain p-2"
                />
                <span v-else class="text-3xl font-bold text-brand-700">
                    {{ partner.name.charAt(0) }}
                </span>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ partner.name }}</h1>
                    <span v-if="partner.category" class="rounded-full bg-brand-100 px-3 py-1 text-xs font-medium text-brand-700">
                        {{ partner.category.name }}
                    </span>
                </div>
                <p v-if="partner.description" class="mt-2 text-gray-600">{{ partner.description }}</p>

                <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-500">
                    <span v-if="partner.address" class="flex items-center gap-1">
                        <MapPin class="h-4 w-4" />
                        {{ partner.address }}
                    </span>
                    <span v-if="partner.phone" class="flex items-center gap-1">
                        <Phone class="h-4 w-4" />
                        {{ partner.phone }}
                    </span>
                    <a v-if="partner.email" :href="`mailto:${partner.email}`" class="flex items-center gap-1 hover:text-brand-700">
                        <Mail class="h-4 w-4" />
                        {{ partner.email }}
                    </a>
                    <a v-if="partner.website" :href="partner.website" target="_blank" rel="noopener" class="flex items-center gap-1 hover:text-brand-700">
                        <Globe class="h-4 w-4" />
                        Website
                    </a>
                </div>
            </div>
        </div>

        <!-- Benefits -->
        <div class="p-8">
            <h2 class="mb-6 text-xl font-bold text-gray-900">Benefícios</h2>

            <div v-if="partner.benefits?.length" class="space-y-4">
                <div
                    v-for="benefit in partner.benefits"
                    :key="benefit.id"
                    class="flex gap-4 rounded-lg border border-gray-100 p-5 transition-colors hover:bg-gray-50"
                >
                    <CheckCircle class="mt-0.5 h-5 w-5 shrink-0 text-green-500" />
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ benefit.title }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ benefit.description }}</p>
                    </div>
                </div>
            </div>

            <p v-else class="text-gray-500">Nenhum benefício cadastrado para este parceiro.</p>
        </div>
    </div>
</template>
