<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TableStatusGrid from '@/Components/TableStatusGrid.vue';
import WaiterAlertBanner from '@/Components/WaiterAlertBanner.vue';
import SlideModal from '@/Components/SlideModal.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, computed, watch } from 'vue';

// ─── Alerts ──────────────────────────────────────────────────────────────────
const alerts = ref([
    { id: 1, table: 'T-03', message: 'Needs Assistance', type: 'warning', time: '1m ago' },
]);
function dismissAlert(id) { alerts.value = alerts.value.filter(a => a.id !== id); }

// ─── Waiters list ─────────────────────────────────────────────────────────────
// In production this would come from Inertia props
const allWaiters = ref([
    { id: 1, name: 'Charlie Waiter', avatar: 'CW', status: 'active', area: 'Ground Floor' },
    { id: 2, name: 'Amina Hassan',   avatar: 'AH', status: 'active', area: 'Rooftop' },
    { id: 3, name: 'Liya Tesfaye',   avatar: 'LT', status: 'active', area: 'Ground Floor' },
    { id: 4, name: 'Samuel Bekele',  avatar: 'SB', status: 'active', area: 'VIP Lounge' },
    { id: 5, name: 'Meron Alemu',    avatar: 'MA', status: 'active', area: 'Outdoor Terrace' },
]);

// ─── Tables ───────────────────────────────────────────────────────────────────
const tables = ref([
    { id: 1,  number: '01', name: 'T-01',           area: 'Ground Floor',    capacity: 4, status: 'occupied', timeSeated: '01:12', activeOrder: true,  waiter: 'Charlie Waiter', waiterId: 1 },
    { id: 2,  number: '02', name: 'T-02',           area: 'Ground Floor',    capacity: 4, status: 'empty',    timeSeated: null,    activeOrder: false, waiter: 'Liya Tesfaye',   waiterId: 3 },
    { id: 3,  number: '03', name: 'T-03',           area: 'Ground Floor',    capacity: 4, status: 'ordering', timeSeated: '00:45', activeOrder: false, waiter: 'Charlie Waiter', waiterId: 1 },
    { id: 4,  number: '04', name: 'T-04',           area: 'Ground Floor',    capacity: 4, status: 'occupied', timeSeated: '00:15', activeOrder: true,  waiter: 'Liya Tesfaye',   waiterId: 3 },
    { id: 5,  number: '05', name: 'T-05',           area: 'Ground Floor',    capacity: 4, status: 'cleaning', timeSeated: null,    activeOrder: false, waiter: null,              waiterId: null },
    { id: 11, number: '11', name: 'T-11 (Rooftop)', area: 'Rooftop',         capacity: 6, status: 'occupied', timeSeated: '02:05', activeOrder: true,  waiter: 'Amina Hassan',   waiterId: 2 },
    { id: 12, number: '12', name: 'T-12 (Rooftop)', area: 'Rooftop',         capacity: 6, status: 'empty',    timeSeated: null,    activeOrder: false, waiter: 'Amina Hassan',   waiterId: 2 },
    { id: 13, number: '13', name: 'T-13 (Rooftop)', area: 'Rooftop',         capacity: 6, status: 'empty',    timeSeated: null,    activeOrder: false, waiter: null,              waiterId: null },
]);

const selectedTable = ref(null);
const areas = ['Ground Floor', 'Rooftop', 'VIP Lounge', 'Outdoor Terrace'];
function onTableClick(table) { selectedTable.value = { ...table }; }

// ─── ADD TABLE MODAL ──────────────────────────────────────────────────────────
const showAddTableModal = ref(false);
const newTable = reactive({
    number: '', area: 'Ground Floor', capacity: 4, qrCode: '',
    selectedWaiterIds: [],        // multi-waiter selection
    selectedExistingTableIds: [], // bulk-assign waiters to other tables in same area
});
const addTableErrors = reactive({});

// Waiters already assigned in the same area (for info display)
const waitersInArea = computed(() =>
    allWaiters.value.filter(w => w.area === newTable.area)
);

// Tables already in the selected area (so manager can see who covers what)
const tablesInSameArea = computed(() =>
    tables.value.filter(t => t.area === newTable.area)
);

