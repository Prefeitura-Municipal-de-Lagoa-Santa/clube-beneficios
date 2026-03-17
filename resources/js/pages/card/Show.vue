<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { CreditCard, Download } from 'lucide-vue-next';
import { computed } from 'vue';
import QrcodeVue from 'qrcode.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { MemberCard, AppPageProps } from '@/types';

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    card: MemberCard | null;
    appUrl: string;
}>();

const page = usePage<AppPageProps>();
const user = computed(() => page.props.auth?.user);

const verificationUrl = computed(() => {
    if (!props.card) return '';
    return `${props.appUrl}/verificar/${props.card.token}`;
});

const form = useForm({});

const generateCard = () => {
    form.post('/carteirinha');
};

const printCard = () => {
    window.print();
};
</script>

<template>
    <Head title="Minha Carteirinha" />

    <div class="mx-auto max-w-lg">
        <h1 class="mb-8 text-center text-3xl font-bold text-gray-900 print:hidden">Minha Carteirinha</h1>

        <!-- Card -->
        <div v-if="card" class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 to-brand-800 p-8 text-white shadow-xl print:shadow-none" id="member-card">
            <div class="mb-6 flex items-center gap-3">
                <img src="/foto_de_capa.png" alt="Clube de Benefícios - Prefeitura Municipal de Lagoa Santa" class="h-12 w-45" />
                
            </div>

            <div class="flex flex-col items-center gap-6 sm:flex-row">
                <div class="flex-1">
                    <p class="text-sm text-brand-200">Nome</p>
                    <p class="text-xl font-bold">{{ user?.name }}</p>
                    <!--p class="mt-3 text-sm text-brand-200">Matrícula</p>
                    <p class="text-lg font-semibold">{{ user?.matricula || '—' }}</p-->
                </div>

                <div class="rounded-xl bg-white p-3">
                    <QrcodeVue :value="verificationUrl" :size="140" level="M" />
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-brand-300">
                Emitida em {{ new Date(card.issued_at).toLocaleDateString('pt-BR') }}
            </p>
        </div>

        <!-- Generate button -->
        <div v-else class="rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
            <CreditCard class="mx-auto mb-4 h-16 w-16 text-gray-400" />
            <h2 class="mb-2 text-xl font-semibold text-gray-700">Carteirinha não gerada</h2>
            <p class="mb-6 text-gray-500">Clique abaixo para gerar sua carteirinha digital do Clube de Benefícios.</p>
            <button
                @click="generateCard"
                :disabled="form.processing"
                class="rounded-lg bg-brand-700 px-8 py-3 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50"
            >
                Gerar Carteirinha
            </button>
        </div>

        <!-- Print button -->
        <div v-if="card" class="mt-6 text-center print:hidden">
            <button
                @click="printCard"
                class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-200"
            >
                <Download class="h-4 w-4" />
                Imprimir Carteirinha
            </button>
        </div>
    </div>
</template>
