<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Gift, Users, Tag, Star, Shield, Settings, LogOut,
    Menu, X, Home,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import type { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const auth = computed(() => page.props.auth);
const showMobileMenu = ref(false);

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
        <!-- Header -->
        <header class="bg-gray-900 text-white shadow-lg">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="/logo.png" alt="Logo" class="h-8 w-auto" />
                        <span class="text-lg font-bold">Admin</span>
                    </div>

                    <!-- Desktop Nav -->
                    <nav class="hidden items-center gap-4 md:flex">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-800"
                            :class="$page.url.startsWith(item.href) ? 'bg-gray-800 text-white' : 'text-gray-300'"
                        >
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                        <div class="ml-2 flex items-center gap-3 border-l border-gray-700 pl-4">
                            <Link href="/beneficios" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                                <Home class="h-4 w-4" />
                                Clube
                            </Link>
                            <span class="text-sm text-gray-400">{{ auth?.user?.name }}</span>
                            <Link href="/logout" method="post" as="button" class="rounded-md p-2 text-gray-400 hover:bg-gray-800 hover:text-white">
                                <LogOut class="h-4 w-4" />
                            </Link>
                        </div>
                    </nav>

                    <!-- Mobile menu button -->
                    <button class="md:hidden" @click="showMobileMenu = !showMobileMenu">
                        <Menu v-if="!showMobileMenu" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </button>
                </div>

                <!-- Mobile Nav -->
                <nav v-if="showMobileMenu" class="pb-4 md:hidden">
                    <div class="flex flex-col gap-2">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-800"
                            :class="$page.url.startsWith(item.href) ? 'bg-gray-800 text-white' : 'text-gray-300'"
                        >
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                        <div class="mt-2 border-t border-gray-700 pt-2">
                            <Link href="/beneficios" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                                <Home class="h-4 w-4" />
                                Voltar ao Clube
                            </Link>
                            <Link href="/logout" method="post" as="button" class="flex items-center gap-2 rounded-md px-3 py-2 text-left text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                                <LogOut class="h-4 w-4" />
                                Sair
                            </Link>
                        </div>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Flash messages -->
        <div v-if="page.props.flash?.success" class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ page.props.flash.success }}
            </div>
        </div>
        <div v-if="page.props.flash?.error" class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                {{ page.props.flash.error }}
            </div>
        </div>

        <!-- Content -->
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <slot />
        </main>
    </div>
</template>
