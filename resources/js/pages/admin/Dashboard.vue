<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { Users, Building2, Tag, Gift, Shield } from 'lucide-vue-next';
import type { AppPageProps } from '@/types';
import { computed } from 'vue';

defineOptions({ layout: AdminLayout });

const page = usePage<AppPageProps>();
const auth = computed(() => page.props.auth);
const isAdmin = computed(() => auth.value?.user.roles?.includes('admin'));
</script>

<template>
    <div>
        <h1 class="mb-8 text-2xl font-bold text-gray-900">Painel Administrativo</h1>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Parceiros -->
            <Link
                href="/admin/parceiros"
                class="group rounded-lg border bg-white p-6 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-center gap-4">
                    <div class="rounded-lg bg-blue-100 p-3">
                        <Building2 class="h-6 w-6 text-blue-600" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">Parceiros</h2>
                        <p class="text-sm text-gray-500">Gerenciar estabelecimentos parceiros</p>
                    </div>
                </div>
            </Link>

            <!-- Categorias -->
            <Link
                href="/admin/categorias"
                class="group rounded-lg border bg-white p-6 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-center gap-4">
                    <div class="rounded-lg bg-green-100 p-3">
                        <Tag class="h-6 w-6 text-green-600" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-green-600">Categorias</h2>
                        <p class="text-sm text-gray-500">Gerenciar categorias de parceiros</p>
                    </div>
                </div>
            </Link>

            <!-- Benefícios -->
            <Link
                href="/admin/beneficios"
                class="group rounded-lg border bg-white p-6 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-center gap-4">
                    <div class="rounded-lg bg-purple-100 p-3">
                        <Gift class="h-6 w-6 text-purple-600" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600">Benefícios</h2>
                        <p class="text-sm text-gray-500">Gerenciar benefícios dos parceiros</p>
                    </div>
                </div>
            </Link>

            <!-- Usuários (admin only) -->
            <Link
                v-if="isAdmin"
                href="/admin/usuarios"
                class="group rounded-lg border bg-white p-6 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-center gap-4">
                    <div class="rounded-lg bg-orange-100 p-3">
                        <Users class="h-6 w-6 text-orange-600" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-orange-600">Usuários</h2>
                        <p class="text-sm text-gray-500">Gerenciar usuários e permissões</p>
                    </div>
                </div>
            </Link>

            <!-- Roles (admin only) -->
            <Link
                v-if="isAdmin"
                href="/admin/roles"
                class="group rounded-lg border bg-white p-6 shadow-sm transition hover:shadow-md"
            >
                <div class="flex items-center gap-4">
                    <div class="rounded-lg bg-red-100 p-3">
                        <Shield class="h-6 w-6 text-red-600" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 group-hover:text-red-600">Roles</h2>
                        <p class="text-sm text-gray-500">Gerenciar papéis e permissões</p>
                    </div>
                </div>
            </Link>
        </div>
    </div>
</template>
