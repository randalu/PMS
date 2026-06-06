## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.

## 2026-05-17 - Injecting Parent Relationships
**Learning:** When retrieving a collection of child models via a parent relationship (e.g. `$category->products`), passing those children to a view that accesses `$product->category` will trigger an N+1 query. Eager loading with `->with('category')` inside the relationship query fetches the same parent record again unnecessarily.
**Action:** When a parent model is already loaded and you query its children, explicitly inject the parent into the child relationships using `$children->each->setRelation('parentRelationName', $parent)` before passing them to the view.
