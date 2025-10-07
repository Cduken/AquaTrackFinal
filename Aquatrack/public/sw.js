const CACHE_NAME = "aquatrack-v5";
const urlsToCache = [
    "/",
    "/css/app.css",
    "/js/app.js",
    "/manifest.json",
    // Remove "/offline" if you don't have this route
];

self.addEventListener("install", (event) => {
    console.log("Service Worker installing...");
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                console.log("Cache opened");
                // Only cache essential internal resources
                return cache.addAll(
                    urlsToCache.filter((url) => !url.startsWith("http"))
                );
            })
            .catch((error) => {
                console.log("Cache addAll error:", error);
            })
    );
    self.skipWaiting();
});

self.addEventListener("activate", (event) => {
    console.log("Service Worker activating...");
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME) {
                            console.log("Deleting old cache:", cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => self.clients.claim())
    );
});

self.addEventListener("fetch", (event) => {
    // Skip non-GET requests and browser extensions
    if (
        event.request.method !== "GET" ||
        event.request.url.startsWith("chrome-extension://") ||
        event.request.url.startsWith("chrome://")
    ) {
        return;
    }

    // Skip Vite HMR in development
    if (
        event.request.url.includes("/@vite/") ||
        event.request.url.includes("?v=") ||
        event.request.url.includes(":5173")
    ) {
        // Vite dev server
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            // Return cached version if available
            if (cachedResponse) {
                return cachedResponse;
            }

            // Otherwise, fetch from network
            return fetch(event.request)
                .then((networkResponse) => {
                    // Check if valid response to cache
                    if (
                        networkResponse &&
                        networkResponse.status === 200 &&
                        networkResponse.type === "basic" &&
                        !event.request.url.includes("/api/") &&
                        !event.request.url.includes("chrome-extension") &&
                        event.request.url.startsWith("http") &&
                        !event.request.url.includes("://localhost")
                    ) {
                        // Clone the response for caching
                        const responseToCache = networkResponse.clone();

                        caches
                            .open(CACHE_NAME)
                            .then((cache) => {
                                cache.put(event.request, responseToCache);
                            })
                            .catch((cacheError) => {
                                console.log("Cache put error:", cacheError);
                            });
                    }

                    return networkResponse;
                })
                .catch((fetchError) => {
                    console.log("Fetch failed for:", event.request.url);

                    // If navigation request fails, return homepage
                    if (event.request.mode === "navigate") {
                        return caches
                            .match("/")
                            .then(
                                (homepage) =>
                                    homepage ||
                                    new Response(
                                        "Offline - Please check your connection"
                                    )
                            );
                    }

                    // For API requests, return proper error response
                    if (event.request.url.includes("/api/")) {
                        return new Response(
                            JSON.stringify({
                                error: "Network unavailable",
                                offline: true,
                            }),
                            {
                                status: 408,
                                headers: { "Content-Type": "application/json" },
                            }
                        );
                    }

                    // Let the error propagate for other requests
                    throw fetchError;
                });
        })
    );
});

// Simple message handler for skip waiting
self.addEventListener("message", (event) => {
    if (event.data && event.data.type === "SKIP_WAITING") {
        self.skipWaiting();
    }
});

// Background sync for offline reports (simplified)
self.addEventListener("sync", (event) => {
    if (event.tag === "sync-offline-reports") {
        console.log("Background sync triggered");
        event.waitUntil(syncOfflineReports());
    }
});

// Simplified sync function without external dependencies
async function syncOfflineReports() {
    try {
        console.log("Starting offline reports sync...");

        // Get all clients to send message
        const clients = await self.clients.matchAll();
        clients.forEach((client) => {
            client.postMessage({
                type: "TRIGGER_SYNC_FROM_SW",
            });
        });
    } catch (error) {
        console.error("Sync error:", error);
    }
}
