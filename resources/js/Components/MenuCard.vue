<script setup>
/**
 * MenuCard.vue - Dish/Menu Card component for Customer and Waiter pages
 * Props: dish (Object), onAdd (Function)
 */
import { computed } from 'vue';

const props = defineProps({
    dish: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['add']);

const DIETARY_COLORS = {
    vegan:      { bg: 'bg-emerald-100', text: 'text-emerald-700', border: 'border-emerald-200', label: 'Vegan' },
    vegetarian: { bg: 'bg-green-100',   text: 'text-green-700',   border: 'border-green-200',   label: 'Vegetarian' },
    halal:      { bg: 'bg-blue-100',    text: 'text-blue-700',    border: 'border-blue-200',    label: 'Halal' },
    spicy:      { bg: 'bg-red-100',     text: 'text-red-700',     border: 'border-red-200',     label: '🌶 Spicy' },
    'gluten-free': { bg: 'bg-amber-100', text: 'text-amber-700', border: 'border-amber-200',   label: 'GF' },
};

const dietaryBadges = computed(() =>
    (props.dish.dietary || []).map(d => DIETARY_COLORS[d] || { bg: 'bg-slate-100', text: 'text-slate-600', border: 'border-slate-200', label: d })
);

const categoryLabel = computed(() => (props.dish.category || '').split(' ')[0]);
</script>

<template>
    <div class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 flex flex-col border border-slate-100">
        <!-- Image -->
        <div class="h-56 overflow-hidden relative bg-slate-50 flex items-center justify-center">
            <img
                v-if="dish.image"
                :src="dish.image"
                :alt="dish.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <span v-else class="material-symbols-outlined text-5xl text-slate-300">restaurant</span>

            <!-- Price badge -->
            <div class="absolute top-4 right-4 bg-white/95 backdrop-blur px-3 py-1 rounded-lg font-mono text-sm text-emerald-700 shadow-sm font-bold border border-emerald-100">
                {{ dish.price }} ETB
            </div>
        </div>

        <!-- Body -->
        <div class="p-5 flex flex-col flex-grow space-y-3 justify-between">
            <div class="space-y-2">
                <!-- Dietary badges + category -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span
                        v-for="badge in dietaryBadges"
                        :key="badge.label"
                        :class="[badge.bg, badge.text, badge.border, 'px-2 py-0.5 rounded-full text-[10px] font-bold border']"
                    >{{ badge.label }}</span>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ categoryLabel }}</span>
                </div>

                <h3 class="font-bold text-base text-slate-900 leading-snug">{{ dish.name }}</h3>
                <p class="text-slate-500 text-xs leading-relaxed line-clamp-3">{{ dish.description || 'No description available.' }}</p>
            </div>

            <!-- Add button -->
            <button
                @click="emit('add', dish)"
                class="w-full bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-800 py-3 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 active:scale-95 duration-150 group/btn"
            >
                <span class="material-symbols-outlined text-base group-hover/btn:scale-110 transition-transform">add_shopping_cart</span>
                <span>Add to Order</span>
            </button>
        </div>
    </div>
</template>
