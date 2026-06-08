## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.
## 2026-05-16 - Avoid N+1 on Inverse Relations

**Learning:** When retrieving child models using a `HasMany` relation with extra query builder scopes (e.g., `$category->products()->with('...')->get()`), the parent model is NOT automatically injected into the inverse relation (`$product->category`) of the results. This results in N+1 queries when accessing the parent model properties from a loop on the child objects.

**Action:** Whenever iterating over child objects to access their parent, use `$children->each->setRelation('parentName', $parent);` to inject the already-loaded parent directly without querying again.
