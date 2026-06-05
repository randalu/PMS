## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.

## 2026-05-16 - Prevent N+1 queries when accessing parent from children retrieved via relationship
**Learning:** When retrieving children via a relationship (`$parent->children()->get()`), iterating through the children and accessing the parent model (`$child->parent->name`) causes an N+1 query issue, because the parent relation is not loaded on the children. Eager loading with `with('parent')` works but issues an unnecessary query since we already have the parent.
**Action:** Inject the already loaded parent into the children models using `$children->each->setRelation('parentName', $parent)` immediately after the `get()` call to prevent the extra queries without additional database overhead.
