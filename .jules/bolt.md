## 2024-06-11 - Global Setting Caching
**Learning:** Default fallbacks provided in standard query accessors (like `?? $default`) must be evaluated *outside* the cache closure. If evaluated inside, the cache will erroneously permanently store the default value if the record doesn't exist, preventing updates if a record is later created.
**Action:** Always evaluate `$value = Cache::rememberForever...` then return `$value ?? $default;`.
