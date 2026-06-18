<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import debounce from 'lodash/debounce';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { 
    InboxArrowDownIcon, 
    PaperAirplaneIcon, 
    DocumentChartBarIcon, 
    UsersIcon, 
    Cog6ToothIcon,
    QuestionMarkCircleIcon,
    ArrowRightOnRectangleIcon,
    MagnifyingGlassIcon,
    BellIcon,
    FolderOpenIcon
} from '@heroicons/vue/24/outline';
import { Squares2X2Icon } from '@heroicons/vue/24/solid';

const page = usePage();
const user = page.props.auth.user;
const notifications = page.props.notifications || [];

const navigation = [
    { 
        group: 'Menu Utama',
        items: [
            { name: 'Dashboard', route: 'dashboard', icon: Squares2X2Icon, current: route().current('dashboard') },
        ]
    },
    {
        group: 'Manajemen Arsip',
        items: [
            { name: 'Surat Masuk', route: 'incoming.index', icon: InboxArrowDownIcon, current: route().current('incoming.*') },
            { name: 'Surat Keluar', route: 'outgoing.index', icon: PaperAirplaneIcon, current: route().current('outgoing.*') },
            { name: 'Laporan', route: 'reports.index', icon: DocumentChartBarIcon, current: route().current('reports.*') },
        ]
    },
    {
        group: 'Pengaturan',
        items: [
            { name: 'Master Data User', route: 'users.index', icon: UsersIcon, current: route().current('users.*'), adminOnly: true },
            { name: 'Profil Akun', route: 'profile.edit', icon: Cog6ToothIcon, current: route().current('profile.*') },
        ]
    }
];

// Global Search State
const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const showDropdown = ref(false);
const showProfileMenu = ref(false);

// Mobile sidebar state
const showMobileMenu = ref(false);

const performSearch = debounce(async (query) => {
    if (!query) {
        searchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await axios.get(route('global.search'), { params: { q: query } });
        searchResults.value = response.data;
    } catch (error) {
        console.error('Search failed:', error);
    } finally {
        isSearching.value = false;
    }
}, 300);

watch(searchQuery, (newVal) => {
    showDropdown.value = newVal.length > 0;
    performSearch(newVal);
});

