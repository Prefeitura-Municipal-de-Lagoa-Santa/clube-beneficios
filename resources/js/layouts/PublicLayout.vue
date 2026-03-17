<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Gift, LogOut, Menu, CreditCard, X, Settings } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import type { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const auth = computed(() => page.props.auth);
const showMobileMenu = ref(false);
const isAdminOrManager = computed(() => {
    const roles = auth.value?.user.roles ?? [];
    return roles.includes('admin') || roles.includes('partner_manager');
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-blue-700 text-white shadow-lg">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Gift class="h-8 w-8" />
                        <Link href="/beneficios" class="text-xl font-bold">
                            Clube de Benefícios
                        </Link>
                    </div>

                    <!-- Desktop Nav -->
                    <nav class="hidden items-center gap-6 md:flex">
                        <Link
                            href="/beneficios"
                            class="rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-600"
                        >
                            Parceiros
                        </Link>
                        <Link
                            href="/carteirinha"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-600"
                        >
                            <CreditCard class="h-4 w-4" />
                            Carteirinha
                        </Link>
                        <Link
                            v-if="isAdminOrManager"
                            href="/admin"
                            class="flex items-center gap-2 rounded-md bg-blue-800 px-3 py-2 text-sm font-medium hover:bg-blue-600"
                        >
                            <Settings class="h-4 w-4" />
                            Administração
                        </Link>
                        <div v-if="auth" class="flex items-center gap-3">
                            <span class="text-sm">{{ auth.user.name }}</span>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="rounded-md p-2 hover:bg-blue-600"
                            >
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
                        <Link href="/beneficios" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-600">
                            Parceiros
                        </Link>
                        <Link href="/carteirinha" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-600">
                            Carteirinha
                        </Link>
                        <Link v-if="isAdminOrManager" href="/admin" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-600">
                            Administração
                        </Link>
                        <Link href="/logout" method="post" as="button" class="rounded-md px-3 py-2 text-left text-sm font-medium hover:bg-blue-600">
                            Sair
                        </Link>
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
