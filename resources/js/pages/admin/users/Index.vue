<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Search, Shield, UserPlus } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { User, Role, PaginatedData } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    users: PaginatedData<User>;
    roles: Role[];
    filters: { search?: string };
    ldapResults?: any[];
    ldapQuery?: string;
}>();

const search = ref(props.filters.search || '');
const ldapSearch = ref('');
const editingUserId = ref<number | null>(null);
const editRoles = ref<string[]>([]);
let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/usuarios', { search: search.value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

const startEditRoles = (user: User) => {
    editingUserId.value = user.id;
    editRoles.value = (user.roles || []).map((r: any) => typeof r === 'string' ? r : r.name);
};

const saveRoles = (userId: number) => {
    router.put(`/admin/usuarios/${userId}`, { roles: editRoles.value }, {
        onSuccess: () => { editingUserId.value = null; },
    });
};

const searchLdap = () => {
    if (ldapSearch.value.length >= 2) {
        router.get('/admin/usuarios/ldap/search', { query: ldapSearch.value }, { preserveState: true });
    }
};

const importLdapUser = (user: any) => {
    router.post('/admin/usuarios/ldap/import', user);
};
</script>

<template>
    <Head title="Usuários — Admin" />

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Usuários</h1>

    <!-- Search users -->
    <div class="mb-4">
        <div class="relative">
            <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
            <input v-model="search" type="text" placeholder="Buscar usuário..." class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
        </div>
    </div>

    <!-- Users table -->
    <div class="mb-8 overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Usuário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Matrícula</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Papéis</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">{{ user.name }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ user.username }}</td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ user.matricula || '—' }}</td>
                    <td class="px-6 py-4">
                        <div v-if="editingUserId === user.id" class="flex flex-wrap gap-2">
                            <label v-for="role in roles" :key="role.id" class="flex items-center gap-1">
                                <input type="checkbox" :value="role.name" v-model="editRoles" class="rounded border-gray-300 text-blue-600" />
                                <span class="text-xs">{{ role.name }}</span>
                            </label>
                        </div>
                        <div v-else class="flex flex-wrap gap-1">
                            <span v-for="role in user.roles" :key="typeof role === 'string' ? role : role.name" class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                {{ typeof role === 'string' ? role : role.name }}
                            </span>
                            <span v-if="!user.roles?.length" class="text-xs text-gray-400">Sem papéis</span>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        <button v-if="editingUserId === user.id" @click="saveRoles(user.id)" class="rounded-md bg-green-600 px-3 py-1 text-xs font-medium text-white hover:bg-green-700">
                            Salvar
                        </button>
                        <button v-else @click="startEditRoles(user)" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-blue-600">
                            <Shield class="h-4 w-4" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- LDAP Import -->
    <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-bold text-gray-900">Importar do Active Directory</h2>
        <div class="mb-4 flex gap-3">
            <input v-model="ldapSearch" type="text" placeholder="Buscar no AD (mín. 2 caracteres)..." class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" @keyup.enter="searchLdap" />
            <button @click="searchLdap" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                Buscar
            </button>
        </div>

        <div v-if="ldapResults?.length" class="space-y-2">
            <div v-for="(result, i) in ldapResults" :key="i" class="flex items-center justify-between rounded-lg border p-3">
                <div>
                    <p class="font-medium text-gray-900">{{ result.name }}</p>
                    <p class="text-sm text-gray-500">{{ result.username }} — {{ result.email }}</p>
                </div>
                <button
                    v-if="!result.already_imported"
                    @click="importLdapUser(result)"
                    class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700"
                >
                    <UserPlus class="h-3 w-3" />
                    Importar
                </button>
                <span v-else class="text-xs text-green-600">Já importado</span>
            </div>
        </div>
    </div>
</template>
