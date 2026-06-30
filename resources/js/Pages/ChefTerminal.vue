<script setup>
import { ref, computed } from 'vue';
import { Link, Head } from '@inertiajs/vue3';

const props = defineProps({
    initialCatalog: {
        type: Array,
        required: true
    },
    initialInventory: {
        type: Array,
        required: true
    }
});

// Navigation state
const activeTab = ref('creator'); // creator, inventory

// Data state
const catalog = ref(JSON.parse(JSON.stringify(props.initialCatalog)));
const inventory = ref(JSON.parse(JSON.stringify(props.initialInventory)));

// Clinical alerts log console
const logs = ref([
    { type: 'ALERT', time: '09:12', text: 'Critical level reached: TEFF_FLOUR_GR_BAG (0.85/1.50)' },
    { type: 'NOTIF', time: '08:45', text: 'Reorder suggested: KIBBEH_BUTTER_L (1.10/1.00)' },
    { type: 'SYSTEM', time: '06:00', text: 'Morning inventory audit complete. Integrity verified.' }
]);

// Menu Creator Form State
const dishForm = ref({
    name: '',
    price: '',
    category: 'Mains (Wats & Tibs)',
    description: '',
    isVegan: false,
    isMeat: true,
    ingredientInput: '',
    ingredientTags: ['Berbere', 'Niter Kibbeh'],
    imagePreview: null
});

// Supply Request Form State
const supplyForm = ref({
    itemId: '',
    quantity: '',
    priority: 'Standard Sourcing'
});

// Toast notification state
const toast = ref({
    show: false,
    message: ''
});

// Switch tabs
const switchTab = (tab) => {
    activeTab.value = tab;
};

// Add ingredient tag
const addIngredientTag = () => {
    const text = dishForm.value.ingredientInput.trim();
    if (text) {
        if (!dishForm.value.ingredientTags.includes(text)) {
            dishForm.value.ingredientTags.push(text);
            showToastNotification(`Added ingredient tag: ${text}`);
        }
        dishForm.value.ingredientInput = '';
    }
};

// Remove ingredient tag
const removeIngredientTag = (index) => {
    const tag = dishForm.value.ingredientTags[index];
    dishForm.value.ingredientTags.splice(index, 1);
    showToastNotification(`Removed ingredient tag: ${tag}`);
};

// Handle Image Upload
const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            showToastNotification('File is too large. Max size is 10MB.');
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            dishForm.value.imagePreview = e.target.result;
            showToastNotification('Image uploaded successfully.');
        };
        reader.readAsDataURL(file);
    }
};

// Save custom menu item
const saveToMenu = () => {
    if (!dishForm.value.name.trim()) {
        showToastNotification('Please enter a dish name.');
        return;
    }
    if (!dishForm.value.price || parseFloat(dishForm.value.price) <= 0) {
        showToastNotification('Please enter a valid price.');
        return;
    }

    const classification = dishForm.value.isVegan ? 'Yetsome' : 'Yefisik';

    const newItem = {
        id: catalog.value.length + 1,
        name: dishForm.value.name,
        price: parseFloat(dishForm.value.price),
        category: dishForm.value.category,
        description: dishForm.value.description,
        classification: classification,
        image: dishForm.value.imagePreview || null
    };

    // Add to catalog
    catalog.value.unshift(newItem);

    // Log to console
    const now = new Date();
    const timeStr = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
    logs.value.unshift({
        type: 'SYSTEM',
        time: timeStr,
        text: `Menu creator: Cataloged new dish "${newItem.name}" (${newItem.price} ETB)`
    });

    showToastNotification(`"${newItem.name}" has been successfully added to the digital menu!`);

    // Reset Form
    discardDraft();
};

// Discard Form Draft
const discardDraft = () => {
    dishForm.value.name = '';
    dishForm.value.price = '';
    dishForm.value.category = 'Mains (Wats & Tibs)';
    dishForm.value.description = '';
    dishForm.value.isVegan = false;
    dishForm.value.isMeat = true;
    dishForm.value.ingredientInput = '';
    dishForm.value.ingredientTags = ['Berbere', 'Niter Kibbeh'];
    dishForm.value.imagePreview = null;
    showToastNotification('Draft discarded.');
};

