## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.
## 2024-06-11 - N+1 optimization pattern

**Learning:** Injecting loaded parents into loaded children collections (`$children->each->setRelation('parent', $parent)`) effectively prevents implicit N+1 queries from views calling child-to-parent relationships in loops without causing DB hit.

**Action:** Look out for implicit N+1 queries inside blade templates mapping over loaded relations and use `$children->each->setRelation` when applicable.
