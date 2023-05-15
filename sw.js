const staticDevHCAl = "hcal1"
const assets = [
    "/",
    "/index.html",
    "/website_style.css",
]

self.addEventListener("install", installEvent => {
    installEvent.waitUntil(
        caches.open(staticDevHCAl).then(cache => {
            cache.addAll(assets)
        })
    )
})


self.addEventListener("fetch", fetchEvent => {
    fetchEvent.respondWith(
        caches.match(fetchEvent.request).then(res => {
            return res || fetch(fetchEvent.request)
        })
    )
})