// Click outside to close dropdown
const closeDropdown = (e) => {
    if (!e.target.closest('.search-container')) {
        showDropdown.value = false;
    }
    if (!e.target.closest('.notification-container')) {
        showNotifications.value = false;
    }
    if (!e.target.closest('.profile-menu-container')) {
        showProfileMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
    
    // Check if there is a message flash from backend
    if (page.props.flash?.message) {
        toast.success(page.props.flash.message);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});

// Notifications State
const showNotifications = ref(false);
</script>

<template>
    <div class="flex h-screen bg-[#F8FAFC] font-sans text-navy overflow-hidden">
        
        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="showMobileMenu" 
            class="fixed inset-0 bg-navy/50 z-40 lg:hidden transition-opacity"
            @click="showMobileMenu = false"
        ></div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-[260px] bg-white border-r border-slate-200 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
            :class="showMobileMenu ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo Area -->
            <div class="h-16 lg:h-24 flex items-center justify-between px-6 lg:px-8 border-b border-slate-100 lg:border-none">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <img src="/logo/logo.png" alt="E-Surat Logo" class="h-8 lg:h-10 w-auto" />
                    <div class="flex flex-col">
                        <span class="font-display text-lg lg:text-xl font-bold leading-none text-navy">E-Surat</span>
                        <span class="text-[9px] lg:text-[10px] text-slate-secondary font-medium tracking-widest uppercase mt-1">Bapenda Kab. Batu Bara</span>
                    </div>
                </Link>
                <!-- Close Button (Mobile Only) -->
                <button 
                    @click="showMobileMenu = false"
                    class="lg:hidden p-2 text-slate-400 hover:text-navy hover:bg-slate-50 rounded-lg transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 mt-4 overflow-y-auto">
                <template v-for="navGroup in navigation" :key="navGroup.group">
                    <!-- Tampilkan judul grup jika ada item yang diizinkan untuk user ini -->
                    <div class="mt-6 mb-2 px-4" v-if="navGroup.items.some(item => !item.adminOnly || (item.adminOnly && user.role === 'admin'))">
                        <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase">{{ navGroup.group }}</h3>
                    </div>
                    
                    <div class="space-y-1">
                        <template v-for="item in navGroup.items" :key="item.name">
                            <Link 
                                v-if="!item.adminOnly || (item.adminOnly && user.role === 'admin')"
                                :href="item.route === '#' ? '#' : route(item.route)" 
                                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200"
                                :class="[item.current ? 'bg-sage/10 text-sage' : 'text-slate-secondary hover:bg-slate-50 hover:text-navy']"
                            >
                                <component :is="item.icon" class="h-5 w-5" :class="item.current ? 'text-sage' : 'text-slate-secondary'" />
                                {{ item.name }}
                            </Link>
                        </template>
                    </div>
                </template>
            </nav>

            <!-- Bottom Area -->
            <div class="p-6 mt-auto">
                <!-- Logout -->
                <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-3 px-4 py-2 w-full text-slate-secondary hover:text-status-error transition-colors text-sm font-medium">
                    <ArrowRightOnRectangleIcon class="h-5 w-5" />
                    Keluar Sistem
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            
            <!-- Topbar -->
            <header class="h-16 lg:h-24 flex items-center justify-between px-4 lg:px-10 flex-shrink-0 bg-white lg:bg-transparent border-b border-slate-200 lg:border-none z-30">
                
                <!-- Mobile Menu Button & Search Trigger -->
                <div class="flex items-center gap-2 lg:hidden">
                    <button 
                        @click="showMobileMenu = true"
                        class="p-2 text-slate-500 hover:text-navy hover:bg-slate-100 rounded-lg transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <!-- Mobile Logo (Shows only on mobile topbar) -->
                    <span class="font-display text-lg font-bold text-navy ml-2">E-Surat</span>
                </div>

                <!-- Search -->
                <div class="relative w-full max-w-[200px] sm:max-w-[300px] lg:max-w-[400px] search-container z-50 hidden md:block">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-4 w-4 lg:h-5 lg:w-5 text-slate-400" />
                    </div>
                    <input 
                        v-model="searchQuery"
                        @focus="showDropdown = searchQuery.length > 0"
                        type="text" 
                        class="block w-full pl-10 lg:pl-11 pr-4 py-2 lg:py-2.5 bg-slate-100 lg:bg-slate-200/50 border-transparent rounded-xl text-xs lg:text-sm placeholder-slate-400 focus:border-sage focus:ring-sage focus:bg-white transition-colors" 
                        placeholder="Cari surat..."
                    >
                    <!-- Search Dropdown -->
                    <div v-if="showDropdown" class="absolute mt-2 w-full bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden">
                        <div v-if="isSearching" class="p-4 text-center text-sm text-slate-500">
                            Mencari...
                        </div>
                        <div v-else-if="searchResults.length === 0" class="p-4 text-center text-sm text-slate-500">
                            Tidak ditemukan data yang cocok.
                        </div>
                        <div v-else class="max-h-80 overflow-y-auto">
                            <Link 
                                v-for="result in searchResults" 
                                :key="result.id"
                                :href="result.url"
                                class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-50 last:border-0 transition-colors"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg" :class="result.type === 'incoming' ? 'bg-sage/10 text-sage' : 'bg-status-warning/10 text-status-warning'">
                                        <InboxArrowDownIcon v-if="result.type === 'incoming'" class="h-4 w-4" />
                                        <PaperAirplaneIcon v-else class="h-4 w-4" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-navy truncate">{{ result.title }}</p>
                                        <p class="text-xs text-slate-500 truncate mt-0.5">{{ result.subtitle }}</p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Right Menu -->
                <div class="flex items-center gap-3 lg:gap-6">
                    <!-- Mobile Search Icon (triggers dropdown/modal in future or just simple toggle) -->
                    <button class="md:hidden p-2 text-slate-500 hover:text-navy hover:bg-slate-100 rounded-lg">
                        <MagnifyingGlassIcon class="h-5 w-5" />
                    </button>

                    <div class="relative notification-container z-50">
                        <button 
                            @click="showNotifications = !showNotifications"
                            class="relative text-slate-500 hover:text-navy transition-colors focus:outline-none"
                        >
                            <BellIcon class="h-6 w-6" />
                            <span v-if="notifications.length > 0" class="absolute top-0 right-0 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-status-error opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-status-error ring-2 ring-[#F8FAFC]"></span>
                            </span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div v-if="showNotifications" class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden origin-top-right">
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                                <h3 class="text-sm font-bold text-navy">Notifikasi Baru</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Surat masuk hari ini</p>
                            </div>
                            
                            <div class="max-h-80 overflow-y-auto">
                                <template v-if="notifications.length > 0">
                                    <Link 
                                        v-for="notif in notifications" 
                                        :key="notif.id"
                                        :href="route('incoming.index')"
                                        class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-50 last:border-0 transition-colors"
                                    >
                                        <div class="flex gap-3 items-start">
                                            <div class="mt-0.5 p-1.5 rounded-full bg-sage/10 text-sage shrink-0">
                                                <InboxArrowDownIcon class="h-4 w-4" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-navy line-clamp-2 leading-snug">{{ notif.message }}</p>
                                                <p class="text-[10px] text-slate-400 mt-1">{{ notif.time }}</p>
                                            </div>
                                        </div>
                                    </Link>
                                </template>
                                <div v-else class="p-6 text-center">
                                    <BellIcon class="h-8 w-8 text-slate-300 mx-auto mb-2" />
                                    <p class="text-sm text-slate-500 font-medium">Belum ada notifikasi</p>
                                    <p class="text-xs text-slate-400 mt-1">Surat masuk hari ini akan muncul di sini</p>
                                </div>
                            </div>
                            
                            <Link 
                                v-if="notifications.length > 0"
                                :href="route('incoming.index')"
                                class="block px-4 py-2 text-center text-xs font-bold text-sage hover:text-navy bg-slate-50/50 transition-colors border-t border-slate-100"
                            >
                                Lihat Semua Surat Masuk
                            </Link>
                        </div>
                    </div>
                    
                    <div class="relative profile-menu-container">
                        <button 
                            @click="showProfileMenu = !showProfileMenu"
                            class="flex items-center gap-2 lg:gap-3 lg:border-l border-slate-200 lg:pl-6 text-left hover:bg-slate-50 p-1 lg:p-2 rounded-xl transition-colors focus:outline-none"
                        >
                            <div class="hidden lg:flex flex-col text-right">
                                <span class="text-sm font-bold text-navy">{{ user.name }}</span>
                                <span class="text-xs font-medium text-slate-secondary capitalize">{{ user.role }}</span>
                            </div>
                            <div class="h-8 w-8 lg:h-10 lg:w-10 rounded-full bg-navy flex items-center justify-center text-white font-bold overflow-hidden ring-2 ring-transparent group-hover:ring-sage/30 transition-all">
                                <img :src="'https://ui-avatars.com/api/?name='+user.name+'&background=0F172A&color=fff'" alt="Avatar" class="h-full w-full object-cover">
                            </div>
                        </button>

                        <!-- Profile Dropdown -->
                        <div v-if="showProfileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden origin-top-right z-50">
                            <div class="py-1">
                                <Link 
                                    :href="route('profile.edit')" 
                                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-navy hover:bg-slate-50 transition-colors"
                                >
                                    <Cog6ToothIcon class="h-4 w-4" />
                                    Profil Akun
                                </Link>
                                <div class="h-px bg-slate-100 my-1"></div>
                                <Link 
                                    :href="route('logout')" 
                                    method="post" 
                                    as="button" 
                                    class="w-full flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-status-error hover:bg-status-error/5 transition-colors text-left"
                                >
                                    <ArrowRightOnRectangleIcon class="h-4 w-4" />
                                    Keluar Sistem
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content (Scrollable) -->
            <main class="flex-1 overflow-y-auto px-4 lg:px-10 pb-10">
                <slot />
            </main>
        </div>
    </div>
</template>