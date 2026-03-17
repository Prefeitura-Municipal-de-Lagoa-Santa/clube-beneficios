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
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-600 to-blue-800 px-4">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="rounded-full bg-white/20 p-4">
                        <Gift class="h-10 w-10 text-white" />
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-white">Clube de Benefícios</h1>
                <p class="mt-1 text-blue-200">Acesse com suas credenciais de rede</p>
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
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
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
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="Sua senha de rede"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                        {{ form.errors.password }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                >
                    <span v-if="form.processing">Entrando...</span>
                    <span v-else>Entrar</span>
                </button>
            </form>
        </div>
    </div>
</template>