// Submit supply request
const submitRequisition = () => {
    const itemId = supplyForm.value.itemId.trim().toUpperCase();
    const qty = parseFloat(supplyForm.value.quantity);

    if (!itemId) {
        showToastNotification('Please specify an Item ID.');
        return;
    }
    if (isNaN(qty) || qty <= 0) {
        showToastNotification('Please specify a positive quantity.');
        return;
    }

    const now = new Date();
    const timeStr = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;

    // Find and update item in stock table
    const item = inventory.value.find(i => i.id === itemId);
    
    if (item) {
        // Increase stock
        item.stock = parseFloat((item.stock + qty).toFixed(2));
        
        // Recalculate status based on threshold
        if (item.stock > item.threshold * 1.5) {
            item.status = 'Optimal';
        } else if (item.stock > item.threshold) {
            item.status = 'Low Stock';
        } else {
            item.status = 'Critical';
        }

        logs.value.unshift({
            type: 'REQUISITION',
            time: timeStr,
            text: `Requisition: Sourced +${qty} ${item.unit} for ${itemId}. Current: ${item.stock} ${item.unit}.`
        });
        showToastNotification(`Requisition processed. Stock updated for ${itemId}.`);
    } else {
        // Add pending log if item is new
        logs.value.unshift({
            type: 'NOTIF',
            time: timeStr,
            text: `Requisition submitted: ${qty} units of new Item ${itemId} (${supplyForm.value.priority}).`
        });
        showToastNotification(`Requisition submitted for verification: ${itemId}.`);
    }

    // Reset Form
    supplyForm.value.itemId = '';
    supplyForm.value.quantity = '';
    supplyForm.value.priority = 'Standard Sourcing';
};

