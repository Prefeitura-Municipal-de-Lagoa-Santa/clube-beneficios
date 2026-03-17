<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CheckCircle, XCircle, AlertTriangle } from 'lucide-vue-next';

defineOptions({ layout: null });

const props = defineProps<{
    found: boolean;
    active: boolean;
    name: string | null;
}>();
</script>

<template>
    <Head title="Verificação de Carteirinha" />
    <div class="flex min-h-screen flex-col items-center justify-center p-4" :class="[
        !found ? 'bg-gray-100' :
        active ? 'bg-gradient-to-br from-green-700 to-green-900' :
        'bg-gradient-to-br from-red-700 to-red-900'
    ]">
        <img src="/foto_de_capa.png" alt="Clube de Benefícios - Prefeitura Municipal de Lagoa Santa" class="mb-6 h-16 w-auto" />

        <div class="w-full max-w-md rounded-2xl bg-white p-10 text-center shadow-2xl">

            <!-- Not found -->
            <template v-if="!found">
                <AlertTriangle class="mx-auto mb-6 h-20 w-20 text-yellow-500" />
                <h1 class="mb-2 text-2xl font-bold text-gray-800">Carteirinha não encontrada</h1>
                <p class="text-gray-500">
                    O QR Code escaneado não corresponde a nenhuma carteirinha válida.
                </p>
            </template>

            <!-- Active -->
            <template v-else-if="active">
                <CheckCircle class="mx-auto mb-6 h-24 w-24 text-green-500" />
                <div class="mb-4 inline-block rounded-full bg-green-100 px-6 py-2">
                    <span class="text-2xl font-bold text-green-700">ATIVO</span>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">{{ name }}</h2>
                <p class="mt-2 text-sm text-gray-500">Servidor público municipal</p>
            </template>

            <!-- Inactive -->
            <template v-else>
                <XCircle class="mx-auto mb-6 h-24 w-24 text-red-500" />
                <div class="mb-4 inline-block rounded-full bg-red-100 px-6 py-2">
                    <span class="text-2xl font-bold text-red-700">INATIVO</span>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">{{ name }}</h2>
                <p class="mt-2 text-sm text-gray-500">Este servidor não está ativo</p>
            </template>

            <p class="mt-8 text-xs text-gray-400">
                Prefeitura Municipal de Lagoa Santa — Clube de Benefícios
            </p>
        </div>
    </div>
</template>
