## 2026-05-23 - Cached App Settings
**Learning:** Eloquent 'booted' method event hooks (, ) are effective for granular cache invalidation, ensuring  caching logic doesn't go stale.
**Action:** Utilize these hooks directly in model boot cycles instead of clearing whole cache stores.
## 2024-05-23 - Cached App Settings
**Learning:** Eloquent 'booted' method event hooks (`saved`, `deleted`) are effective for granular cache invalidation, ensuring `rememberForever` caching logic doesn't go stale.
**Action:** Utilize these hooks directly in model boot cycles instead of clearing whole cache stores.
