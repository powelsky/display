'use strict';

/*
 * Static-asset service worker.
 *
 * Deliberately narrow: only same-origin, idempotent static files listed in
 * ASSETS are served cache-first. Documents and PHP endpoints are never
 * intercepted or cached, because they carry per-request state that must not be
 * replayed:
 *   - index.php embeds a CSRF token bound to the current session and a CSP
 *     nonce that must match the response header.
 *   - api.php returns live rates, history and mutation results.
 * Returning early (no respondWith) keeps the browser's normal network path.
 */

const CACHE_NAME = 'mandiri-fx-static-v1';

const ASSETS = [
  './manifest.json',
  './favicon.ico',
  './icons/icon-192.png',
  './icons/icon-512.png',
  './icons/icon-512-maskable.png',
  './apple-touch-icon.png',
];

const ASSET_PATHS = new Set(
  ASSETS.map((asset) => new URL(asset, self.location.href).pathname),
);

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches
      .open(CACHE_NAME)
      .then((cache) => cache.addAll(ASSETS))
      .then(() => self.skipWaiting()),
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))),
      )
      .then(() => self.clients.claim()),
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;

  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);

  if (url.origin !== self.location.origin) {
    return;
  }

  if (!ASSET_PATHS.has(url.pathname)) {
    return;
  }

  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) {
        return cached;
      }

      return fetch(request).then((response) => {
        if (response.ok) {
          const copy = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
        }

        return response;
      });
    }),
  );
});
