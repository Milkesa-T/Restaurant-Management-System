<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import SlideModal from '@/Components/SlideModal.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

const ACCEPTED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_IMAGE_SIZE_MB = 5;

// ─── Tabs ────────────────────────────────────────────────────────────────────
const activeTab = ref('published');
const tabs = [
    { key: 'published', label: 'Published Menu' },
    { key: 'proposed',  label: 'Chef Proposals' },
    { key: 'archived',  label: 'Archived' },
];

// ─── Data ────────────────────────────────────────────────────────────────────
const menuItems = ref([
    { id: 1,  name: 'Margherita Pizza',    category: 'Mains',       station: 'Italian Kitchen',   price: 11.00, prep: 15, availability: 'available' },
    { id: 2,  name: 'Classic Beef Burger', category: 'Mains',       station: 'Italian Kitchen',   price: 12.50, prep: 12, availability: 'available' },
    { id: 3,  name: 'Pasta Carbonara',     category: 'Mains',       station: 'Italian Kitchen',   price: 14.00, prep: 14, availability: 'available' },
    { id: 4,  name: 'Shiro Wat',           category: 'Mains',       station: 'Ethiopian Kitchen', price: 10.00, prep: 10, availability: 'available' },
    { id: 5,  name: 'Kitfo',               category: 'Mains',       station: 'Ethiopian Kitchen', price: 18.00, prep: 25, availability: 'available' },
    { id: 6,  name: 'Garlic Bread',        category: 'Appetizers',  station: 'Italian Kitchen',   price:  5.50, prep:  8, availability: 'available' },
    { id: 7,  name: 'Bruschetta',          category: 'Appetizers',  station: 'Italian Kitchen',   price:  6.50, prep: 10, availability: 'available' },
    { id: 8,  name: 'Tiramisu',            category: 'Desserts',    station: 'Italian Kitchen',   price:  7.00, prep:  5, availability: 'available' },
    { id: 9,  name: 'Chocolate Fudge Cake',category: 'Desserts',    station: 'Italian Kitchen',   price:  6.50, prep:  5, availability: 'available' },
    { id: 10, name: 'Fresh Orange Juice',  category: 'Drinks',      station: 'Bar',               price:  4.00, prep:  3, availability: 'available' },
    { id: 11, name: 'Espresso',            category: 'Drinks',      station: 'Bar',               price:  3.00, prep:  3, availability: 'available' },
]);

const proposedItems = ref([
    { id: 12, name: 'Doro Wat',      category: 'Mains', station: 'Ethiopian Kitchen', proposedPrice: 22.00, finalPrice: 20.00, prep: 35, description: 'Slow-cooked chicken stew in spicy berbere sauce served with Injera.', proposedBy: 'Hassan Chef', proposedAt: '1 hour ago' },
    { id: 13, name: 'Lemon Risotto', category: 'Mains', station: 'Italian Kitchen',   proposedPrice: 16.00, finalPrice: 14.00, prep: 20, description: 'Creamy arborio rice with fresh lemon zest, white wine, and parmesan.', proposedBy: 'David Chef',  proposedAt: '3 hours ago' },
]);

const stationColors = {
    'Italian Kitchen':   'bg-blue-500/20 text-blue-400',
    'Ethiopian Kitchen': 'bg-amber-500/20 text-amber-400',
    'Bar':               'bg-purple-500/20 text-purple-400',
};

const stations    = ['Italian Kitchen', 'Ethiopian Kitchen', 'Bar'];
const categories  = ['Appetizers', 'Mains', 'Desserts', 'Drinks'];

// ─── ADD MENU ITEM MODAL ────────────────────────────────────────────────────
const showAddModal = ref(false);
const newItem = reactive({
    name: '', category: 'Mains', station: 'Italian Kitchen',
    price: '', prep: '', description: '', availability: 'available',
    imageFile: null, imagePreview: null,
});
const addErrors = reactive({});
const addImageInput = ref(null);
const editImageInput = ref(null);
const reviewImageInput = ref(null);

function resetImageState(target) {
    target.imageFile = null;
    target.imagePreview = null;
}

