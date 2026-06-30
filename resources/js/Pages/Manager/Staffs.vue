<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const roleFilter = ref('all');

const staffList = ref([
    { id: 1, name: 'Alice Owner', email: 'owner@gourmethub.com', role: 'restaurant-owner', roleLabel: 'Owner', status: 'active', lastSeen: 'Online', avatar: 'AO', kitchenStation: null },
    { id: 2, name: 'Bob Manager', email: 'manager@gourmethub.com', role: 'manager', roleLabel: 'Branch Manager', status: 'active', lastSeen: 'Online', avatar: 'BM', kitchenStation: null },
    { id: 3, name: 'Charlie Waiter', email: 'waiter@gourmethub.com', role: 'waiter', roleLabel: 'Waiter', status: 'active', lastSeen: '5m ago', avatar: 'CW', kitchenStation: null },
    { id: 4, name: 'David Chef', email: 'chef@gourmethub.com', role: 'chef', roleLabel: 'Head Chef', status: 'active', lastSeen: 'Online', avatar: 'DC', kitchenStation: 'Italian Kitchen' },
    { id: 5, name: 'Hassan Chef', email: 'hassan@gourmethub.com', role: 'chef', roleLabel: 'Chef', status: 'active', lastSeen: 'Online', avatar: 'HC', kitchenStation: 'Ethiopian Kitchen' },
    { id: 6, name: 'Emma Cashier', email: 'cashier@gourmethub.com', role: 'cashier', roleLabel: 'Cashier', status: 'active', lastSeen: '1h ago', avatar: 'EC', kitchenStation: null },
    { id: 7, name: 'Fred Storekeeper', email: 'inventory@gourmethub.com', role: 'inventory-manager', roleLabel: 'Inventory Manager', status: 'active', lastSeen: '30m ago', avatar: 'FS', kitchenStation: null },
    { id: 8, name: 'Grace Accountant', email: 'accountant@gourmethub.com', role: 'accountant', roleLabel: 'Accountant', status: 'inactive', lastSeen: 'Yesterday', avatar: 'GA', kitchenStation: null },
]);

const roleColors = {
    'restaurant-owner': 'bg-purple-500/20 text-purple-400',
    'manager': 'bg-blue-500/20 text-blue-400',
    'waiter': 'bg-cyan-500/20 text-cyan-400',
    'chef': 'bg-amber-500/20 text-amber-400',
    'cashier': 'bg-emerald-500/20 text-emerald-400',
    'inventory-manager': 'bg-orange-500/20 text-orange-400',
    'accountant': 'bg-pink-500/20 text-pink-400',
};

const avatarColors = [
    'from-purple-600 to-purple-800',
    'from-blue-600 to-blue-800',
    'from-cyan-600 to-cyan-800',
    'from-amber-600 to-amber-800',
    'from-emerald-600 to-emerald-800',
    'from-orange-600 to-orange-800',
    'from-pink-600 to-pink-800',
    'from-teal-600 to-teal-800',
];

const filteredStaff = () => roleFilter.value === 'all' ? staffList.value : staffList.value.filter(s => s.role === roleFilter.value);
</script>

<template>
    <Head title="Staff Management" />

    <AuthenticatedLayout active-nav="Staffs">
        <template #header>
            <PageHeader title="Staff Management" :breadcrumbs="[{ label: 'Dashboard', url: route('dashboard') }, { label: 'Staffs' }]">
                <template #actions>
                    <button class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Staff Member
                    </button>
                </template>
            </PageHeader>
        </template>

        <!-- Role Filter -->
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <button
                v-for="role in ['all', 'chef', 'waiter', 'cashier', 'manager']"
                :key="role"
                @click="roleFilter = role"
                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all border capitalize"
                :class="roleFilter === role
                    ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'
                    : 'text-gray-400 border-gray-700/50 hover:text-white hover:border-gray-600 bg-transparent'"
            >
                {{ role === 'all' ? 'All Roles' : role }}
            </button>
        </div>

        <!-- Staff Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <div 
                v-for="(staff, idx) in filteredStaff()" 
                :key="staff.id"
                class="bg-[#11141c]/70 border border-gray-800/80 rounded-2xl p-5 backdrop-blur-md flex flex-col items-center text-center hover:border-gray-700 transition-all group"
            >
                <!-- Avatar -->
                <div class="relative mb-4">
                    <div 
                        class="h-16 w-16 rounded-2xl bg-gradient-to-br flex items-center justify-center text-white font-bold text-xl shadow-lg" 
                        :class="avatarColors[idx % avatarColors.length]"
                    >
                        {{ staff.avatar }}
                    </div>
                    <span 
                        class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-[#11141c]"
                        :class="staff.status === 'active' ? 'bg-emerald-400' : 'bg-gray-600'"
                    ></span>
                </div>

                <p class="font-bold text-white text-base leading-tight">{{ staff.name }}</p>
                <p class="text-gray-500 text-xs mt-0.5">{{ staff.email }}</p>

                <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="roleColors[staff.role] || 'bg-gray-700 text-gray-400'">
                        {{ staff.roleLabel }}
                    </span>
                    <span v-if="staff.kitchenStation" class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        {{ staff.kitchenStation }}
                    </span>
                </div>

                <p class="text-xs text-gray-600 mt-3">{{ staff.lastSeen }}</p>

                <!-- Actions -->
                <div class="mt-4 flex gap-2 w-full opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="flex-1 py-2 text-xs font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 rounded-lg border border-gray-700/50 transition-colors">Edit</button>
                    <button class="flex-1 py-2 text-xs font-medium text-red-400 bg-red-950/20 hover:bg-red-950/40 rounded-lg border border-red-900/20 transition-colors">Deactivate</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
