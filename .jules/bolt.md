## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.

## 2026-05-30 - Prevent N+1 queries on parent relations using setRelation
**Learning:** When retrieving children via a parent model relation (e.g., `$category->products()`), looping over the children and accessing `$child->parent` inside a view triggers an N+1 query per child or requires an extra eager load query (`with('parent')`).
**Action:** Use `$children->each->setRelation('parentName', $parent)` immediately after fetching the children to inject the already-loaded parent object into each child model. This avoids database overhead completely.
