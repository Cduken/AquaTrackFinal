import "../css/app.css";
import "./bootstrap";
import Swal from "sweetalert2";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { OhVueIcon } from "oh-vue-icons";
import "leaflet/dist/leaflet.css";
import L from "leaflet";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

// Import icons from separate file
import "./icons";

// Make Leaflet globally available
window.L = L;

// Fix for default Leaflet markers (optional but recommended)
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png",
    iconUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png",
    shadowUrl:
        "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png",
});

// ============================================================================
// SERVICE WORKER SETUP - PUT THIS BEFORE createInertiaApp
// ============================================================================

// Chrome-specific service worker handling
const isChrome =
    /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);

// Development mode cleanup
if (import.meta.env.DEV) {
    console.log("Development mode - cleaning up service workers");

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.getRegistrations().then((registrations) => {
            registrations.forEach((registration) => {
                console.log("Unregistering SW:", registration);
                registration.unregister();
            });
        });
    }

    // Force clear caches
    if ("caches" in window) {
        caches.keys().then((cacheNames) => {
            cacheNames.forEach((cacheName) => {
                caches.delete(cacheName);
            });
        });
    }
}

// Chrome-specific additional cleanup
if (isChrome && "serviceWorker" in navigator) {
    navigator.serviceWorker.getRegistrations().then((registrations) => {
        registrations.forEach((registration) => {
            // Unregister service workers with errors or redundant state
            if (registration.active?.state === "redundant") {
                console.log("Unregistering redundant SW:", registration);
                registration.unregister();
            }
        });
    });
}

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, {
                timeout: 5000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: "button",
                icon: true,
                rtl: false,
            })
            .component("v-icon", OhVueIcon);

        app.config.globalProperties.$swal = Swal;
        return app.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});

// ============================================================================
// SERVICE WORKER REGISTRATION - ONLY IN PRODUCTION
// ============================================================================
// In app.js - Updated service worker registration
if ("serviceWorker" in navigator) {
    // Only register in production
    if (import.meta.env.PROD) {
        window.addEventListener("load", () => {
            // Clear any problematic registrations first
            navigator.serviceWorker.getRegistrations().then((registrations) => {
                registrations.forEach((registration) =>
                    registration.unregister()
                );

                // Then register fresh
                setTimeout(() => {
                    navigator.serviceWorker
                        .register("/sw.js", { scope: "/" })
                        .then((registration) => {
                            console.log(
                                "SW registered successfully:",
                                registration
                            );

                            // Check for updates
                            registration.addEventListener("updatefound", () => {
                                const newWorker = registration.installing;
                                console.log(
                                    "New service worker found:",
                                    newWorker
                                );
                            });
                        })
                        .catch((error) => {
                            console.error("SW registration failed:", error);
                        });
                }, 100);
            });
        });
    } else {
        // Development: unregister all service workers
        navigator.serviceWorker.getRegistrations().then((registrations) => {
            registrations.forEach((registration) => registration.unregister());
        });
    }
}