// Waiter assignment summary (how many tables each waiter already covers in area)
const waiterLoadInArea = computed(() => {
    const load = {};
    allWaiters.value.forEach(w => { load[w.id] = 0; });
    tables.value.filter(t => t.area === newTable.area && t.waiterId).forEach(t => {
        if (load[t.waiterId] !== undefined) load[t.waiterId]++;
    });
    return load;
});

function openAddTableModal() {
    Object.assign(newTable, {
        number: '', area: 'Ground Floor', capacity: 4, qrCode: '',
        selectedWaiterIds: [], selectedExistingTableIds: [],
    });
    Object.keys(addTableErrors).forEach(k => delete addTableErrors[k]);
    showAddTableModal.value = true;
}

function toggleWaiter(id) {
    const idx = newTable.selectedWaiterIds.indexOf(id);
    if (idx === -1) newTable.selectedWaiterIds.push(id);
    else newTable.selectedWaiterIds.splice(idx, 1);
}

function toggleExistingTable(id) {
    const idx = newTable.selectedExistingTableIds.indexOf(id);
    if (idx === -1) newTable.selectedExistingTableIds.push(id);
    else newTable.selectedExistingTableIds.splice(idx, 1);
}

function selectAllTablesInArea() {
    newTable.selectedExistingTableIds = tablesInSameArea.value.map(t => t.id);
}

function clearTableSelection() {
    newTable.selectedExistingTableIds = [];
}

function applyWaiterAssignment(tableIds, waiterIds) {
    const primaryWaiterId = waiterIds[0] ?? null;
    const primaryWaiter   = allWaiters.value.find(w => w.id === primaryWaiterId);

    tableIds.forEach(tableId => {
        const idx = tables.value.findIndex(t => t.id === tableId);
        if (idx === -1) return;
        tables.value[idx] = {
            ...tables.value[idx],
            waiterId: primaryWaiterId,
            waiter: primaryWaiter?.name ?? null,
            assignedWaiterIds: [...waiterIds],
        };
    });
}

function validateTable() {
    Object.keys(addTableErrors).forEach(k => delete addTableErrors[k]);
    if (!newTable.number.trim())            addTableErrors.number   = 'Table number/name is required.';
    if (!newTable.capacity || newTable.capacity < 1) addTableErrors.capacity = 'Capacity must be at least 1.';
    return Object.keys(addTableErrors).length === 0;
}

function submitAddTable() {
    if (!validateTable()) return;
    const id   = Date.now();
    const name = `T-${newTable.number}${newTable.area !== 'Ground Floor' ? ` (${newTable.area})` : ''}`;
    const qr   = newTable.qrCode || `QR_T${newTable.number}_${newTable.area.replace(/\s+/g,'_').toUpperCase()}`;

    const primaryWaiterId = newTable.selectedWaiterIds[0] ?? null;
    const primaryWaiter   = allWaiters.value.find(w => w.id === primaryWaiterId);

    tables.value.push({
        id, number: newTable.number, name, area: newTable.area,
        capacity: parseInt(newTable.capacity),
        status: 'empty', timeSeated: null, activeOrder: false,
        qrCode: qr,
        waiter: primaryWaiter?.name ?? null,
        waiterId: primaryWaiterId,
        assignedWaiterIds: [...newTable.selectedWaiterIds],
    });

    // Bulk-assign selected waiters to other tables in the same dining area
    if (newTable.selectedWaiterIds.length && newTable.selectedExistingTableIds.length) {
        applyWaiterAssignment(newTable.selectedExistingTableIds, newTable.selectedWaiterIds);
    }

    showAddTableModal.value = false;
    selectedTable.value = null;
}

// ─── EDIT TABLE MODAL ─────────────────────────────────────────────────────────
const showEditTableModal = ref(false);
const editTable = reactive({});

function openEditTable() {
    if (!selectedTable.value) return;
    Object.assign(editTable, {
        ...selectedTable.value,
        selectedWaiterIds: selectedTable.value.assignedWaiterIds ? [...selectedTable.value.assignedWaiterIds]
                          : selectedTable.value.waiterId ? [selectedTable.value.waiterId] : [],
    });
    showEditTableModal.value = true;
}

