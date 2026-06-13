## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.
## 2024-06-13 - Inverse Relationship N+1

**Learning:** When fetching child models through a parent relationship (`$category->products()`), the parent model (`$category`) isn't automatically attached to the child's inverse relationship. This causes unexpected N+1 queries if a view loops through the children and accesses `$product->category`.
**Action:** Use `$products->each->setRelation('category', $category);` after fetching to manually inject the parent model into all children.
