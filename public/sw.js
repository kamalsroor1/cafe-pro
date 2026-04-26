const STATIC = [
  '/',
  '/pos',
  '/manifest.json',
  '/icons/web-app-manifest-192x192.png',
  '/icons/web-app-manifest-512x512.png',
  '/icons/favicon.ico',
];

const getVersion = async () => {
  try {
    const res = await fetch('/build-version.json?t=' + Date.now());
    const data = await res.json();
    return 'cafe-pro-' + data.version;
  } catch {
    return 'cafe-pro-v1';
  }
};

// تثبيت: جيب الـ version الأول ثم cache
self.addEventListener('install', e => {
  e.waitUntil(
    getVersion().then(CACHE =>
      caches.open(CACHE).then(c => c.addAll(STATIC))
    )
  );
  self.skipWaiting();
});

// تنشيط: احذف كل الـ cache القديم
self.addEventListener('activate', e => {
  e.waitUntil(
    getVersion().then(CACHE =>
      caches.keys().then(keys =>
        Promise.all(
          keys.filter(k => k !== CACHE).map(k => caches.delete(k))
        )
      )
    )
  );
  self.clients.claim();
});

// Fetch
self.addEventListener('fetch', e => {
  const url = new URL(e.request.url);

  // Livewire — دايما من الشبكة
  if (url.pathname.startsWith('/livewire')) return;

  // build-version.json — دايما من الشبكة عشان يكتشف التحديث
  if (url.pathname === '/build-version.json') return;

  // Assets — Cache First
  if (
    e.request.destination === 'style' ||
    e.request.destination === 'script' ||
    e.request.destination === 'image'
  ) {
    e.respondWith(
      getVersion().then(CACHE =>
        caches.match(e.request).then(cached =>
          cached || fetch(e.request).then(res => {
            const clone = res.clone();
            caches.open(CACHE).then(c => c.put(e.request, clone));
            return res;
          })
        )
      )
    );
    return;
  }

  // HTML — Network First
  e.respondWith(
    getVersion().then(CACHE =>
      fetch(e.request)
        .then(res => {
          const clone = res.clone();
          caches.open(CACHE).then(c => c.put(e.request, clone));
          return res;
        })
        .catch(() => caches.match(e.request))
    )
  );
});