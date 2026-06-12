## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.

## 2026-06-12 - Prevent N+1 queries when accessing parent relationships
**Learning:** Eloquent does not automatically set inverse relationships when querying children from a parent (e.g. `$category->products()`). Accessing the parent from the retrieved children in a loop will trigger an N+1 query issue.
**Action:** Iterate through the retrieved children and use `$children->each->setRelation('parent', $parent)` to inject the already loaded parent object into the children.
