<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, Check, X } from 'lucide-vue-next';
import { ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { Role, Permission } from '@/types';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    roles: (Role & { permissions: Permission[] })[];
    permissions: Permission[];
}>();

const showCreateForm = ref(false);
const editingRoleId = ref<number | null>(null);

const createForm = useForm({
    name: '',
    permissions: [] as string[],
});

const editForm = useForm({
    name: '',
    permissions: [] as string[],
});

const submitCreate = () => {
    createForm.post('/admin/roles', {
        onSuccess: () => {
            createForm.reset();
            showCreateForm.value = false;
        },
    });
};

const startEdit = (role: Role & { permissions: Permission[] }) => {
    editingRoleId.value = role.id;
    editForm.name = role.name;
    editForm.permissions = role.permissions.map(p => p.name);
};

const submitEdit = (roleId: number) => {
    editForm.put(`/admin/roles/${roleId}`, {
        onSuccess: () => { editingRoleId.value = null; },
    });
};

const destroy = (roleId: number) => {
    if (confirm('Tem certeza que deseja excluir este papel?')) {
        router.delete(`/admin/roles/${roleId}`);
    }
};
</script>

<template>
    <Head title="Papéis e Permissões — Admin" />

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Papéis e Permissões</h1>
        <button @click="showCreateForm = !showCreateForm" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <Plus class="h-4 w-4" />
            Novo Papel
        </button>
    </div>

    <!-- Create form -->
    <div v-if="showCreateForm" class="mb-6 rounded-lg bg-white p-6 shadow">
        <h2 class="mb-4 font-semibold text-gray-900">Criar Papel</h2>
        <form @submit.prevent="submitCreate" class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Nome</label>
                <input v-model="createForm.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Permissões</label>
                <div class="flex flex-wrap gap-3">
                    <label v-for="perm in permissions" :key="perm.id" class="flex items-center gap-1">
                        <input type="checkbox" :value="perm.name" v-model="createForm.permissions" class="rounded border-gray-300 text-blue-600" />
                        <span class="text-sm">{{ perm.name }}</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" :disabled="createForm.processing" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Criar</button>
                <button type="button" @click="showCreateForm = false" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-700">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Roles list -->
    <div class="space-y-4">
        <div v-for="role in roles" :key="role.id" class="rounded-lg bg-white p-6 shadow">
            <div v-if="editingRoleId === role.id">
                <form @submit.prevent="submitEdit(role.id)" class="space-y-4">
                    <div>
                        <input v-model="editForm.name" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <label v-for="perm in permissions" :key="perm.id" class="flex items-center gap-1">
                            <input type="checkbox" :value="perm.name" v-model="editForm.permissions" class="rounded border-gray-300 text-blue-600" />
                            <span class="text-sm">{{ perm.name }}</span>
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center gap-1 rounded-md bg-green-600 px-3 py-1 text-xs font-medium text-white hover:bg-green-700">
                            <Check class="h-3 w-3" /> Salvar
                        </button>
                        <button type="button" @click="editingRoleId = null" class="inline-flex items-center gap-1 rounded-md border px-3 py-1 text-xs font-medium text-gray-700">
                            <X class="h-3 w-3" /> Cancelar
                        </button>
                    </div>
                </form>
            </div>
            <div v-else class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ role.name }}</h3>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <span v-for="perm in role.permissions" :key="perm.id" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                            {{ perm.name }}
                        </span>
                        <span v-if="!role.permissions.length" class="text-xs text-gray-400">Sem permissões</span>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button @click="startEdit(role)" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-blue-600">
                        <Pencil class="h-4 w-4" />
                    </button>
                    <button @click="destroy(role.id)" class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600">
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
