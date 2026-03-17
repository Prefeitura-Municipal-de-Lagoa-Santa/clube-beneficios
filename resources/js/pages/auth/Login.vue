<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Gift } from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({ layout: null });

const form = useForm({
    username: '',
    password: '',
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-600 to-brand-800 px-4">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mb-4 flex justify-center">     
                    <img src="/foto_de_capa.png" alt="Clube de Benefícios - Prefeitura Municipal de Lagoa Santa" class="mx-auto w-full max-w-md object-contain" />
                </div>
                                <p class="mt-1 text-brand-200">Acesse com suas credenciais de rede</p>
            </div>

            <form @submit.prevent="submit" class="rounded-2xl bg-white p-8 shadow-xl">
                <div class="mb-6">
                    <label for="username" class="mb-2 block text-sm font-medium text-gray-700">
                        Usuário
                    </label>
                    <input
                        id="username"
                        v-model="form.username"
                        type="text"
                        autocomplete="username"
                        required
                        autofocus
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none"
                        placeholder="Seu usuário de rede"
                    />
                    <p v-if="form.errors.username" class="mt-1 text-sm text-red-600">
                        {{ form.errors.username }}
                    </p>
                </div>

                <div class="mb-6">
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                        Senha
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500 focus:outline-none"
                        placeholder="Sua senha de rede"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                        {{ form.errors.password }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-brand-700 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                >
                    <span v-if="form.processing">Entrando...</span>
                    <span v-else>Entrar</span>
                </button>
            </form>
        </div>
    </div>
</template>