function handleImageSelect(event, target, errors) {
    const file = event.target.files?.[0];
    if (!file) return;

    if (errors.image) delete errors.image;

    if (!ACCEPTED_IMAGE_TYPES.includes(file.type)) {
        errors.image = 'Please upload a JPG, PNG, or WebP image.';
        event.target.value = '';
        return;
    }
    if (file.size > MAX_IMAGE_SIZE_MB * 1024 * 1024) {
        errors.image = `Image must be under ${MAX_IMAGE_SIZE_MB} MB.`;
        event.target.value = '';
        return;
    }

    target.imageFile = file;
    const reader = new FileReader();
    reader.onload = (e) => { target.imagePreview = e.target.result; };
    reader.readAsDataURL(file);
}

function removeImage(target, inputRef) {
    target.imageFile = null;
    target.imagePreview = null;
    if (inputRef?.value) inputRef.value.value = '';
}

function openAddModal() {
    Object.assign(newItem, {
        name: '', category: 'Mains', station: 'Italian Kitchen',
        price: '', prep: '', description: '', availability: 'available',
        imageFile: null, imagePreview: null,
    });
    Object.keys(addErrors).forEach(k => delete addErrors[k]);
    if (addImageInput.value) addImageInput.value.value = '';
    showAddModal.value = true;
}

function validateNewItem() {
    Object.keys(addErrors).forEach(k => delete addErrors[k]);
    if (!newItem.name.trim())  addErrors.name  = 'Name is required.';
    if (!newItem.price || isNaN(newItem.price) || +newItem.price <= 0) addErrors.price = 'Enter a valid price.';
    if (!newItem.prep  || isNaN(newItem.prep)  || +newItem.prep  <= 0) addErrors.prep  = 'Enter a valid prep time.';
    return Object.keys(addErrors).length === 0;
}

function submitNewItem() {
    if (!validateNewItem()) return;
    menuItems.value.push({
        id: Date.now(),
        name: newItem.name,
        category: newItem.category,
        station: newItem.station,
        price: parseFloat(newItem.price),
        prep: parseInt(newItem.prep),
        availability: newItem.availability,
        description: newItem.description,
        imageUrl: newItem.imagePreview,
    });
    showAddModal.value = false;
}

// ─── EDIT PUBLISHED ITEM MODAL ──────────────────────────────────────────────
const showEditModal = ref(false);
const editItem = reactive({});

function openEditModal(item) {
    Object.assign(editItem, { ...item, imageFile: null, imagePreview: item.imageUrl ?? null });
    if (editImageInput.value) editImageInput.value.value = '';
    showEditModal.value = true;
}

function submitEditItem() {
    const idx = menuItems.value.findIndex(i => i.id === editItem.id);
    if (idx !== -1) {
        menuItems.value[idx] = {
            ...editItem,
            price: parseFloat(editItem.price),
            prep: parseInt(editItem.prep),
            imageUrl: editItem.imagePreview ?? editItem.imageUrl ?? null,
        };
    }
    showEditModal.value = false;
}

// ─── REVIEW PROPOSAL MODAL (Admin edits before approve/reject) ──────────────
const showProposalModal = ref(false);
const reviewItem = reactive({});

function openProposalReview(item) {
    Object.assign(reviewItem, { ...item, imageFile: null, imagePreview: item.imageUrl ?? null });
    if (reviewImageInput.value) reviewImageInput.value.value = '';
    showProposalModal.value = true;
}

function approveProposal() {
    menuItems.value.push({
        id: reviewItem.id,
        name: reviewItem.name,
        category: reviewItem.category,
        station: reviewItem.station,
        price: parseFloat(reviewItem.finalPrice),
        prep: parseInt(reviewItem.prep),
        availability: 'available',
        description: reviewItem.description,
        imageUrl: reviewItem.imagePreview ?? reviewItem.imageUrl ?? null,
    });
    proposedItems.value = proposedItems.value.filter(p => p.id !== reviewItem.id);
    showProposalModal.value = false;
    activeTab.value = 'published';
}

function rejectProposal() {
    proposedItems.value = proposedItems.value.filter(p => p.id !== reviewItem.id);
    showProposalModal.value = false;
}

