<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    activeNav: {
        type: String,
        default: 'Dashboard',
    },
});

const page = usePage();
const user = page.props.auth.user;

const sidebarOpen = ref(false);

const navItems = [
    { name: 'Dashboard', href: '/dashboard', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z' },
    { name: 'Menu', href: '/manager/menu', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', badge: '2 Proposed' },
    { name: 'KDS Menu', href: '/manager/kds', icon: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z' },
    { name: 'Orders', href: '/manager/orders', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
    { name: 'Tables', href: '/manager/tables', icon: 'M4 6h16M4 10h16M4 14h16M4 18h16' },
    { name: 'Inventory', href: '/manager/inventory', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Finance', href: '/manager/finance', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Staffs', href: '/manager/staffs', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' },
];

function isActive(itemName) {
    return itemName === props.activeNav;
}

</script>

<template>
    <div class="min-h-screen bg-[#0b0f17] text-gray-100 flex">
        <!-- Sidebar Backdrop for Mobile -->
        <div 
            v-if="sidebarOpen" 
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm"
        ></div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 w-64 bg-[#11141c]/90 border-r border-gray-800/80 z-50 transform lg:transform-none lg:static transition-transform duration-300 ease-in-out flex flex-col backdrop-blur-md"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo area -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800/50">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                        <span class="text-white font-bold text-sm">R</span>
                    </div>
                    <span class="font-bold text-lg text-white font-outfit tracking-wide">RestoFlow</span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 text-gray-400 hover:text-white rounded-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Items -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <Link 
                    v-for="item in navItems" 
                    :key="item.name"
                    :href="item.href"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all group"
                    :class="isActive(item.name) 
                        ? 'text-emerald-400 bg-emerald-500/10 border-l-4 border-emerald-500 shadow-[inset_0_0_10px_rgba(16,185,129,0.05)]' 
                        : 'text-gray-400 hover:text-white hover:bg-gray-800/40 border-l-4 border-transparent'"
                >
                    <svg 
                        class="w-5 h-5 shrink-0 transition-colors" 
                        :class="isActive(item.name) ? 'text-emerald-400' : 'text-gray-400 group-hover:text-white'" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" />
                    </svg>
                    <span>{{ item.name }}</span>
                    
                    <!-- Badge (e.g. Proposed count) -->
                    <span v-if="item.badge" class="ml-auto bg-amber-500/20 text-amber-400 text-xs px-2 py-0.5 rounded-full font-bold">
                        {{ item.badge }}
                    </span>
                </Link>
            </nav>

            <!-- User Info Area -->
            <div class="p-4 border-t border-gray-800/50 bg-[#161a24]/30">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gray-800 border border-gray-700 flex items-center justify-center font-bold text-emerald-400">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 truncate capitalize">{{ user.role?.name || 'Staff' }}</p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <Link :href="route('profile.edit')" class="flex-1 text-center py-2 text-xs bg-gray-800 hover:bg-gray-700 text-gray-300 font-medium rounded-lg transition-colors border border-gray-700/50">
                        Profile
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="flex-1 text-center py-2 text-xs bg-red-950/30 hover:bg-red-950/60 text-red-400 font-medium rounded-lg transition-colors border border-red-900/30">
                        Log Out
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Mobile header -->
            <header class="h-16 border-b border-gray-800/80 bg-[#11141c]/60 flex items-center justify-between px-6 lg:hidden backdrop-blur-md shrink-0">
                <button @click="sidebarOpen = true" class="p-2 text-gray-400 hover:text-white rounded-lg focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-bold text-white">RestoFlow Dashboard</span>
                <div class="h-8 w-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs border border-emerald-500/30">
                    {{ user.name.charAt(0) }}
                </div>
            </header>

            <!-- Workspace viewport -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <!-- Page Heading (Injected slot) -->
                <slot name="header" />

                <!-- Page content wrapper -->
                <div class="mt-6">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
