<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    menuItem: {
        type: Object,
        required: true,
        // Example: { id: 1, name: 'Classic Cheeseburger', image: '...' }
    },
    availableIngredients: {
        type: Array,
        required: true,
        // Example: [{ id: 1, name: 'Ground Beef', unit: 'kg' }, { id: 2, name: 'Cheddar Slice', unit: 'pcs' }]
    },
    initialRecipe: {
        type: Array,
        default: () => [],
        // Example: [{ ingredient_id: 1, quantity: 0.2 }, { ingredient_id: 2, quantity: 1 }]
    }
});

const emit = defineEmits(['save']);

const recipeItems = ref([...props.initialRecipe]);
const searchQuery = ref('');

const filteredIngredients = computed(() => {
    if (!searchQuery.value) return props.availableIngredients;
    const lowerQuery = searchQuery.value.toLowerCase();
    return props.availableIngredients.filter(i => i.name.toLowerCase().includes(lowerQuery));
});

function addIngredient(ingredient) {
    if (!recipeItems.value.find(i => i.ingredient_id === ingredient.id)) {
        recipeItems.value.push({
            ingredient_id: ingredient.id,
            ingredient: ingredient,
            quantity: 1
        });
    }
}

function removeIngredient(index) {
    recipeItems.value.splice(index, 1);
}

function getIngredientDetails(id) {
    return props.availableIngredients.find(i => i.id === id);
}
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full">
        <!-- Left: Available Ingredients -->
        <div class="lg:col-span-5 bg-[#1e1e24]/80 rounded-2xl border border-gray-800 flex flex-col overflow-hidden backdrop-blur-md">
            <div class="p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-gray-100 mb-3">Inventory Items</h3>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Search ingredients..." 
                        class="w-full bg-gray-900/50 border border-gray-700 text-gray-300 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block pl-10 p-2.5 placeholder-gray-500"
                    >
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                <div class="space-y-1">
                    <button 
                        v-for="ingredient in filteredIngredients" 
                        :key="ingredient.id"
                        @click="addIngredient(ingredient)"
                        class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-gray-800/50 text-left transition-colors group"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-200">{{ ingredient.name }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ ingredient.unit }}</p>
                        </div>
                        <svg class="w-5 h-5 text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Recipe Builder -->
        <div class="lg:col-span-7 bg-[#1e1e24]/80 rounded-2xl border border-gray-800 flex flex-col backdrop-blur-md">
            <div class="p-5 border-b border-gray-800 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Bill of Materials</h3>
                    <p class="text-sm text-gray-400">Recipe for <span class="text-emerald-400 font-medium">{{ menuItem.name }}</span></p>
                </div>
                <button 
                    @click="emit('save', recipeItems)"
                    class="px-5 py-2 text-sm font-medium text-emerald-950 bg-emerald-400 hover:bg-emerald-300 rounded-lg shadow-[0_0_15px_rgba(52,211,153,0.3)] transition-all"
                >
                    Save Recipe
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-5">
                <div v-if="recipeItems.length === 0" class="h-full flex flex-col items-center justify-center text-center text-gray-500 space-y-3">
                    <svg class="w-12 h-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p>No ingredients added yet.<br>Select items from the left to build this recipe.</p>
                </div>

                <div v-else class="space-y-3">
                    <transition-group name="list">
                        <div 
                            v-for="(item, index) in recipeItems" 
                            :key="item.ingredient_id"
                            class="flex items-center gap-4 p-4 rounded-xl bg-gray-900/40 border border-gray-700/50"
                        >
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-200">
                                    {{ item.ingredient ? item.ingredient.name : getIngredientDetails(item.ingredient_id)?.name }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input 
                                    v-model.number="item.quantity" 
                                    type="number" 
                                    step="0.01" 
                                    min="0"
                                    class="w-24 bg-gray-900 border border-gray-600 text-gray-200 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block p-2 text-right"
                                >
                                <span class="text-sm text-gray-500 uppercase w-8">
                                    {{ item.ingredient ? item.ingredient.unit : getIngredientDetails(item.ingredient_id)?.unit }}
                                </span>
                            </div>
                            <button @click="removeIngredient(index)" class="p-2 text-gray-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </transition-group>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
