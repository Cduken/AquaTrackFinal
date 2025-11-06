<template>
    <section
        id="features"
        class="relative py-12 sm:py-[150px] px-4 sm:px-6 lg:px-[100px]"
    >
        <div class="max-w-[1500px] mx-auto relative z-10">
            <div class="text-center mb-8 sm:mb-12">
                <!-- Badge -->


                <h2
                    class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-200 via-cyan-200 to-blue-300 mb-3 sm:mb-4"
                >
                    Why Choose AquaTrack?
                </h2>
                <p
                    class="text-base sm:text-md lg:text-lg text-slate-300 max-w-3xl mx-auto"
                >
                    Everything you need to manage your water consumption
                    efficiently in one powerful platform
                </p>
            </div>

            <div
                class="grid gap-6 sm:gap-8 md:gap-[60px] sm:grid-cols-2 lg:grid-cols-4"
            >
                <!-- Usage Monitoring -->
                <div
                    class="feature-item group flex flex-col items-center rounded-2xl border border-slate-700/50 bg-slate-800/40 backdrop-blur-md p-4 sm:p-6 md:p-8 shadow-2xl transition-all duration-500 hover:shadow-blue-500/20 hover:-translate-y-2 hover:border-blue-500/30 relative"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300"
                    >
                        <Gauge class="h-6 w-6 sm:h-7 sm:w-7 text-cyan-400" />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 to-blue-300 mb-2 text-center"
                    >
                        Usage Monitoring
                    </h3>
                    <p
                        class="text-sm sm:text-base text-slate-300 mt-2 sm:mt-4 text-center leading-relaxed"
                    >
                        Track your water usage with detailed consumption
                        analytics and real-time trends.
                    </p>
                    <!-- Hover Glow Effect -->
                    <div
                        class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                    ></div>
                </div>

                <!-- Easy Issue Reporting -->
                <div
                    class="feature-item group flex flex-col items-center rounded-2xl border border-slate-700/50 bg-slate-800/40 backdrop-blur-md p-4 sm:p-6 md:p-8 shadow-2xl transition-all duration-500 hover:shadow-blue-500/20 hover:-translate-y-2 hover:border-blue-500/30 relative"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300"
                    >
                        <Flag class="h-6 w-6 sm:h-7 sm:w-7 text-cyan-400" />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 to-blue-300 mb-2 text-center"
                    >
                        Easy Issue Reporting
                    </h3>
                    <p
                        class="text-sm sm:text-base text-slate-300 mt-2 sm:mt-4 text-center leading-relaxed"
                    >
                        Report water issues quickly and track resolution
                        progress through our platform.
                    </p>
                    <!-- Hover Glow Effect -->
                    <div
                        class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                    ></div>
                </div>

                <!-- Community Updates -->
                <div
                    class="feature-item group flex flex-col items-center rounded-2xl border border-slate-700/50 bg-slate-800/40 backdrop-blur-md p-4 sm:p-6 md:p-8 shadow-2xl transition-all duration-500 hover:shadow-blue-500/20 hover:-translate-y-2 hover:border-blue-500/30 relative"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300"
                    >
                        <TrendingUp
                            class="h-6 w-6 sm:h-7 sm:w-7 text-cyan-400"
                        />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 to-blue-300 mb-2 text-center"
                    >
                        Community Updates
                    </h3>
                    <p
                        class="text-sm sm:text-base text-slate-300 mt-2 sm:mt-4 text-center leading-relaxed"
                    >
                        Stay informed on water schedules, maintenance, meetings,
                        and important community news.
                    </p>
                    <!-- Hover Glow Effect -->
                    <div
                        class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                    ></div>
                </div>

                <!-- Secure & Reliable -->
                <div
                    class="feature-item group flex flex-col items-center rounded-2xl border border-slate-700/50 bg-slate-800/40 backdrop-blur-md p-4 sm:p-6 md:p-8 shadow-2xl transition-all duration-500 hover:shadow-blue-500/20 hover:-translate-y-2 hover:border-blue-500/30 relative"
                >
                    <div
                        class="flex h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300"
                    >
                        <ShieldCheck
                            class="h-6 w-6 sm:h-7 sm:w-7 text-cyan-400"
                        />
                    </div>
                    <h3
                        class="text-lg sm:text-xl md:text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 to-blue-300 mb-2 text-center"
                    >
                        Secure & Reliable
                    </h3>
                    <p
                        class="text-sm sm:text-base text-slate-300 mt-2 sm:mt-4 text-center leading-relaxed"
                    >
                        Your data is protected with industry-standard security
                        measures and encryption.
                    </p>
                    <!-- Hover Glow Effect -->
                    <div
                        class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                    ></div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Gauge, Flag, TrendingUp, ShieldCheck } from "lucide-vue-next";
</script>

<style scoped>
/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #1e293b;
}

::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>
