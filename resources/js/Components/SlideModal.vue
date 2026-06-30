<script setup>
import { watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    width: { type: String, default: 'max-w-xl' }, // max-w-sm | max-w-xl | max-w-2xl | max-w-3xl
});

const emit = defineEmits(['close']);

// Lock body scroll when modal is open
watch(() => props.show, (val) => {
    document.body.style.overflow = val ? 'hidden' : '';
});
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="show"
                class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-4"
                @mousedown.self="emit('close')"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

                <!-- Panel -->
                <div
                    class="relative w-full bg-[#13161f] border border-gray-800/80 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]"
                    :class="width"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800/60 shrink-0">
                        <h2 class="text-lg font-bold text-white font-outfit">{{ title }}</h2>
                        <button
                            @click="emit('close')"
                            class="p-2 text-gray-500 hover:text-white hover:bg-gray-800 rounded-xl transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Scrollable body -->
                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        <slot />
                    </div>

                    <!-- Footer (optional slot) -->
                    <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-800/60 shrink-0 flex items-center justify-end gap-3">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active { transition: all 0.2s ease-out; }
.modal-leave-active { transition: all 0.15s ease-in; }
.modal-enter-from { opacity: 0; transform: scale(0.96) translateY(10px); }
.modal-leave-to   { opacity: 0; transform: scale(0.96) translateY(10px); }
</style>