function toggleEditWaiter(id) {
    const idx = editTable.selectedWaiterIds.indexOf(id);
    if (idx === -1) editTable.selectedWaiterIds.push(id);
    else editTable.selectedWaiterIds.splice(idx, 1);
}

const editWaitersInArea = computed(() =>
    allWaiters.value.filter(w => w.area === editTable.area)
);

function submitEditTable() {
    const idx = tables.value.findIndex(t => t.id === editTable.id);
    if (idx !== -1) {
        const primaryWaiterId = editTable.selectedWaiterIds[0] ?? null;
        const primaryWaiter   = allWaiters.value.find(w => w.id === primaryWaiterId);
        const updated = {
            ...tables.value[idx],
            number: editTable.number,
            area:   editTable.area,
            capacity: parseInt(editTable.capacity),
            name: `T-${editTable.number}${editTable.area !== 'Ground Floor' ? ` (${editTable.area})` : ''}`,
            waiterId: primaryWaiterId,
            waiter: primaryWaiter?.name ?? null,
            assignedWaiterIds: [...editTable.selectedWaiterIds],
        };
        tables.value[idx] = updated;
        selectedTable.value = { ...updated };
    }
    showEditTableModal.value = false;
}

// ─── DELETE TABLE ─────────────────────────────────────────────────────────────
function deleteTable() {
    if (!selectedTable.value) return;
    tables.value = tables.value.filter(t => t.id !== selectedTable.value.id);
    selectedTable.value = null;
}

// Clear bulk table selection when dining area changes
watch(() => newTable.area, () => {
    newTable.selectedExistingTableIds = [];
});

// Avatar gradient colours
const avatarGrads = ['from-emerald-600 to-emerald-800','from-blue-600 to-blue-800','from-purple-600 to-purple-800','from-amber-600 to-amber-800','from-pink-600 to-pink-800'];
</script>

