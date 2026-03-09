self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open('erah-platform-v1').then(function (cache) {
      return cache.addAll([
        '/',
        '/manifest.json',
        '/template/assets/img/logo.png'
      ]);
    })
  );
});

self.addEventListener('fetch', function (event) {
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(event.request).then(function (cached) {
      return cached || fetch(event.request).then(function (response) {
        var copy = response.clone();
        caches.open('erah-platform-v1').then(function (cache) {
          cache.put(event.request, copy);
        });

        return response;
      }).catch(function () {
        return cached;
      });
    })
  );
});
