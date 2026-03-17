<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Gift, QrCode, ShieldCheck } from 'lucide-vue-next';

defineOptions({ layout: null });
</script>

<template>
    <Head title="Clube de Benefícios" />
    <div class="flex min-h-screen flex-col items-center justify-center px-4" style="background: #212d5d;">
        <div class="mb-6 text-center">
            <img src="/foto_de_capa.png" alt="Clube de Benefícios - Prefeitura Municipal de Lagoa Santa" class="mx-auto w-full max-w-3xl object-contain" />
        </div>

        <div class="mb-8">
            <p class="text-center text-brand-200">
                Acesse descontos e vantagens exclusivas para servidores públicos
            </p>
        </div>

        <div class="grid w-full max-w-2xl gap-6 md:grid-cols-2">
            <Link
                href="/login"
                class="group flex flex-col items-center rounded-2xl bg-white p-8 shadow-xl transition-transform hover:scale-105"
            >
                <ShieldCheck class="mb-4 h-12 w-12 text-brand-700" />
                <h2 class="mb-2 text-xl font-bold text-gray-900">Área do Servidor</h2>
                <p class="text-center text-sm text-gray-500">
                    Acesse seus benefícios e emita sua carteirinha
                </p>
            </Link>

            <div class="flex flex-col items-center rounded-2xl bg-white/10 p-8 text-white backdrop-blur-sm">
                <QrCode class="mb-4 h-12 w-12" />
                <h2 class="mb-2 text-xl font-bold">Verificação</h2>
                <p class="text-center text-sm text-brand-100">
                    Escaneie o QR Code da carteirinha para verificar o status do servidor
                </p>
            </div>
        </div>

        <p class="mt-12 text-sm text-brand-200">
            &copy; {{ new Date().getFullYear() }} Prefeitura Municipal de Lagoa Santa
        </p>
    </div>
</template>
