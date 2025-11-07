//About.vue
<template>
    <section
        id="about"
        class="relative py-16 sm:py-20 px-4 sm:px-6 lg:px-8 overflow-hidden"
    >
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-500/5 rounded-full blur-3xl"
            ></div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div
                class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16"
            >
                <!-- Left Column - Text Content -->
                <div class="about-content lg:w-1/2">
                    <!-- Section Badge -->
                    <div
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 backdrop-blur-sm border border-blue-400/30 rounded-full px-4 py-2 mb-6 shadow-xl"
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
                            >About Our Platform</span
                        >
                    </div>

                    <h2
                        class="text-3xl text-white sm:text-4xl md:text-5xl lg:text-5xl font-bold mb-6"
                    >
                        About
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400"
                            >AquaTrack</span
                        >
                    </h2>

                    <div class="space-y-5">
                        <p
                            class="text-base sm:text-lg text-slate-300 leading-relaxed"
                        >
                            AquaTrack is a comprehensive water management
                            platform designed specifically for residential
                            customers. Our mission is to make water consumption
                            tracking simple, transparent, and efficient.
                        </p>

                        <p
                            class="text-base sm:text-lg text-slate-300 leading-relaxed"
                        >
                            With features like monthly water usage monitoring,
                            monthly bill viewing, and instant issue reporting,
                            we're revolutionizing how communities interact with
                            their water services.
                        </p>

                        <div class="space-y-3 pt-2">
                            <div
                                class="group flex items-start p-3 rounded-xl border border-slate-700/50 bg-slate-800/50 backdrop-blur-sm shadow-lg hover:shadow-blue-500/20 hover:bg-slate-800/70 hover:border-blue-500/30 transition-all duration-300 hover:-translate-y-0.5"
                            >
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 flex items-center justify-center mr-3 group-hover:scale-105 transition-transform duration-300 shadow-md"
                                >
                                    <div
                                        class="h-3 w-3 rounded-full bg-cyan-400 shadow-sm"
                                    ></div>
                                </div>
                                <div>
                                    <h3
                                        class="text-base font-semibold text-white mb-1"
                                    >
                                        Water Usage Monitoring
                                    </h3>
                                    <p class="text-slate-400 text-sm">
                                        Track consumption with detailed
                                        analytics and real-time insights
                                    </p>
                                </div>
                            </div>

                            <div
                                class="group flex items-start p-3 rounded-xl border border-slate-700/50 bg-slate-800/50 backdrop-blur-sm shadow-lg hover:shadow-blue-500/20 hover:bg-slate-800/70 hover:border-blue-500/30 transition-all duration-300 hover:-translate-y-0.5"
                            >
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 flex items-center justify-center mr-3 group-hover:scale-105 transition-transform duration-300 shadow-md"
                                >
                                    <div
                                        class="h-3 w-3 rounded-full bg-cyan-400 shadow-sm"
                                    ></div>
                                </div>
                                <div>
                                    <h3
                                        class="text-base font-semibold text-white mb-1"
                                    >
                                        Instant Issue Reporting & Tracking
                                    </h3>
                                    <p class="text-slate-400 text-sm">
                                        Report and monitor water service issues
                                        in real-time
                                    </p>
                                </div>
                            </div>

                            <div
                                class="group flex items-start p-3 rounded-xl border border-slate-700/50 bg-slate-800/50 backdrop-blur-sm shadow-lg hover:shadow-blue-500/20 hover:bg-slate-800/70 hover:border-blue-500/30 transition-all duration-300 hover:-translate-y-0.5"
                            >
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 flex items-center justify-center mr-3 group-hover:scale-105 transition-transform duration-300 shadow-md"
                                >
                                    <div
                                        class="h-3 w-3 rounded-full bg-cyan-400 shadow-sm"
                                    ></div>
                                </div>
                                <div>
                                    <h3
                                        class="text-base font-semibold text-white mb-1"
                                    >
                                        Community Announcements
                                    </h3>
                                    <p class="text-slate-400 text-sm">
                                        Stay updated with important community
                                        news and schedules
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Enhanced Image -->
                <div class="about-image lg:w-1/2 hidden lg:block">
                    <div class="relative group">


                        <!-- Main Image Container -->
                        <div
                            class="relative overflow-hidden p-6 transform group-hover:scale-[1.02] transition-all duration-500"
                        >
                            <div class="relative z-10">
                                <img
                                    src="/images/MainLogo.png"
                                    alt="AquaTrack Logo"
                                    class="w-full h-auto object-contain filter drop-shadow-lg"
                                />
                            </div>




                        </div>


                    </div>


                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
// Component logic here if needed
</script>

<style scoped>
/* Custom animations */
@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-6px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Smooth transitions */
* {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
