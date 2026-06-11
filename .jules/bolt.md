## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.
## 2024-06-11 - Resolving Relationship Overlap N+1
**Learning:** Found a sneaky N+1 query pattern where children fetched via a parent relationship (e.g. `$category->products()`) were issuing an additional query to retrieve the exact same parent when a child requested it via its own relationship (`$product->category`).
**Action:** Used `$products->each->setRelation('category', $category);` to manually inject the already-loaded parent object back into each child's relation array, thereby safely preventing the redundant N+1 queries.
