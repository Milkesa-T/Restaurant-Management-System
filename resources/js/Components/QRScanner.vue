<script setup>
/**
 * QRScanner.vue - Universal QR Code / Table Switcher Component
 */
import { ref, computed } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'scan']);

const DINING_ZONES = [
    { zone: 'Zone A — Garden Terrace',  waiter: 'Sarah Chen' },
    { zone: 'Zone B — Main Hall',       waiter: 'Marco Santos' },
    { zone: 'Zone C — Private Booth',   waiter: 'Jenny Kiros' },
    { zone: 'Zone D — Rooftop Lounge',  waiter: 'Alex Tadesse' },
];

const selectedTable = ref(null);
const selectedZoneIdx = ref(0);
const scanning = ref(false);

const currentZone = computed(() => DINING_ZONES[selectedZoneIdx.value]);

const selectManualTable = (t) => {
    selectedTable.value = t;
};

const simulateScan = () => {
    if (!selectedTable.value) return;

    scanning.value = true;
    
    // Simulate camera/scanner animation latency
    setTimeout(() => {
        scanning.value = false;
        emit('scan', {
            table: selectedTable.value,
            zone: currentZone.value.zone,
            waiter: currentZone.value.waiter,
            scannedAt: new Date().toISOString()
        });
        emit('close');
    }, 600);
};

const close = () => {
    selectedTable.value = null;
    emit('close');
};
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-[80] flex items-center justify-center p-6">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="close"></div>

        <!-- Panel -->
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden border border-slate-100">
            <!-- Header -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-700 px-8 pt-8 pb-6 text-white">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Ethiopian Culinary Hub</p>
                        <h3 class="text-xl font-black">Scan Table QR Code</h3>
                        <p class="text-sm text-slate-300 mt-1">Point camera at table QR, or manually select below.</p>
                    </div>
                    <button @click="close" class="p-2 rounded-full hover:bg-white/10 text-slate-300 hover:text-white transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Viewfinder animation -->
                <div class="relative mx-auto w-48 h-48 rounded-2xl overflow-hidden bg-slate-800 border-2 border-emerald-500 shadow-inner">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-7xl text-slate-600">qr_code_2</span>
                    </div>
                    <!-- Scanning line -->
                    <div 
                        id="qr-scan-line" 
                        class="absolute left-0 right-0 h-0.5 bg-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.8)]"
                        :style="{ animation: scanning ? 'none' : 'qrScanLine 2s linear infinite', opacity: scanning ? '0.3' : '1', height: scanning ? '100%' : '2px', background: scanning ? '#fff' : '' }"
                    ></div>
                    <!-- Corner markers -->
                    <div class="absolute top-2 left-2 w-6 h-6 border-t-2 border-l-2 border-emerald-400 rounded-tl-sm"></div>
                    <div class="absolute top-2 right-2 w-6 h-6 border-t-2 border-r-2 border-emerald-400 rounded-tr-sm"></div>
                    <div class="absolute bottom-2 left-2 w-6 h-6 border-b-2 border-l-2 border-emerald-400 rounded-bl-sm"></div>
                    <div class="absolute bottom-2 right-2 w-6 h-6 border-b-2 border-r-2 border-emerald-400 rounded-br-sm"></div>
                </div>
            </div>

            <!-- Manual Table Selector -->
            <div class="px-8 py-6 space-y-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">— Or manually select your table —</p>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Table Number</label>
                    <div class="flex gap-2 flex-wrap">
                        <button
                            v-for="t in 12"
                            :key="t"
                            @click="selectManualTable(t)"
                            :class="[
                                'w-9 h-9 rounded-lg border text-xs font-bold transition-all',
                                selectedTable === t
                                    ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm'
                                    : 'border-slate-200 hover:bg-emerald-50 hover:border-emerald-400 hover:text-emerald-700'
                            ]"
                        >
                            {{ t }}
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Dining Zone</label>
                    <select 
                        v-model="selectedZoneIdx" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                    >
                        <option v-for="(z, idx) in DINING_ZONES" :key="idx" :value="idx">
                            {{ z.zone }}
                        </option>
                    </select>
                </div>

                <div v-if="selectedTable" class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs space-y-1">
                    <div class="flex items-center gap-2 text-emerald-700 font-bold">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        <span>Table #{{ selectedTable }}</span>
                    </div>
                    <p class="text-emerald-600">{{ currentZone.zone }}</p>
                    <p class="text-emerald-600">Waiter: {{ currentZone.waiter }}</p>
                </div>

                <button 
                    @click="simulateScan" 
                    :disabled="!selectedTable || scanning"
                    class="w-full py-3 bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 active:scale-95 transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    <span class="material-symbols-outlined text-base">qr_code_scanner</span>
                    Simulate QR Scan
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes qrScanLine {
    0%   { top: 8px;  opacity: 1; }
    90%  { top: calc(100% - 8px); opacity: 1; }
    100% { top: 8px; opacity: 0; }
}
</style>
