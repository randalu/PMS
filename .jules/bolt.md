## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.

## 2026-06-01 - Avoid N+1 queries when accessing parent relationships from children
**Learning:** When retrieving a collection of child models via a parent relationship (e.g., `$category->products()->get()`), accessing the parent from the child (e.g., `$product->category->name` in a Blade view) triggers an N+1 query. This happens because Eloquent doesn't automatically set the inverse relationship on the children during lazy loading.
**Action:** Use `$children->each->setRelation('parentRelationName', $parent);` to inject the already-loaded parent model into the children. This eliminates the need for eager loading (`->with('parent')`) and prevents N+1 queries entirely.
