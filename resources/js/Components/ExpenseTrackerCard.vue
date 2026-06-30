<script setup>
import { ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
        // Example: [{ id: 1, name: 'Utilities' }, { id: 2, name: 'Maintenance' }]
    },
    recentExpenses: {
        type: Array,
        default: () => [],
        // Example: [{ id: 1, amount: 45.00, category_id: 1, date: 'Today', description: 'Plumbing' }]
    }
});

const emit = defineEmits(['add-expense']);

const form = ref({
    category_id: '',
    amount: '',
    description: '',
});

function submit() {
    if (!form.value.amount || !form.value.category_id) return;
    
    emit('add-expense', { ...form.value });
    
    // Reset
    form.value.amount = '';
    form.value.description = '';
}

function getCategoryName(id) {
    const cat = props.categories.find(c => c.id === id);
    return cat ? cat.name : 'Unknown';
}
</script>

<template>
    <div class="bg-[#1e1e24]/80 rounded-2xl border border-gray-800 backdrop-blur-md overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-800 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Expense Tracker</h3>
            <div class="h-8 w-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        
        <div class="p-5 flex-1 flex flex-col gap-6">
            <!-- Quick Add Form -->
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Category</label>
                        <select 
                            v-model="form.category_id" 
                            required
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                        >
                            <option value="" disabled>Select Category...</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Amount ($)</label>
                        <input 
                            v-model.number="form.amount" 
                            type="number" 
                            step="0.01" 
                            required
                            placeholder="0.00"
                            class="w-full bg-gray-900/50 border border-gray-700 text-gray-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                        >
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1">Description (Optional)</label>
                    <input 
                        v-model="form.description" 
                        type="text" 
                        placeholder="What was this for?"
                        class="w-full bg-gray-900/50 border border-gray-700 text-gray-200 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5"
                    >
                </div>
                <button 
                    type="submit"
                    class="w-full px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 rounded-lg shadow-[0_0_15px_rgba(37,99,235,0.3)] transition-all flex justify-center items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Log Expense
                </button>
            </form>

            <hr class="border-gray-800">

            <!-- Recent List -->
            <div>
                <h4 class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Recent Expenses</h4>
                <div v-if="recentExpenses.length === 0" class="text-sm text-gray-500 italic text-center py-4">
                    No expenses logged recently.
                </div>
                <div v-else class="space-y-3 max-h-48 overflow-y-auto pr-1">
                    <div 
                        v-for="expense in recentExpenses" 
                        :key="expense.id"
                        class="flex items-center justify-between p-3 rounded-lg bg-gray-900/40 border border-gray-800 hover:border-gray-700 transition-colors"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-200">${{ parseFloat(expense.amount).toFixed(2) }}</p>
                            <p class="text-xs text-gray-500">{{ getCategoryName(expense.category_id) }} &bull; {{ expense.date }}</p>
                        </div>
                        <div class="text-right max-w-[120px]">
                            <p class="text-xs text-gray-400 truncate">{{ expense.description || 'No description' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
