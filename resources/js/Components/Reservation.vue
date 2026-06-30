<script setup>
/**
 * Reservation.vue - Enhanced Reservation Page Component
 */
import { ref } from 'vue';

const TIME_SLOTS = [
    { label: '12:00 PM — Lunch Service', value: '12:00', icon: 'wb_sunny' },
    { label: '01:30 PM — Midday Seating', value: '13:30', icon: 'wb_sunny' },
    { label: '06:00 PM — Dinner Service', value: '18:00', icon: 'nights_stay' },
    { label: '07:30 PM — Prime Evening', value: '19:30', icon: 'nights_stay' },
    { label: '09:00 PM — Late Night', value: '21:00', icon: 'dark_mode' },
];

const DINING_EXPERIENCES = [
    { id: 'standard',  label: 'Standard Table',     desc: 'Comfortable seating for 2–4 guests.',       icon: 'restaurant',      color: 'emerald' },
    { id: 'booth',     label: 'Private Booth',       desc: 'Semi-private dining for couples/business.', icon: 'night_shelter',   color: 'blue' },
    { id: 'communal',  label: 'Communal Table',      desc: 'Large shared Ethiopian injera experience.', icon: 'groups',          color: 'amber' },
    { id: 'rooftop',   label: 'Rooftop Lounge',      desc: 'Open-air sky dining with panoramic views.', icon: 'deck',            color: 'purple' },
];

const GUEST_COUNTS = ['1', '2', '3', '4', '5', '6', '7', '8+'];

const selectedExperience = ref('standard');
const selectedGuests = ref('2');
const selectedTime = ref('19:30');
const reserveDate = ref(new Date().toISOString().substring(0, 10));

const guestName = ref('');
const guestPhone = ref('');
const guestEmail = ref('');
const occasion = ref('None');
const notes = ref('');

const bookingResult = ref(null);
const loading = ref(false);

const getExperienceColor = (expId) => {
    switch (expId) {
        case 'standard': return 'border-emerald-500 bg-emerald-50/50 text-emerald-800 ring-2 ring-emerald-400';
        case 'booth': return 'border-blue-500 bg-blue-50/50 text-blue-800 ring-2 ring-blue-400';
        case 'communal': return 'border-amber-500 bg-amber-50/50 text-amber-800 ring-2 ring-amber-400';
        case 'rooftop': return 'border-purple-500 bg-purple-50/50 text-purple-800 ring-2 ring-purple-400';
        default: return '';
    }
};

const getExperienceIconColor = (expId) => {
    switch (expId) {
        case 'standard': return 'text-emerald-700 bg-emerald-100';
        case 'booth': return 'text-blue-700 bg-blue-100';
        case 'communal': return 'text-amber-700 bg-amber-100';
        case 'rooftop': return 'text-purple-700 bg-purple-100';
        default: return '';
    }
};

const submitReservation = () => {
    if (!guestName.value || !guestPhone.value) {
        alert('Please fill in your name and phone number.');
        return;
    }

    loading.value = true;
    setTimeout(() => {
        loading.value = false;
        bookingResult.value = {
            id: 'RES-' + Math.floor(100000 + Math.random() * 900000),
            name: guestName.value,
            phone: guestPhone.value,
            date: reserveDate.value,
            time: TIME_SLOTS.find(t => t.value === selectedTime.value)?.label.split(' — ')[0] || selectedTime.value,
            guests: selectedGuests.value,
            experience: DINING_EXPERIENCES.find(e => e.id === selectedExperience.value)?.label || selectedExperience.value,
            occasion: occasion.value !== 'None' ? occasion.value : null
        };
    }, 1200);
};