// ─── Archive ─────────────────────────────────────────────────────────────────
const archivedItems = ref([]);
function archiveItem(item) {
    archivedItems.value.push(item);
    menuItems.value = menuItems.value.filter(i => i.id !== item.id);
}
</script>

<template>
    <Head title="Menu Management" />

    <AuthenticatedLayout active-nav="Menu">
        <template #header>
            <PageHeader title="Menu Management" :breadcrumbs="[{ label: 'Dashboard', url: route('dashboard') }, { label: 'Menu' }]">
                <template #actions>
                    <button @click="openAddModal" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all shadow-[0_0_15px_rgba(16,185,129,0.2)] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Item
                    </button>
                </template>
            </PageHeader>
        </template>

        <!-- Tabs -->
        <div class="flex items-center gap-1 mb-6 bg-[#11141c]/60 p-1 rounded-xl border border-gray-800/80 w-fit">
            <button
                v-for="tab in tabs" :key="tab.key"
                @click="activeTab = tab.key"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all"
                :class="activeTab === tab.key ? 'bg-gray-800 text-white shadow' : 'text-gray-400 hover:text-gray-200'"
            >
                {{ tab.label }}
                <span v-if="tab.key === 'proposed' && proposedItems.length" class="px-1.5 py-0.5 rounded-full text-xs bg-amber-500/20 text-amber-400 font-bold">
                    {{ proposedItems.length }}
                </span>
                <span v-else-if="tab.key === 'archived' && archivedItems.length" class="px-1.5 py-0.5 rounded-full text-xs bg-gray-700 text-gray-400">
                    {{ archivedItems.length }}
                </span>
            </button>
        </div>

        <!-- ── Published Menu Table ───────────────────────────────────────── -->
        <div v-if="activeTab === 'published'" class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl overflow-hidden backdrop-blur-md">
            <div class="p-5 border-b border-gray-800/50 flex items-center justify-between">
                <p class="text-sm text-gray-400">{{ menuItems.length }} items visible to customers</p>
                <input type="text" placeholder="Search items…" class="bg-gray-900 border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-1.5 placeholder-gray-600 w-48 focus:ring-emerald-500 focus:border-emerald-500" />
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-300 text-left">
                    <thead class="text-xs uppercase text-gray-500 border-b border-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 w-16">Image</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Kitchen</th>
                            <th class="px-6 py-3">Est. Prep</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Availability</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/40">
                        <tr v-for="item in menuItems" :key="item.id" class="hover:bg-gray-800/10 transition-colors">
                            <td class="px-6 py-4">
                                <div v-if="item.imageUrl" class="h-10 w-10 rounded-lg overflow-hidden border border-gray-700/50">
                                    <img :src="item.imageUrl" :alt="item.name" class="h-full w-full object-cover" />
                                </div>
                                <div v-else class="h-10 w-10 rounded-lg bg-gray-800/60 border border-gray-700/50 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-white">{{ item.name }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ item.category }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="stationColors[item.station] || 'bg-gray-700 text-gray-300'">
                                    {{ item.station }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-400">{{ item.prep }} min</td>
                            <td class="px-6 py-4 font-bold text-white font-mono">${{ item.price.toFixed(2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">Available</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <button @click="openEditModal(item)" class="text-xs text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-1 rounded-lg transition-colors border border-gray-700/50 mr-1">Edit</button>
                                <button @click="archiveItem(item)" class="text-xs text-red-400 bg-red-950/20 hover:bg-red-950/40 px-3 py-1 rounded-lg transition-colors border border-red-900/20">Archive</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Chef Proposals ─────────────────────────────────────────────── -->
        <div v-if="activeTab === 'proposed'" class="space-y-4">
            <p v-if="proposedItems.length === 0" class="text-center text-gray-500 py-16">No pending proposals.</p>
            <div v-for="item in proposedItems" :key="item.id" class="bg-[#11141c]/60 border border-amber-800/30 rounded-2xl p-6 backdrop-blur-md">
                <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-bold text-white">{{ item.name }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/20">Pending Review</span>
                        </div>
                        <p class="text-sm text-gray-400 mb-3">{{ item.description }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Kitchen Station</p>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="stationColors[item.station] || 'bg-gray-700'">{{ item.station }}</span>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Chef's Proposed Price</p>
                                <p class="font-bold text-amber-400 font-mono">${{ item.proposedPrice.toFixed(2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Admin Final Price</p>
                                <p class="font-bold text-white font-mono">${{ item.finalPrice.toFixed(2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1">Est. Prep Time</p>
                                <p class="font-bold text-white font-mono">{{ item.prep }} min</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">Proposed by <span class="text-gray-300 font-semibold">{{ item.proposedBy }}</span> · {{ item.proposedAt }}</p>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <button @click="openProposalReview(item)" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Review & Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Archived ───────────────────────────────────────────────────── -->
        <div v-if="activeTab === 'archived'">
            <div v-if="archivedItems.length === 0" class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl p-12 text-center backdrop-blur-md">
                <svg class="w-12 h-12 mx-auto text-gray-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <p class="text-gray-400 font-medium">No archived items yet.</p>
            </div>
            <div v-else class="bg-[#11141c]/60 border border-gray-800/80 rounded-2xl overflow-hidden backdrop-blur-md">
                <table class="w-full text-sm text-gray-400 text-left">
                    <thead class="text-xs uppercase text-gray-600 border-b border-gray-800/50">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Kitchen</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/40">
                        <tr v-for="item in archivedItems" :key="item.id">
                            <td class="px-6 py-4 line-through">{{ item.name }}</td>
                            <td class="px-6 py-4">{{ item.category }}</td>
                            <td class="px-6 py-4">{{ item.station }}</td>
                            <td class="px-6 py-4 font-mono">${{ item.price.toFixed(2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <button @click="menuItems.push(item); archivedItems = archivedItems.filter(a => a.id !== item.id)" class="text-xs text-emerald-400 bg-emerald-950/30 hover:bg-emerald-950/60 px-3 py-1 rounded-lg border border-emerald-900/30">Restore</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- ══════════════════════════════════════════════════════════════════════
         MODAL: ADD NEW MENU ITEM
    ══════════════════════════════════════════════════════════════════════════ -->
    <SlideModal :show="showAddModal" title="Add New Menu Item" width="max-w-xl" @close="showAddModal = false">
        <div class="space-y-4">
            <!-- Item Image Upload -->
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Item Image</label>
                <input
                    ref="addImageInput"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    @change="handleImageSelect($event, newItem, addErrors)"
                />
                <div v-if="newItem.imagePreview" class="relative rounded-xl overflow-hidden border border-gray-700/50">
                    <img :src="newItem.imagePreview" alt="Preview" class="w-full h-40 object-cover" />
                    <button
                        type="button"
                        @click="removeImage(newItem, addImageInput)"
                        class="absolute top-2 right-2 p-1.5 bg-black/60 hover:bg-black/80 text-white rounded-lg transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <button
                    v-else
                    type="button"
                    @click="addImageInput?.click()"
                    class="w-full flex flex-col items-center justify-center gap-2 py-8 rounded-xl border-2 border-dashed border-gray-700 hover:border-emerald-500/50 hover:bg-emerald-500/5 transition-all cursor-pointer"
                >
                    <svg class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm text-gray-400">Click to upload dish photo</span>
                    <span class="text-xs text-gray-600">JPG, PNG or WebP · max {{ MAX_IMAGE_SIZE_MB }} MB</span>
                </button>
                <p v-if="addErrors.image" class="text-red-400 text-xs mt-1">{{ addErrors.image }}</p>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Item Name <span class="text-red-400">*</span></label>
                <input v-model="newItem.name" type="text" placeholder="e.g. Truffle Pasta" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm placeholder-gray-600 focus:ring-emerald-500 focus:border-emerald-500" />
                <p v-if="addErrors.name" class="text-red-400 text-xs mt-1">{{ addErrors.name }}</p>
            </div>

            <!-- Category & Kitchen Station (2 col) -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Category <span class="text-red-400">*</span></label>
                    <select v-model="newItem.category" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Kitchen Station</label>
                    <select v-model="newItem.station" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option v-for="s in stations" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>
            </div>

            <!-- Price & Prep Time -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Price (USD) <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">$</span>
                        <input v-model="newItem.price" type="number" min="0" step="0.01" placeholder="0.00" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl pl-7 pr-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
                    </div>
                    <p v-if="addErrors.price" class="text-red-400 text-xs mt-1">{{ addErrors.price }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Est. Prep Time (min) <span class="text-red-400">*</span></label>
                    <input v-model="newItem.prep" type="number" min="1" placeholder="15" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
                    <p v-if="addErrors.prep" class="text-red-400 text-xs mt-1">{{ addErrors.prep }}</p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Description</label>
                <textarea v-model="newItem.description" rows="3" placeholder="Short description of the dish…" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm placeholder-gray-600 focus:ring-emerald-500 focus:border-emerald-500 resize-none"></textarea>
            </div>

            <!-- Availability -->
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Availability</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" v-model="newItem.availability" value="available" class="accent-emerald-500" />
                        <span class="text-sm text-gray-300">Available</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" v-model="newItem.availability" value="unavailable" class="accent-emerald-500" />
                        <span class="text-sm text-gray-300">Unavailable</span>
                    </label>
                </div>
            </div>
        </div>

        <template #footer>
            <button @click="showAddModal = false" class="px-5 py-2 text-sm font-medium text-gray-400 bg-gray-800 hover:bg-gray-700 rounded-xl border border-gray-700/50 transition-all">Cancel</button>
            <button @click="submitNewItem" class="px-5 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.2)] transition-all">Publish Item</button>
        </template>
    </SlideModal>

    <!-- ══════════════════════════════════════════════════════════════════════
         MODAL: EDIT PUBLISHED ITEM
    ══════════════════════════════════════════════════════════════════════════ -->
    <SlideModal :show="showEditModal" title="Edit Menu Item" width="max-w-xl" @close="showEditModal = false">
        <div class="space-y-4">
            <!-- Item Image -->
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Item Image</label>
                <input
                    ref="editImageInput"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    @change="handleImageSelect($event, editItem, {})"
                />
                <div v-if="editItem.imagePreview" class="relative rounded-xl overflow-hidden border border-gray-700/50">
                    <img :src="editItem.imagePreview" alt="Preview" class="w-full h-40 object-cover" />
                    <div class="absolute inset-x-0 bottom-0 flex gap-2 p-2 bg-gradient-to-t from-black/80 to-transparent">
                        <button type="button" @click="editImageInput?.click()" class="flex-1 py-1.5 text-xs font-medium text-white bg-gray-800/80 hover:bg-gray-700 rounded-lg transition-colors">
                            Replace
                        </button>
                        <button type="button" @click="removeImage(editItem, editImageInput)" class="py-1.5 px-3 text-xs font-medium text-red-400 bg-red-950/60 hover:bg-red-950/80 rounded-lg transition-colors">
                            Remove
                        </button>
                    </div>
                </div>
                <button
                    v-else
                    type="button"
                    @click="editImageInput?.click()"
                    class="w-full flex flex-col items-center justify-center gap-2 py-6 rounded-xl border-2 border-dashed border-gray-700 hover:border-blue-500/50 hover:bg-blue-500/5 transition-all"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs text-gray-500">Upload image</span>
                </button>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Item Name</label>
                <input v-model="editItem.name" type="text" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Category</label>
                    <select v-model="editItem.category" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Kitchen Station</label>
                    <select v-model="editItem.station" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option v-for="s in stations" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Price (USD)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">$</span>
                        <input v-model="editItem.price" type="number" min="0" step="0.01" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl pl-7 pr-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Est. Prep Time (min)</label>
                    <input v-model="editItem.prep" type="number" min="1" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Availability</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" v-model="editItem.availability" value="available" class="accent-emerald-500" />
                        <span class="text-sm text-gray-300">Available</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" v-model="editItem.availability" value="unavailable" class="accent-emerald-500" />
                        <span class="text-sm text-gray-300">Unavailable</span>
                    </label>
                </div>
            </div>
        </div>

        <template #footer>
            <button @click="showEditModal = false" class="px-5 py-2 text-sm font-medium text-gray-400 bg-gray-800 hover:bg-gray-700 rounded-xl border border-gray-700/50 transition-all">Cancel</button>
            <button @click="submitEditItem" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all">Save Changes</button>
        </template>
    </SlideModal>

    <!-- ══════════════════════════════════════════════════════════════════════
         MODAL: REVIEW CHEF PROPOSAL (Admin edits finalPrice, prep, etc.)
    ══════════════════════════════════════════════════════════════════════════ -->
    <SlideModal :show="showProposalModal" title="Review Chef Proposal" width="max-w-2xl" @close="showProposalModal = false">
        <div v-if="reviewItem.id" class="space-y-5">

            <!-- Info banner -->
            <div class="p-3 rounded-xl bg-amber-950/30 border border-amber-800/40 text-sm text-amber-300 flex items-start gap-2">
                <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>You are reviewing a proposal by <strong>{{ reviewItem.proposedBy }}</strong>. You can edit all fields before approving. The <strong>final price</strong> you set will override the chef's proposed price on the menu.</span>
            </div>

            <!-- Name & Description -->
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Item Image</label>
                <input
                    ref="reviewImageInput"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    @change="handleImageSelect($event, reviewItem, {})"
                />
                <div v-if="reviewItem.imagePreview" class="relative rounded-xl overflow-hidden border border-gray-700/50 mb-4">
                    <img :src="reviewItem.imagePreview" alt="Preview" class="w-full h-36 object-cover" />
                    <button
                        type="button"
                        @click="reviewImageInput?.click()"
                        class="absolute bottom-2 right-2 px-3 py-1 text-xs font-medium text-white bg-gray-800/80 hover:bg-gray-700 rounded-lg"
                    >
                        Replace
                    </button>
                </div>
                <button
                    v-else
                    type="button"
                    @click="reviewImageInput?.click()"
                    class="w-full flex items-center justify-center gap-2 py-4 mb-4 rounded-xl border border-dashed border-gray-700 hover:border-amber-500/50 transition-all text-sm text-gray-500"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Add item photo before publishing
                </button>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Item Name</label>
                <input v-model="reviewItem.name" type="text" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Description</label>
                <textarea v-model="reviewItem.description" rows="2" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm resize-none focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>

            <!-- Category & Station -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Category</label>
                    <select v-model="reviewItem.category" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Kitchen Station</label>
                    <select v-model="reviewItem.station" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option v-for="s in stations" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>
            </div>

            <!-- Price Comparison -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Chef's Proposed Price</label>
                    <div class="bg-gray-900/60 border border-gray-800 rounded-xl px-4 py-2.5 text-amber-400 font-mono font-bold">
                        ${{ Number(reviewItem.proposedPrice).toFixed(2) }}
                        <span class="text-xs text-gray-600 font-normal ml-1">(read-only)</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-emerald-400 mb-1.5">✎ Your Final Price (USD)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">$</span>
                        <input v-model="reviewItem.finalPrice" type="number" min="0" step="0.01" class="w-full bg-gray-900 border border-emerald-700/50 text-white rounded-xl pl-7 pr-4 py-2.5 text-sm font-mono font-bold focus:ring-emerald-500 focus:border-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.05)]" />
                    </div>
                </div>
            </div>

            <!-- Prep Time -->
            <div>
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">Estimated Prep Time (minutes)</label>
                <input v-model="reviewItem.prep" type="number" min="1" class="w-48 bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-emerald-500 focus:border-emerald-500" />
            </div>
        </div>

        <template #footer>
            <button @click="rejectProposal" class="px-5 py-2 text-sm font-medium text-red-400 bg-red-950/30 hover:bg-red-950/60 rounded-xl border border-red-900/30 transition-all">
                Reject Proposal
            </button>
            <button @click="showProposalModal = false" class="px-5 py-2 text-sm font-medium text-gray-400 bg-gray-800 hover:bg-gray-700 rounded-xl border border-gray-700/50 transition-all">Cancel</button>
            <button @click="approveProposal" class="px-5 py-2 text-sm font-medium text-emerald-950 bg-emerald-400 hover:bg-emerald-300 rounded-xl shadow-[0_0_15px_rgba(52,211,153,0.3)] transition-all font-semibold">
                ✓ Approve & Publish
            </button>
        </template>
    </SlideModal>
</template>
