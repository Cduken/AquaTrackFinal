// Hero.vue
<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, watch } from "vue";
import Navigation from "./Header/Navigation.vue";
import gsap from "gsap";
import Swal from "sweetalert2";
import { defineProps } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
    dashboardStats: {
        type: Object,
        default: () => ({
            active_users: 0,
            resolution_percentage: 0,
        }),
    },
});

const stats = ref({
    active_users: props.dashboardStats?.active_users || 0,
    resolution_percentage: props.dashboardStats?.resolution_percentage || 0,
});

let refreshInterval;

const showImageTooltip = ref(false);

watch(
    () => props.dashboardStats,
    (newStats) => {
        if (newStats) {
            stats.value.active_users = newStats.active_users || 0;
            stats.value.resolution_percentage =
                newStats.resolution_percentage || 0;
        }
    },
    { immediate: true, deep: true }
);

const refreshStats = () => {
    router.reload({
        only: ["dashboardStats"],
        preserveScroll: true,
        preserveState: true,
    });
};

onMounted(() => {
    if (props.dashboardStats) {
        stats.value = {
            active_users: props.dashboardStats.active_users || 0,
            resolution_percentage:
                props.dashboardStats.resolution_percentage || 0,
        };
    }

    refreshInterval = setInterval(() => {
        refreshStats();
    }, 30000);

    const tl = gsap.timeline();

    tl.fromTo(
        ".hero-badge",
        { scale: 0, opacity: 0 },
        { scale: 1, opacity: 1, duration: 0.5, ease: "back.out(1.7)" }
    );
    tl.fromTo(
        ".hero-content",
        { y: 30, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.8, ease: "power2.out" },
        "-=0.2"
    );
    tl.fromTo(
        "h1 .word",
        { y: 50, opacity: 0 },
        {
            y: 0,
            opacity: 1,
            duration: 0.8,
            stagger: 0.1,
            ease: "back.out(1.7)",
        },
        "-=0.4"
    );
    tl.fromTo(
        ".subheadline",
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" },
        "-=0.4"
    );

    tl.fromTo(
        ".cta-buttons",
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" },
        "-=0.3"
    );
    tl.fromTo(
        ".hero-image-container",
        { scale: 0.8, opacity: 0 },
        { scale: 1, opacity: 1, duration: 1, ease: "elastic.out(1, 0.5)" },
        "-=0.8"
    );
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

const handleTrackReport = () => {
    console.log("Track report clicked");
};

const handleReportSuccess = (data) => {
    trackingInfo.value = {
        code: data.trackingCode,
        date: data.dateSubmitted,
    };
    showSuccessModal.value = true;

    Swal.fire({
        icon: "success",
        title: "Report Submitted Successfully!",
        toast: true,
        position: "bottom-end",
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: false,
        background: "#0f172a",
        iconColor: "#38bdf8",
        color: "#e2e8f0",
        width: 380,
        padding: "0.75rem",
    });

    setTimeout(() => {
        refreshStats();
    }, 2000);
};
</script>

<template>
    <main id="home" class="relative w-full min-h-screen overflow-hidden">
        <!-- Navigation -->
        <Navigation />

        <div
            class="relative px-4 sm:px-8 lg:px-[120px] flex flex-col lg:flex-row gap-12 lg:gap-20 items-center justify-center min-h-[85vh] py-6 lg:py-0"
        >
            <!-- Left Content -->
            <div
                class="hero-content flex flex-col max-w-[700px] order-2 lg:order-1 text-center lg:text-left lg:w-3/5 z-10"
            >
                <!-- Badge -->
                <div
                    class="hero-badge mb-6 inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 backdrop-blur-sm border border-blue-400/30 rounded-full px-4 py-2 w-fit mx-auto lg:mx-0"
                >
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-cyan-400"
                        ></span>
                    </span>
                    <span class="text-sm font-medium text-blue-200"
                        >Clarin, Bohol Water District</span
                    >
                </div>

                <!-- Main Heading with Individual Word Animation -->
                <h1
                    class="text-6xl sm:text-6xl lg:text-7xl mb-6 leading-tight font-semibold"
                >
                    <span class="block mb-2">
                        <span
                            class="word inline-block text-transparent bg-clip-text bg-gradient-to-r from-blue-200 via-cyan-200 to-blue-300"
                        >
                            Clarin
                        </span>
                        <span
                            class="word inline-block text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 to-blue-300 ml-3"
                        >
                            Water
                        </span>
                    </span>
                    <span class="block">
                        <span
                            class="word inline-block text-transparent leading-snug bg-clip-text bg-gradient-to-r from-blue-300 via-cyan-400 to-blue-400 break-words"
                        >
                            Management
                        </span>
                    </span>
                </h1>

                <!-- Subheadline -->
                <p
                    class="subheadline text-slate-300 max-w-[600px] mb-8 text-lg lg:text-xl leading-relaxed mx-auto lg:mx-0"
                >
                    Your complete water service solution. Report issues, track
                    consumption, manage bills, and access detailed analytics
                    built for Clarin's community.
                </p>

                <!-- CTA Buttons -->
                <div
                    class="cta-buttons flex flex-col sm:flex-row gap-4 justify-center lg:justify-start"
                >
                    <Link
                        :href="route('report-issue.create')"
                        class="group relative inline-flex items-center justify-center gap-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-8 py-4 rounded-xl font-semibold shadow-2xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-105 overflow-hidden"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        ></div>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-700/20 to-cyan-700/20 backdrop-blur-sm"
                        ></div>
                        <v-icon
                            name="bi-droplet"
                            scale="1.2"
                            class="relative z-10"
                        />
                        <span class="relative z-10">Report Issue Now</span>
                        <div
                            class="absolute inset-0 rounded-xl bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-sm"
                        ></div>
                    </Link>

                    <Link
                        :href="route('track-report')"
                        class="group relative inline-flex items-center justify-center gap-3 bg-transparent text-slate-200 border border-slate-600 hover:border-slate-400 px-8 py-4 rounded-xl font-semibold shadow-xl hover:shadow-slate-500/10 transition-all duration-300 hover:scale-105 overflow-hidden"
                    >
                        <div
                            class="absolute inset-0 bg-slate-800/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-sm rounded-xl"
                        ></div>
                        <v-icon
                            name="io-search"
                            scale="1.2"
                            class="relative z-10"
                        />
                        <span class="relative z-10">Track Report</span>
                    </Link>
                </div>
            </div>

            <!-- Right Image -->
            <div
                class="hero-image-container w-full lg:w-2/5 flex items-center justify-center order-1 lg:order-2 relative z-10"
            >
                <div
                    class="relative group"
                    @mouseenter="showImageTooltip = true"
                    @mouseleave="showImageTooltip = false"
                >
                    <!-- Reduced Glowing Border Effect -->
                    <div
                        class="absolute -inset-2 bg-gradient-to-r from-blue-400/20 to-cyan-400/20 rounded-2xl blur-md opacity-50 group-hover:opacity-70 transition-all duration-500"
                    ></div>

                    <!-- Image Container with Glass Effect -->
                    <div
                        class="relative bg-slate-800/40 backdrop-blur-md rounded-2xl shadow-2xl overflow-hidden transform group-hover:scale-[1.02] transition-all duration-500 border border-slate-700/50"
                    >
                        <div class="p-4">
                            <img
                                class="rounded-xl w-full h-auto object-cover shadow-inner"
                                src="/images/AquatrackIMG.jpg"
                                alt="Water District Office"
                            />

                            <!-- Subtle Tooltip -->
                            <div
                                v-if="showImageTooltip"
                                class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-900/90 to-transparent p-4 transition-all duration-300"
                            >
                                <h3
                                    class="text-sm font-semibold text-white text-center drop-shadow-lg"
                                >
                                    Clarin Water District Office
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
@keyframes blob {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    25% {
        transform: translate(20px, -50px) scale(1.1);
    }

    50% {
        transform: translate(-20px, 20px) scale(0.9);
    }

    75% {
        transform: translate(50px, 50px) scale(1.05);
    }
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-20px);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #475569 #1e293b;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #1e293b;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

@media (max-width: 1024px) {
    .hero-content {
        text-align: center;
    }

    h1 {
        font-size: 3.5rem !important;
    }
}

@media (max-width: 640px) {
    h1 {
        font-size: 2.5rem !important;
        line-height: 1.2;
    }

    .subheadline {
        font-size: 1rem !important;
    }

    .hero-badge {
        font-size: 0.75rem;
    }

    .cta-buttons button {
        padding: 0.875rem 1.5rem;
        font-size: 0.875rem;
    }

    .animate-float {
        display: none;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 2rem !important;
    }
}

html {
    scroll-behavior: smooth;
}
</style>