// Show Toast
const showToastNotification = (msg) => {
    toast.value.message = msg;
    toast.value.show = true;
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// Get status badge classes
const getStatusBadgeClass = (status) => {
    if (status === 'Optimal') return 'bg-primary/10 text-primary';
    if (status === 'Critical') return 'bg-error/10 text-error';
    return 'bg-tertiary/10 text-tertiary'; // Low Stock
};

// Get log type class
const getLogTypeClass = (type) => {
    if (type === 'ALERT') return 'text-error';
    if (type === 'NOTIF') return 'text-tertiary';
    if (type === 'REQUISITION') return 'text-secondary-container';
    return 'text-white/40';
};
</script>

<template>
    <Head title="Chef Terminal - Ethiopian Culinary Hub" />

    <div class="bg-background text-on-surface font-body-md min-h-screen flex flex-col no-scrollbar select-none pb-20">
        
        <!-- TopAppBar -->
        <header class="sticky top-0 w-full z-50 bg-surface/80 backdrop-blur-md dark:bg-surface-dim/80 glass-panel border-b border-outline-variant/30 flex justify-between items-center px-margin-mobile py-sm md:px-margin-desktop">
            <div class="flex items-center gap-md">
                <Link href="/kds" class="p-sm hover:bg-surface-variant/50 transition-colors rounded-full active:scale-90 flex items-center border border-transparent hover:border-outline-variant/30">
                    <span class="material-symbols-outlined text-primary">menu</span>
                </Link>
                <h1 class="font-title-md text-title-md uppercase tracking-wider text-primary font-bold">ETHIOPIAN CULINARY HUB</h1>
            </div>
            <div class="flex items-center gap-sm">
                <span class="font-label-caps text-label-caps text-on-surface-variant hidden md:block tracking-wider">CHEF TERMINAL</span>
                <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center border border-primary/20">
                    <span class="material-symbols-outlined text-on-primary-container text-sm">person</span>
                </div>
            </div>
        </header>

        <!-- Secondary Navigation Tabs -->
        <div class="bg-surface border-b border-outline-variant/20 px-margin-mobile md:px-margin-desktop shadow-sm">
            <div class="flex gap-lg max-w-7xl mx-auto">
                <button 
                    @click="switchTab('creator')"
                    class="py-md border-b-2 font-label-caps text-label-caps tracking-wider transition-all active:scale-98" 
                    :class="activeTab === 'creator' ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-primary'"
                >
                    MENU CREATOR
                </button>
                <button 
                    @click="switchTab('inventory')"
                    class="py-md border-b-2 font-label-caps text-label-caps tracking-wider transition-all active:scale-98" 
                    :class="activeTab === 'inventory' ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-primary'"
                >
                    INVENTORY & SUPPLY
                </button>
            </div>
        </div>

        <main class="flex-grow w-full max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop py-xl">
            
            <!-- Tab 1: Menu Creator Section -->
            <div v-if="activeTab === 'creator'" class="space-y-xl animate-in fade-in duration-200">
                <div class="flex flex-col md:flex-row gap-xl">
                    
                    <!-- Content Canvas: Left Column -->
                    <div class="w-full md:w-5/12 flex flex-col gap-lg">
                        
                        <!-- Dish Photography Box -->
                        <div class="relative overflow-hidden group">
                            <div class="aspect-square w-full rounded-lg bg-surface-container-high border-dashed border-2 border-outline-variant flex flex-col items-center justify-center p-xl text-center group-hover:border-primary transition-all cursor-pointer relative">
                                
                                <template v-if="dishForm.imagePreview">
                                    <img :src="dishForm.imagePreview" alt="Dish Preview" class="w-full h-full object-cover rounded-lg absolute inset-0" />
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white">
                                        <span class="material-symbols-outlined text-4xl mb-sm">add_a_photo</span>
                                        <span class="font-label-caps text-xs">REPLACE PHOTO</span>
                                    </div>
                                </template>
                                <template v-else>
                                    <span class="material-symbols-outlined text-4xl text-outline-variant mb-md group-hover:text-primary transition-colors">add_a_photo</span>
                                    <p class="font-label-caps text-label-caps text-on-surface-variant mb-base">DISH PHOTOGRAPHY</p>
                                    <p class="text-xs text-outline italic">Upload high-resolution clinical food imagery (Max 10MB)</p>
                                </template>
                                
                                <input 
                                    class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" 
                                    type="file" 
                                    accept="image/*"
                                    @change="handleImageUpload"
                                />
                            </div>
                        </div>

                        <!-- Curation Protocols -->
                        <div class="p-lg bg-surface-container-low rounded-lg border border-outline-variant/30">
                            <h3 class="font-title-md text-title-md text-primary mb-sm uppercase font-bold tracking-tight">Curation Protocols</h3>
                            <ul class="space-y-sm text-sm text-on-surface-variant">
                                <li class="flex gap-sm items-start">
                                    <span class="material-symbols-outlined text-primary text-base mt-0.5">verified</span>
                                    <span>Standardize Amharic nomenclature.</span>
                                </li>
                                <li class="flex gap-sm items-start">
                                    <span class="material-symbols-outlined text-primary text-base mt-0.5">verified</span>
                                    <span>Map all specific ingredients for inventory tracking.</span>
                                </li>
                                <li class="flex gap-sm items-start">
                                    <span class="material-symbols-outlined text-primary text-base mt-0.5">verified</span>
                                    <span>Highlight heritage sourcing in descriptions.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Form Shell: Right Column -->
                    <div class="w-full md:w-7/12">
                        <div class="mb-lg">
                            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface font-bold">Chef's Menu Creator</h2>
                            <p class="text-on-surface-variant mt-sm">Meticulously catalog new additions to the digital culinary archive.</p>
                        </div>
                        
                        <div class="mb-lg flex flex-col sm:flex-row sm:items-center justify-between gap-md p-lg bg-primary/5 border border-primary/20 rounded-xl">
                            <div class="flex flex-col gap-xs">
                                <h3 class="font-title-md text-title-md text-primary font-bold">New Menu Entry</h3>
                                <p class="text-xs text-on-surface-variant">Initialize a new dish record with heritage ingredient mapping.</p>
                            </div>
                            <button 
                                @click="saveToMenu"
                                class="bg-primary text-on-primary font-label-caps text-label-caps py-md px-xl rounded-lg hover:bg-primary-container transition-all active:scale-95 shadow-md flex items-center justify-center gap-sm" 
                                type="button"
                            >
                                <span class="material-symbols-outlined text-lg">add_circle</span> 
                                CREATE MENU
                            </button>
                        </div>

                        <form class="space-y-lg" @submit.prevent="saveToMenu">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                                <div class="flex flex-col gap-base">
                                    <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">DISH NAME</label>
                                    <input 
                                        v-model="dishForm.name"
                                        class="bg-surface border border-outline-variant/50 rounded-lg p-md text-body-md custom-focus" 
                                        placeholder="e.g. Doro Wat" 
                                        type="text"
                                    />
                                </div>
                                <div class="flex flex-col gap-base relative">
                                    <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">PRICE (ETB)</label>
                                    <div class="relative flex items-center">
                                        <span class="absolute left-md font-price-sm text-price-sm text-tertiary">ETB</span>
                                        <input 
                                            v-model="dishForm.price"
                                            class="w-full bg-surface border border-outline-variant/50 rounded-lg p-md pl-14 text-body-md font-price-lg custom-focus" 
                                            placeholder="0.00" 
                                            type="number"
                                            step="0.01"
                                        />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-base">
                                <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">CATEGORY</label>
                                <select 
                                    v-model="dishForm.category"
                                    class="bg-surface border border-outline-variant/50 rounded-lg p-md text-body-md appearance-none custom-focus bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23004f35%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:0.7em_auto] bg-[right_1.2rem_center] bg-no-repeat"
                                >
                                    <option>Starters (Qurs)</option>
                                    <option>Mains (Wats & Tibs)</option>
                                    <option>Vegan Platters (Beyaynetu)</option>
                                    <option>Beverages (Tella & Tej)</option>
                                </select>
                            </div>

                            <!-- Ingredients Link Section -->
                            <div class="flex flex-col gap-base p-lg bg-surface-container-low rounded-lg border border-outline-variant/20">
                                <label class="font-label-caps text-label-caps text-on-surface-variant mb-sm flex items-center gap-xs tracking-wider">
                                    <span class="material-symbols-outlined text-sm">link</span>
                                    INGREDIENTS LINKING
                                </label>
                                <div class="flex flex-wrap gap-sm mb-md" id="ingredient-tags">
                                    <span 
                                        v-for="(tag, index) in dishForm.ingredientTags" 
                                        :key="index"
                                        class="px-sm py-1 bg-surface border border-outline-variant rounded-full flex items-center gap-xs text-xs font-semibold text-on-surface shadow-sm"
                                    >
                                        {{ tag }} 
                                        <button 
                                            class="material-symbols-outlined text-sm hover:text-error transition-colors flex items-center" 
                                            type="button"
                                            @click="removeIngredientTag(index)"
                                        >
                                            close
                                        </button>
                                    </span>
                                </div>
                                <div class="flex gap-sm">
                                    <input 
                                        v-model="dishForm.ingredientInput"
                                        @keydown.enter.prevent="addIngredientTag"
                                        class="flex-grow bg-surface border border-outline-variant/50 rounded-lg p-md text-xs font-semibold custom-focus" 
                                        id="ingredient-input" 
                                        placeholder="Type ingredient and press Enter..." 
                                        type="text"
                                    />
                                    <button 
                                        class="bg-secondary text-on-secondary px-lg rounded-lg font-label-caps text-label-caps flex items-center gap-xs hover:opacity-90 active:scale-95 transition-all" 
                                        @click="addIngredientTag"
                                        type="button"
                                    >
                                        ADD
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col gap-base">
                                <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">DESCRIPTION</label>
                                <textarea 
                                    v-model="dishForm.description"
                                    class="bg-surface border border-outline-variant/50 rounded-lg p-md text-body-md custom-focus" 
                                    placeholder="Describe the flavor profile, spices, and heritage story..." 
                                    rows="4"
                                ></textarea>
                            </div>

                            <div class="flex flex-col gap-base">
                                <label class="font-label-caps text-label-caps text-on-surface-variant mb-sm tracking-wider">DIETARY CLASSIFICATION</label>
                                <div class="flex flex-wrap gap-md">
                                    <label class="relative inline-flex items-center cursor-pointer group">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only peer"
                                            v-model="dishForm.isVegan"
                                            @change="dishForm.isVegan && (dishForm.isMeat = false)"
                                        />
                                        <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary border border-outline-variant/20"></div>
                                        <span class="ml-sm font-semibold text-on-surface-variant peer-checked:text-primary">Yetsome (Vegan)</span>
                                    </label>
                                    <label class="relative inline-flex items-center cursor-pointer group">
                                        <input 
                                            type="checkbox" 
                                            class="sr-only peer"
                                            v-model="dishForm.isMeat"
                                            @change="dishForm.isMeat && (dishForm.isVegan = false)"
                                        />
                                        <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary border border-outline-variant/20"></div>
                                        <span class="ml-sm font-semibold text-on-surface-variant peer-checked:text-secondary">Yefisik (Meat)</span>
                                    </label>
                                </div>
                            </div>

                            <div class="border-t border-outline-variant/20 pt-lg"></div>
                            
                            <div class="flex flex-col md:flex-row gap-md">
                                <button 
                                    class="flex-1 bg-primary text-on-primary font-label-caps text-label-caps py-md px-xl rounded-lg hover:bg-primary-container transition-all active:scale-95 shadow-md flex items-center justify-center gap-sm font-bold" 
                                    type="submit"
                                >
                                    <span class="material-symbols-outlined text-lg">save</span>
                                    SAVE TO MENU
                                </button>
                                <button 
                                    @click="discardDraft"
                                    class="px-xl py-md border border-outline-variant text-on-surface-variant font-label-caps text-label-caps rounded-lg hover:bg-surface-variant/30 transition-all active:scale-98" 
                                    type="button"
                                >
                                    DISCARD DRAFT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Live Catalog Preview -->
                <div class="mt-xl pt-xl border-t border-outline-variant/30">
                    <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-lg uppercase tracking-wider">Live Catalog Preview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                        
                        <!-- Catalog Item Card -->
                        <div 
                            v-for="item in catalog" 
                            :key="item.id"
                            class="bg-surface rounded-lg border border-outline-variant/30 overflow-hidden flex flex-col shadow-sm hover:shadow transition-shadow duration-200"
                        >
                            <div 
                                class="h-48 relative bg-surface-container flex items-center justify-center overflow-hidden" 
                            >
                                <img 
                                    v-if="item.image" 
                                    :src="item.image" 
                                    :alt="item.name" 
                                    class="w-full h-full object-cover"
                                />
                                <span v-else class="material-symbols-outlined text-outline-variant text-5xl opacity-40">restaurant</span>
                                <div class="absolute top-md right-md px-sm py-xs bg-tertiary-container/90 backdrop-blur-md rounded text-on-tertiary-container font-price-sm font-bold shadow">
                                    {{ item.price }} ETB
                                </div>
                            </div>
                            <div class="p-md flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-xs mb-xs">
                                        <span 
                                            class="w-2 h-2 rounded-full" 
                                            :class="item.classification === 'Yetsome' ? 'bg-primary' : 'bg-secondary'"
                                        ></span>
                                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">
                                            {{ item.classification }}
                                        </span>
                                    </div>
                                    <h4 class="font-title-md text-title-md text-on-surface font-semibold">{{ item.name }}</h4>
                                    <p class="text-xs text-on-surface-variant mt-xs line-clamp-2 italic leading-relaxed">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Empty Slot Preview -->
                        <div class="bg-surface rounded-lg border border-outline-variant/30 overflow-hidden flex flex-col opacity-50 border-dashed">
                            <div class="h-48 bg-surface-container-low flex items-center justify-center">
                                <span class="material-symbols-outlined text-outline-variant text-4xl">inventory_2</span>
                            </div>
                            <div class="p-md space-y-md">
                                <div class="w-12 h-2 bg-outline-variant/20 rounded"></div>
                                <div class="w-32 h-4 bg-outline-variant/20 rounded"></div>
                                <div class="w-full h-8 bg-outline-variant/10 rounded"></div>
                            </div>
                        </div>

                        <!-- Archive Integrity Info -->
                        <div class="bg-primary-container/5 rounded-lg border border-primary/20 p-lg flex flex-col justify-center items-center text-center shadow-sm">
                            <span class="material-symbols-outlined text-primary text-3xl mb-sm animate-pulse">auto_awesome</span>
                            <h4 class="font-title-md text-title-md text-primary font-bold">Archive Integrity</h4>
                            <p class="text-xs text-on-surface-variant mt-sm leading-relaxed">
                                Data will be verified against the ingredient ledger automatically.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Inventory & Supply Section -->
            <div v-if="activeTab === 'inventory'" class="space-y-xl animate-in fade-in duration-200">
                <div class="mb-lg">
                    <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface font-bold uppercase tracking-tight">Stock Integrity Report</h2>
                    <p class="text-on-surface-variant mt-sm">Precision tracking of heritage ingredients and essential supplies.</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-xl">
                    <!-- Stock Report Table -->
                    <div class="lg:col-span-2 space-y-lg">
                        <div class="bg-surface rounded-lg border border-outline-variant/30 overflow-hidden shadow-sm">
                            <table class="w-full text-left font-price-sm">
                                <thead class="bg-surface-container text-on-surface-variant text-xs uppercase border-b border-outline-variant/20">
                                    <tr>
                                        <th class="px-lg py-md font-label-caps tracking-wider">Ingredient ID</th>
                                        <th class="px-lg py-md font-label-caps tracking-wider">Current Stock</th>
                                        <th class="px-lg py-md font-label-caps tracking-wider">Threshold</th>
                                        <th class="px-lg py-md font-label-caps tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20">
                                    <tr 
                                        v-for="item in inventory" 
                                        :key="item.id"
                                        class="hover:bg-surface-container-low transition-colors duration-150"
                                        :class="{
                                            'bg-error-container/5': item.status === 'Critical',
                                            'bg-tertiary-container/5': item.status === 'Low Stock'
                                        }"
                                    >
                                        <td class="px-lg py-md text-on-surface font-bold">{{ item.id }}</td>
                                        <td class="px-lg py-md font-semibold" :class="item.status === 'Critical' ? 'text-error' : item.status === 'Low Stock' ? 'text-tertiary' : 'text-primary'">
                                            {{ item.stock.toFixed(2) }} {{ item.unit }}
                                        </td>
                                        <td class="px-lg py-md text-outline font-semibold">{{ item.threshold.toFixed(2) }} {{ item.unit }}</td>
                                        <td class="px-lg py-md">
                                            <span 
                                                class="px-sm py-1 rounded text-[10px] font-bold uppercase tracking-wider inline-flex items-center gap-xs"
                                                :class="getStatusBadgeClass(item.status)"
                                            >
                                                <span v-if="item.status !== 'Optimal'" class="w-1.5 h-1.5 rounded-full" :class="item.status === 'Critical' ? 'bg-error animate-ping' : 'bg-tertiary'"></span>
                                                {{ item.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Stock Alert Console -->
                        <div class="p-lg bg-inverse-surface text-inverse-on-surface rounded-lg font-price-sm text-xs space-y-md clinical-border shadow-md">
                            <div class="flex items-center gap-sm text-error border-b border-white/10 pb-sm font-bold">
                                <span class="material-symbols-outlined text-sm">terminal</span>
                                <span class="font-label-caps tracking-wider">AUTOMATED ALERTS LOG</span>
                            </div>
                            <div class="space-y-sm max-h-48 overflow-y-auto custom-scrollbar pr-xs">
                                <p 
                                    v-for="(log, idx) in logs" 
                                    :key="idx"
                                    class="leading-relaxed"
                                >
                                    <span :class="getLogTypeClass(log.type)">[{{ log.type }}]</span> 
                                    <span class="text-white/40 font-mono ml-xs">{{ log.time }}</span> - 
                                    <span class="text-white/90 font-mono ml-xs">{{ log.text }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Supply Request Form -->
                    <div class="flex flex-col gap-lg">
                        <div class="bg-surface-container-low p-lg border border-outline-variant/30 rounded-lg shadow-sm">
                            <h3 class="font-title-md text-title-md text-primary mb-lg uppercase font-bold tracking-tight">Supply Request Form</h3>
                            <form class="space-y-md" @submit.prevent="submitRequisition">
                                <div class="flex flex-col gap-base">
                                    <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">ITEM ID</label>
                                    <input 
                                        v-model="supplyForm.itemId"
                                        class="bg-surface border border-outline-variant/50 rounded-lg p-md text-xs font-semibold custom-focus" 
                                        placeholder="e.g. TEFF_FLOUR_GR_BAG" 
                                        type="text"
                                        list="inventory-suggestions"
                                    />
                                    <datalist id="inventory-suggestions">
                                        <option v-for="item in inventory" :key="item.id" :value="item.id"></option>
                                    </datalist>
                                </div>
                                <div class="flex flex-col gap-base">
                                    <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">QUANTITY</label>
                                    <input 
                                        v-model="supplyForm.quantity"
                                        class="bg-surface border border-outline-variant/50 rounded-lg p-md text-xs font-semibold custom-focus" 
                                        placeholder="0.00" 
                                        type="number"
                                        step="0.01"
                                    />
                                </div>
                                <div class="flex flex-col gap-base">
                                    <label class="font-label-caps text-label-caps text-on-surface-variant tracking-wider">PRIORITY</label>
                                    <select 
                                        v-model="supplyForm.priority"
                                        class="bg-surface border border-outline-variant/50 rounded-lg p-md text-xs font-semibold appearance-none custom-focus bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23004f35%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:0.7em_auto] bg-[right_1.2rem_center] bg-no-repeat"
                                    >
                                        <option>Standard Sourcing</option>
                                        <option>Express Clinical Sourcing</option>
                                        <option>Emergency Local Acquisition</option>
                                    </select>
                                </div>
                                <button 
                                    class="w-full bg-secondary text-on-secondary font-label-caps text-label-caps py-md rounded-lg hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-sm font-bold" 
                                    type="submit"
                                >
                                    <span class="material-symbols-outlined text-sm">send</span>
                                    SUBMIT REQUISITION
                                </button>
                            </form>
                        </div>
                        
                        <div class="bg-primary/5 p-lg border border-primary/20 rounded-lg shadow-sm">
                            <p class="text-xs text-primary leading-relaxed">
                                <span class="font-bold">CURATOR NOTE:</span> Requests submitted before 11:00 AM are processed same-day for heritage spices and standard flour deliveries.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- BottomNavBar (Mobile only) -->
        <nav class="md:hidden fixed bottom-0 w-full z-50 bg-primary shadow-lg flex justify-around items-center h-16 rounded-t-xl">
            <button 
                @click="switchTab('creator')"
                class="flex flex-col items-center justify-center px-3 py-1 active:scale-90 transition-transform" 
                :class="activeTab === 'creator' ? 'bg-primary-fixed-dim/20 text-on-primary font-bold rounded-xl' : 'text-on-primary/70'"
            >
                <span class="material-symbols-outlined">restaurant_menu</span>
                <span class="font-label-caps text-[10px] tracking-wider">Creator</span>
            </button>
            <button 
                @click="switchTab('inventory')"
                class="flex flex-col items-center justify-center px-3 py-1 active:scale-90 transition-transform"
                :class="activeTab === 'inventory' ? 'bg-primary-fixed-dim/20 text-on-primary font-bold rounded-xl' : 'text-on-primary/70'"
            >
                <span class="material-symbols-outlined">inventory_2</span>
                <span class="font-label-caps text-[10px] tracking-wider">Inventory</span>
            </button>
        </nav>

        <!-- Toast Notification Alert -->
        <div 
            v-if="toast.show" 
            class="fixed bottom-6 right-6 z-[70] bg-inverse-surface text-inverse-on-surface px-lg py-md rounded-xl shadow-2xl flex items-center gap-sm border border-outline-variant/20 animate-in fade-in slide-in-from-bottom-5 duration-200"
        >
            <span class="material-symbols-outlined text-primary-fixed">info</span>
            <span class="text-sm font-semibold tracking-wide">{{ toast.message }}</span>
        </div>

    </div>
</template>
