## 2025-05-25 - Caching DB Query Results with Default Fallbacks
**Learning:** When caching database query results that have a default fallback (e.g., settings or configuration records), caching the default value when the record does not exist in the database can lead to stale cache issues if the record is later added, or can result in caching unexpected defaults.
**Action:** Always evaluate the fallback outside the caching closure (`return $value ?? $default`) to prevent caching the default value when a database record does not exist.