const resetForm = () => {
    guestName.value = '';
    guestPhone.value = '';
    guestEmail.value = '';
    occasion.value = 'None';
    notes.value = '';
    bookingResult.value = null;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50/30 pt-20 pb-24">
        
        <!-- Booking Confirmation Screen -->
        <div v-if="bookingResult" class="max-w-md mx-auto px-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 px-8 py-10 text-white text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur flex items-center justify-center mx-auto shadow-inner">
                        <span class="material-symbols-outlined text-4xl text-white">done_all</span>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-2xl font-black">Table Confirmed!</h2>
                        <p class="text-emerald-100 text-xs font-mono">Booking Ref: {{ bookingResult.id }}</p>
                    </div>
                </div>

                <div class="px-8 py-8 space-y-6">
                    <div class="space-y-4 text-sm border-b border-slate-100 pb-6">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Guest</span>
                            <span class="font-bold text-slate-800">{{ bookingResult.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Phone</span>
                            <span class="font-mono text-slate-800">{{ bookingResult.phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Date</span>
                            <span class="font-bold text-slate-800">{{ bookingResult.date }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Time</span>
                            <span class="font-bold text-slate-800">{{ bookingResult.time }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Guests</span>
                            <span class="font-bold text-slate-800">{{ bookingResult.guests }} Seated</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Experience</span>
                            <span class="font-bold text-slate-800">{{ bookingResult.experience }}</span>
                        </div>
                        <div v-if="bookingResult.occasion" class="flex justify-between">
                            <span class="text-slate-400">Occasion</span>
                            <span class="font-bold text-amber-600">🎉 {{ bookingResult.occasion }}</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs text-slate-500 leading-relaxed text-center">
                        <p>An SMS confirmation has been sent to <span class="font-bold text-slate-700">{{ bookingResult.phone }}</span>. Please present this screen or the SMS upon arrival.</p>
                    </div>

                    <button 
                        @click="resetForm" 
                        class="w-full py-3 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 active:scale-95 transition-all shadow-lg"
                    >
                        Make Another Booking
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Form Screen -->
        <div v-else class="max-w-4xl mx-auto px-6">
            <!-- Hero Header -->
            <div class="text-center space-y-3 mb-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-200 shadow-sm">
                    <span class="material-symbols-outlined text-sm">event_seat</span>
                    Heritage Dining Experience
                </div>
                <h1 class="text-4xl font-black text-slate-900 leading-tight">Reserve Your Table</h1>
                <p class="text-slate-500 max-w-md mx-auto text-sm leading-relaxed">Secure a curated dining experience in the heart of Ethiopian gastronomy. SMS confirmations sent instantly.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left: Form fields (3/5 width) -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Guest Info Card -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-base">person</span>
                            Guest Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Full Name *</label>
                                <input 
                                    v-model="guestName" 
                                    type="text" 
                                    required 
                                    placeholder="e.g. Sara Abebe"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all" 
                                />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Phone Number *</label>
                                <input 
                                    v-model="guestPhone" 
                                    type="tel" 
                                    required 
                                    placeholder="e.g. +251 911 000 000"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all" 
                                />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Email (optional)</label>
                                <input 
                                    v-model="guestEmail" 
                                    type="email" 
                                    placeholder="sara@example.com"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all" 
                                />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Occasion</label>
                                <select 
                                    v-model="occasion"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all"
                                >
                                    <option value="None">No Special Occasion</option>
                                    <option value="Birthday">Birthday 🎂</option>
                                    <option value="Anniversary">Anniversary 💍</option>
                                    <option value="Date Night">Date Night ❤️</option>
                                    <option value="Business">Business Meeting 💼</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Experience Card -->
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-base">deck</span>
                            Select Dining Experience
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div 
                                v-for="exp in DINING_EXPERIENCES" 
                                :key="exp.id"
                                @click="selectedExperience = exp.id"
                                :class="[
                                    'border rounded-2xl p-4 cursor-pointer transition-all hover:shadow-md flex items-start gap-3',
                                    selectedExperience === exp.id ? getExperienceColor(exp.id) : 'border-slate-100 hover:border-slate-200 bg-white'
                                ]"
                            >
                                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0', getExperienceIconColor(exp.id)]">
                                    <span class="material-symbols-outlined">{{ exp.icon }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 text-sm mb-0.5">{{ exp.label }}</p>
                                    <p class="text-xs text-slate-400 leading-normal">{{ exp.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Seating options (2/5 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-base">date_range</span>
                            Schedule Seating
                        </h3>

                        <!-- Date -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Date</label>
                            <input 
                                v-model="reserveDate" 
                                type="date" 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all" 
                            />
                        </div>

                        <!-- Guests count -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Guests</label>
                            <div class="grid grid-cols-4 gap-2">
                                <button
                                    v-for="g in GUEST_COUNTS"
                                    :key="g"
                                    @click="selectedGuests = g"
                                    :class="[
                                        'py-2.5 rounded-xl border text-xs font-bold font-mono transition-all',
                                        selectedGuests === g
                                            ? 'bg-emerald-600 border-emerald-600 text-white shadow-md'
                                            : 'border-slate-100 hover:bg-slate-50 text-slate-700'
                                    ]"
                                >
                                    {{ g }}
                                </button>
                            </div>
                        </div>

                        <!-- Time slot -->
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Preferred Seating</label>
                            <div class="space-y-2">
                                <button
                                    v-for="t in TIME_SLOTS"
                                    :key="t.value"
                                    @click="selectedTime = t.value"
                                    :class="[
                                        'w-full px-4 py-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-between',
                                        selectedTime === t.value
                                            ? 'bg-emerald-600 border-emerald-600 text-white shadow-md'
                                            : 'border-slate-100 hover:bg-slate-50 text-slate-700'
                                    ]"
                                >
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">{{ t.icon }}</span>
                                        <span>{{ t.label.split(' — ')[0] }}</span>
                                    </span>
                                    <span :class="['text-[10px] uppercase font-mono tracking-wider', selectedTime === t.value ? 'text-emerald-100' : 'text-slate-400']">
                                        {{ t.label.split(' — ')[1] }}
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Dietary Notes / Requests</label>
                            <textarea 
                                v-model="notes" 
                                rows="3" 
                                placeholder="e.g. Gluten-free, quiet table, child seat required…"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all resize-none"
                            ></textarea>
                        </div>

                        <button 
                            @click="submitReservation" 
                            :disabled="loading"
                            class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-bold text-sm hover:bg-emerald-700 active:scale-95 transition-all shadow-xl shadow-emerald-100 flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <span class="material-symbols-outlined text-base">restaurant_menu</span>
                            <span>{{ loading ? 'Securing Table...' : 'Confirm Seating' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
