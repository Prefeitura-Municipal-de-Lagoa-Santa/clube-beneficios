<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Gift, Users, Tag, Star, Shield, Settings, LogOut,
    Menu, X, ChevronDown, Home,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import type { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const auth = computed(() => page.props.auth);
const sidebarOpen = ref(false);

const hasRole = (role: string) => auth.value?.user?.roles?.includes(role);
const hasPermission = (perm: string) => auth.value?.user?.permissions?.includes(perm);
const isAdmin = computed(() => hasRole('admin'));

const navItems = computed(() => {
    const items: { title: string; href: string; icon: any; show: boolean }[] = [
        { title: 'Parceiros', href: '/admin/parceiros', icon: Gift, show: isAdmin.value || hasPermission('manage_partners') },
        { title: 'Categorias', href: '/admin/categorias', icon: Tag, show: isAdmin.value || hasPermission('manage_categories') },
        { title: 'Benefícios', href: '/admin/beneficios', icon: Star, show: isAdmin.value || hasPermission('manage_partners') },
        { title: 'Usuários', href: '/admin/usuarios', icon: Users, show: isAdmin.value },
        { title: 'Papéis', href: '/admin/roles', icon: Shield, show: isAdmin.value },
    ];
    return items.filter(i => i.show);
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar (desktop) -->
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 bg-gray-900 text-white lg:block">
            <div class="flex h-16 items-center gap-3 px-6">
                <Settings class="h-6 w-6 text-blue-400" />
                <span class="text-lg font-bold">Admin</span>
            </div>
            <nav class="mt-4 space-y-1 px-3">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white"
                    :class="{ 'bg-gray-800 text-white': $page.url.startsWith(item.href) }"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    {{ item.title }}
                </Link>
            </nav>
            <div class="absolute bottom-0 w-full border-t border-gray-700 p-4">
                <Link href="/beneficios" class="flex items-center gap-2 text-sm text-gray-400 hover:text-white">
                    <Home class="h-4 w-4" />
                    Voltar ao Clube
                </Link>
            </div>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-black/50" />
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white">
                <div class="flex h-16 items-center justify-between px-6">
                    <span class="text-lg font-bold">Admin</span>
                    <button @click="sidebarOpen = false"><X class="h-5 w-5" /></button>
                </div>
                <nav class="mt-4 space-y-1 px-3">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white"
                    >
                        <component :is="item.icon" class="h-5 w-5" />
                        {{ item.title }}
                    </Link>
                </nav>
            </aside>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top bar -->
            <header class="flex h-16 items-center justify-between border-b bg-white px-4 shadow-sm lg:px-8">
                <button class="lg:hidden" @click="sidebarOpen = true">
                    <Menu class="h-6 w-6" />
                </button>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth?.user?.name }}</span>
                    <Link href="/logout" method="post" as="button" class="text-gray-400 hover:text-gray-600">
                        <LogOut class="h-5 w-5" />
                    </Link>
                </div>
            </header>

            <!-- Flash messages -->
            <div v-if="page.props.flash?.success" class="mx-4 mt-4 lg:mx-8">
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ page.props.flash.success }}
                </div>
            </div>
            <div v-if="page.props.flash?.error" class="mx-4 mt-4 lg:mx-8">
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ page.props.flash.error }}
                </div>
            </div>

            <!-- Content -->
            <main class="p-4 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