<template>
    <Head title="Table Management" />

    <AuthenticatedLayout active-nav="Tables">
        <WaiterAlertBanner :alerts="alerts" @dismiss="dismissAlert" @action="dismissAlert" />

        <template #header>
            <PageHeader title="Table Management" :breadcrumbs="[{ label: 'Dashboard', url: route('dashboard') }, { label: 'Tables' }]">
                <template #actions>
                    <button @click="openAddTableModal" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Table
                    </button>
                </template>
            </PageHeader>
        </template>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Table Grid -->
            <div class="xl:col-span-2">
                <TableStatusGrid :tables="tables" @table-click="onTableClick" />
            </div>

            <!-- Detail Panel -->
            <div class="xl:col-span-1">
                <div v-if="selectedTable" class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-6 backdrop-blur-md sticky top-8">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ selectedTable.name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ selectedTable.area }}</p>
                        </div>
                        <button @click="selectedTable = null" class="text-gray-500 hover:text-white p-1 rounded-lg">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-3 text-sm mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Capacity</span>
                            <span class="text-white font-semibold">{{ selectedTable.capacity }} Guests</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span class="font-semibold capitalize" :class="{
                                'text-emerald-400': selectedTable.status === 'occupied',
                                'text-blue-400':    selectedTable.status === 'ordering',
                                'text-amber-400':   selectedTable.status === 'cleaning',
                                'text-gray-400':    selectedTable.status === 'empty',
                            }">{{ selectedTable.status }}</span>
                        </div>
                        <div v-if="selectedTable.timeSeated" class="flex justify-between">
                            <span class="text-gray-500">Time Seated</span>
                            <span class="font-mono text-white">{{ selectedTable.timeSeated }}</span>
                        </div>
                        <!-- Waiter Assignment -->
                        <div class="flex justify-between items-start">
                            <span class="text-gray-500 shrink-0">Assigned Waiter</span>
                            <span v-if="selectedTable.waiter" class="text-right">
                                <span class="text-white font-semibold text-xs">{{ selectedTable.waiter }}</span>
                                <span v-if="selectedTable.assignedWaiterIds && selectedTable.assignedWaiterIds.length > 1" class="block text-gray-500 text-xs mt-0.5">
                                    +{{ selectedTable.assignedWaiterIds.length - 1 }} more
                                </span>
                            </span>
                            <span v-else class="text-gray-600 text-xs italic">Unassigned</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <button class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-xl transition-all">Open New Session</button>
                        <button @click="openEditTable" class="w-full py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-200 text-sm font-medium rounded-xl transition-all border border-gray-700/50">Edit Table & Waiter</button>
                        <button v-if="selectedTable.status === 'empty'" @click="deleteTable" class="w-full py-2.5 bg-red-950/30 hover:bg-red-950/60 text-red-400 text-sm font-medium rounded-xl transition-all border border-red-900/30">Remove Table</button>
                    </div>
                </div>

                <div v-else class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-8 backdrop-blur-md text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                    <p class="text-gray-400 text-sm font-medium">Click a table to view details</p>
                    <p class="text-gray-600 text-xs mt-1">Edit, assign waiter, or open sessions here.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- ══════════════════════════════════════════════════════════════════════
         MODAL: ADD TABLE
    ══════════════════════════════════════════════════════════════════════════ -->
    <SlideModal :show="showAddTableModal" title="Add New Table" width="max-w-lg" @close="showAddTableModal = false">
        <div class="space-y-5">
            <!-- Row 1: Number + Area -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Table Number <span class="text-red-400">*</span></label>
                    <input v-model="newTable.number" type="text" placeholder="e.g. 06 or VIP-01"
                        class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm placeholder-gray-600 focus:ring-blue-500 focus:border-blue-500" />
                    <p v-if="addTableErrors.number" class="text-red-400 text-xs mt-1">{{ addTableErrors.number }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Dining Area <span class="text-red-400">*</span></label>
                    <select v-model="newTable.area" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option v-for="a in areas" :key="a" :value="a">{{ a }}</option>
                    </select>
                </div>
            </div>

            <!-- Capacity + QR -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Seating Capacity <span class="text-red-400">*</span></label>
                    <input v-model="newTable.capacity" type="number" min="1" max="30" placeholder="4"
                        class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500" />
                    <p v-if="addTableErrors.capacity" class="text-red-400 text-xs mt-1">{{ addTableErrors.capacity }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">QR Code <span class="text-gray-600">(optional)</span></label>
                    <input v-model="newTable.qrCode" type="text" placeholder="auto-generated"
                        class="w-full bg-gray-900 border border-gray-700 text-gray-300 rounded-xl px-4 py-2.5 text-sm placeholder-gray-600 font-mono focus:ring-blue-500 focus:border-blue-500" />
                </div>
            </div>

            <!-- ─── WAITER ASSIGNMENT ─────────────────────────────────────── -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-400">Assign Waiter(s) for <span class="text-blue-400">{{ newTable.area }}</span></label>
                    <span v-if="newTable.selectedWaiterIds.length" class="text-xs text-emerald-400 font-bold">{{ newTable.selectedWaiterIds.length }} selected</span>
                </div>

                <!-- Area table count info -->
                <p v-if="tablesInSameArea.length" class="text-xs text-gray-600 mb-3">
                    {{ tablesInSameArea.length }} table(s) already in this area
                    <span v-if="tablesInSameArea.filter(t=>t.waiterId).length">
                        · {{ tablesInSameArea.filter(t=>t.waiterId).length }} already assigned
                    </span>
                </p>

                <!-- Waiter cards (prioritize waiters assigned to this dining area) -->
                <div v-if="waitersInArea.length" class="space-y-2">
                    <button
                        v-for="(waiter, i) in waitersInArea"
                        :key="waiter.id"
                        type="button"
                        @click="toggleWaiter(waiter.id)"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border transition-all text-left"
                        :class="newTable.selectedWaiterIds.includes(waiter.id)
                            ? 'bg-blue-500/10 border-blue-500/40 shadow-[0_0_12px_rgba(59,130,246,0.1)]'
                            : 'bg-gray-900/40 border-gray-800/40 hover:border-gray-600'"
                    >
                        <!-- Avatar -->
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-br flex items-center justify-center text-white text-xs font-bold shrink-0 shadow"
                            :class="avatarGrads[i % avatarGrads.length]">
                            {{ waiter.avatar }}
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white">{{ waiter.name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ waiterLoadInArea[waiter.id] || 0 }} table(s) in {{ newTable.area }}
                                <span class="text-emerald-600">· Works this area</span>
                            </p>
                        </div>

                        <!-- Check -->
                        <div class="h-5 w-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all"
                            :class="newTable.selectedWaiterIds.includes(waiter.id)
                                ? 'bg-blue-500 border-blue-500'
                                : 'border-gray-600'">
                            <svg v-if="newTable.selectedWaiterIds.includes(waiter.id)" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </button>
                </div>
                <p v-else class="text-xs text-gray-600 py-4 text-center">No waiters assigned to {{ newTable.area }} yet.</p>

                <!-- Other-area waiters (optional) -->
                <div v-if="allWaiters.filter(w => w.area !== newTable.area).length" class="mt-3">
                    <p class="text-xs text-gray-600 mb-2">Other areas</p>
                    <div class="space-y-2">
                        <button
                            v-for="(waiter, i) in allWaiters.filter(w => w.area !== newTable.area)"
                            :key="'other-' + waiter.id"
                            type="button"
                            @click="toggleWaiter(waiter.id)"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl border transition-all text-left opacity-80 hover:opacity-100"
                            :class="newTable.selectedWaiterIds.includes(waiter.id)
                                ? 'bg-blue-500/10 border-blue-500/40'
                                : 'bg-gray-900/20 border-gray-800/30 hover:border-gray-600'"
                        >
                            <div class="h-8 w-8 rounded-lg bg-gradient-to-br flex items-center justify-center text-white text-xs font-bold shrink-0"
                                :class="avatarGrads[(waitersInArea.length + i) % avatarGrads.length]">{{ waiter.avatar }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-300">{{ waiter.name }}</p>
                                <p class="text-xs text-gray-600">{{ waiter.area }}</p>
                            </div>
                            <div class="h-5 w-5 rounded-full border-2 flex items-center justify-center shrink-0"
                                :class="newTable.selectedWaiterIds.includes(waiter.id) ? 'bg-blue-500 border-blue-500' : 'border-gray-600'">
                                <svg v-if="newTable.selectedWaiterIds.includes(waiter.id)" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>

                <p class="text-xs text-gray-600 mt-2">The first selected waiter becomes the primary. All selected can be reassigned per shift.</p>
            </div>

            <!-- ─── BULK ASSIGN TO EXISTING TABLES ──────────────────────── -->
            <div v-if="tablesInSameArea.length && newTable.selectedWaiterIds.length">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-400">
                        Also assign to tables in <span class="text-blue-400">{{ newTable.area }}</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="selectAllTablesInArea" class="text-xs text-blue-400 hover:text-blue-300">Select all</button>
                        <span class="text-gray-700">·</span>
                        <button type="button" @click="clearTableSelection" class="text-xs text-gray-500 hover:text-gray-300">Clear</button>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mb-3">
                    Apply the same waiter(s) to existing tables while creating this one.
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        v-for="table in tablesInSameArea"
                        :key="'bulk-' + table.id"
                        type="button"
                        @click="toggleExistingTable(table.id)"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border transition-all text-left"
                        :class="newTable.selectedExistingTableIds.includes(table.id)
                            ? 'bg-emerald-500/10 border-emerald-500/40'
                            : 'bg-gray-900/40 border-gray-800/40 hover:border-gray-600'"
                    >
                        <div class="h-4 w-4 rounded border flex items-center justify-center shrink-0"
                            :class="newTable.selectedExistingTableIds.includes(table.id)
                                ? 'bg-emerald-500 border-emerald-500'
                                : 'border-gray-600'">
                            <svg v-if="newTable.selectedExistingTableIds.includes(table.id)" class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ table.name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ table.waiter || 'Unassigned' }} · {{ table.capacity }} seats
                            </p>
                        </div>
                    </button>
                </div>
                <p v-if="newTable.selectedExistingTableIds.length" class="text-xs text-emerald-400 mt-2 font-medium">
                    {{ newTable.selectedExistingTableIds.length }} table(s) will receive the same waiter assignment
                </p>
            </div>

            <!-- Name Preview -->
            <div class="p-3 rounded-xl bg-gray-900/60 border border-gray-800 text-xs text-gray-400">
                <span class="text-gray-500">Preview: </span>
                <span class="font-semibold text-white">T-{{ newTable.number || '?' }}{{ newTable.area !== 'Ground Floor' ? ` (${newTable.area})` : '' }}</span>
                <span v-if="newTable.selectedWaiterIds.length" class="ml-2 text-blue-400">
                    · {{ allWaiters.find(w=>w.id===newTable.selectedWaiterIds[0])?.name }}
                    <span v-if="newTable.selectedWaiterIds.length > 1"> +{{ newTable.selectedWaiterIds.length-1 }}</span>
                </span>
                <span v-if="newTable.selectedExistingTableIds.length" class="block mt-1 text-emerald-400">
                    + {{ newTable.selectedExistingTableIds.length }} existing table(s) in {{ newTable.area }} will get the same assignment
                </span>
            </div>
        </div>

        <template #footer>
            <button @click="showAddTableModal = false" class="px-5 py-2 text-sm font-medium text-gray-400 bg-gray-800 hover:bg-gray-700 rounded-xl border border-gray-700/50 transition-all">Cancel</button>
            <button @click="submitAddTable" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-xl shadow-[0_0_15px_rgba(59,130,246,0.2)] transition-all">Create Table</button>
        </template>
    </SlideModal>

    <!-- ══════════════════════════════════════════════════════════════════════
         MODAL: EDIT TABLE
    ══════════════════════════════════════════════════════════════════════════ -->
    <SlideModal :show="showEditTableModal" title="Edit Table & Waiter" width="max-w-lg" @close="showEditTableModal = false">
        <div class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Table Number</label>
                    <input v-model="editTable.number" type="text" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Dining Area</label>
                    <select v-model="editTable.area" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option v-for="a in areas" :key="a" :value="a">{{ a }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Seating Capacity</label>
                <input v-model="editTable.capacity" type="number" min="1" max="30" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-blue-500 focus:border-blue-500"/>
            </div>

            <!-- Waiter re-assignment -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-gray-400">Assign Waiter(s)</label>
                    <span v-if="editTable.selectedWaiterIds && editTable.selectedWaiterIds.length" class="text-xs text-emerald-400 font-bold">{{ editTable.selectedWaiterIds.length }} selected</span>
                </div>
                <div class="space-y-2">
                    <button
                        v-for="(waiter, i) in allWaiters"
                        :key="waiter.id"
                        type="button"
                        @click="toggleEditWaiter(waiter.id)"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border transition-all text-left"
                        :class="editTable.selectedWaiterIds && editTable.selectedWaiterIds.includes(waiter.id)
                            ? 'bg-blue-500/10 border-blue-500/40'
                            : 'bg-gray-900/40 border-gray-800/40 hover:border-gray-600'"
                    >
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-br flex items-center justify-center text-white text-xs font-bold shrink-0"
                            :class="avatarGrads[i % avatarGrads.length]">{{ waiter.avatar }}</div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-white">{{ waiter.name }}</p>
                            <p class="text-xs text-gray-500">{{ waiter.area }}</p>
                        </div>
                        <div class="h-5 w-5 rounded-full border-2 flex items-center justify-center"
                            :class="editTable.selectedWaiterIds && editTable.selectedWaiterIds.includes(waiter.id) ? 'bg-blue-500 border-blue-500' : 'border-gray-600'">
                            <svg v-if="editTable.selectedWaiterIds && editTable.selectedWaiterIds.includes(waiter.id)" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <template #footer>
            <button @click="showEditTableModal = false" class="px-5 py-2 text-sm font-medium text-gray-400 bg-gray-800 hover:bg-gray-700 rounded-xl border border-gray-700/50 transition-all">Cancel</button>
            <button @click="submitEditTable" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all">Save Changes</button>
        </template>
    </SlideModal>
</template>
