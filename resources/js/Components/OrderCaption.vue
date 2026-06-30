<script setup>
/**
 * OrderCaption.vue - Session caption/occasion modal
 * Asks the customer to set a caption before their first order.
 * Emits 'confirmed' with the caption string, or 'skipped'.
 */
import { ref, onMounted } from 'vue';

const SESSION_KEY = 'rms_session_caption';

const emit = defineEmits(['confirmed', 'skipped']);

const show = ref(false);
const captionInput = ref('');
const savedCaption = ref('');

const EXAMPLES = [
    { label: 'Birthday 🎂',    text: 'Birthday Celebration 🎂', color: 'emerald' },
    { label: 'Business Lunch', text: 'Business Lunch',           color: 'blue' },
    { label: 'Family',         text: 'Family Gathering',         color: 'amber' },
    { label: 'Date Night ❤️',  text: 'Date Night ❤️',           color: 'pink' },
];

const colorMap = {
    emerald: 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100',
    blue:    'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
    amber:   'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
    pink:    'bg-pink-50 text-pink-700 border-pink-200 hover:bg-pink-100',
};

onMounted(() => {
    savedCaption.value = sessionStorage.getItem(SESSION_KEY) || '';
});

/** Call this to open the modal before allowing an add-to-cart action */
function requireCaption(cb) {
    if (savedCaption.value) { cb(savedCaption.value); return; }
    _pendingCb = cb;
    captionInput.value = '';
    show.value = true;
}

let _pendingCb = null;

function setExample(text) {
    captionInput.value = text;
}

function confirm() {
    savedCaption.value = captionInput.value.trim();
    sessionStorage.setItem(SESSION_KEY, savedCaption.value);
    show.value = false;
    emit('confirmed', savedCaption.value);
    if (_pendingCb) { _pendingCb(savedCaption.value); _pendingCb = null; }
}

function skip() {
    savedCaption.value = '';
    sessionStorage.removeItem(SESSION_KEY);
    show.value = false;
    emit('skipped');
    if (_pendingCb) { _pendingCb(''); _pendingCb = null; }
}

function clearCaption() {
    savedCaption.value = '';
    sessionStorage.removeItem(SESSION_KEY);
}

function openManually() {
    captionInput.value = savedCaption.value;
    show.value = true;
}

defineExpose({ requireCaption, clearCaption, openManually, savedCaption });
</script>

<template>
    <!-- Banner shown when caption is set -->
    <div
        v-if="savedCaption"
        class="fixed top-16 left-0 right-0 z-30 bg-emerald-600 text-white px-6 py-2 flex items-center justify-between text-xs font-bold shadow-sm"
    >
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">rate_review</span>
            <span>Order Caption: <span class="font-mono text-emerald-100">"{{ savedCaption }}"</span></span>
        </div>
        <button @click="openManually" class="flex items-center gap-1 px-2 py-1 bg-white/20 rounded-full hover:bg-white/30 transition-colors">
            <span class="material-symbols-outlined text-xs">edit</span>
            <span>Change</span>
        </button>
    </div>

    <!-- Modal -->
    <Transition name="fade">
        <div v-if="show" class="fixed inset-0 z-[90] flex items-center justify-center p-6">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="skip" />

            <!-- Panel -->
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100">
                <!-- Header -->
                <div class="px-8 pt-8 pb-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mb-4 shadow-inner">
                        <span class="material-symbols-outlined text-3xl text-emerald-700">rate_review</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 leading-tight">Set Your Order Caption</h3>
                    <p class="text-sm text-slate-500 mt-1">Add a short caption or occasion note — it appears on the kitchen and waiter screens.</p>
                </div>

                <!-- Example chips -->
                <div class="px-8 py-3 flex gap-2 flex-wrap">
                    <button
                        v-for="ex in EXAMPLES"
                        :key="ex.label"
                        @click="setExample(ex.text)"
                        :class="['px-3 py-1 rounded-full text-xs font-bold border transition-colors', colorMap[ex.color]]"
                    >{{ ex.label }}</button>
                </div>

                <!-- Input -->
                <div class="px-8 py-4">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Your Caption / Note</label>
                    <input
                        v-model="captionInput"
                        type="text"
                        maxlength="80"
                        placeholder="e.g. No peanuts — allergy, Anniversary dinner…"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all"
                    />
                    <div class="flex justify-between mt-1">
                        <span class="text-[10px] text-slate-400">Optional but recommended</span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ captionInput.length }}/80</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-8 pb-8 flex gap-3">
                    <button
                        @click="skip"
                        class="flex-1 py-3 border border-slate-200 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 active:scale-95 transition-all"
                    >Skip</button>
                    <button
                        @click="confirm"
                        class="flex-[2] py-3 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 active:scale-95 transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-200"
                    >
                        <span class="material-symbols-outlined text-base">done_all</span>
                        Confirm Caption
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
