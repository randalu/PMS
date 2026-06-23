## 2026-05-16 - Cache queries with defaults outside the cache closure
**Learning:** When using `Cache::rememberForever` for database queries that return null but use a fallback default, evaluate the default *outside* the cache closure. If evaluated inside, the default value is cached, meaning it becomes stale if the underlying code updates the default value for the application, and it misrepresents the actual state in the database.
**Action:** Always fetch the raw database value inside the cache closure. Apply the `?? $default` fallback on the return value of `Cache::rememberForever`.

## 2026-05-16 - Avoid N+1 on HasMany inverse relation without eager load
**Learning:** In standard Laravel conventions, accessing a parent relation (`$product->category`) in a loop of children fetched via a `hasMany` query (`$category->products()`) causes an N+1 issue because the child doesn't inherently know its parent without a specific `with('category')` or manually setting it. Eager loading `with('category')` works but issues a redundant query (e.g. `select * from categories where id in (1)`).
**Action:** Instead of `->with('category')`, when querying children from a loaded parent model instance, iterate through the collection and manually inject the parent model using `$children->each->setRelation('parentRelationName', $parentModel);`. This achieves 0 additional queries compared to 1 (eager load) or N (lazy load).

## 2024-05-24 - Prevent N+1 queries during bulk operations
**Learning:** During bulk operations (like order confirmation where `lockForUpdate` is used on multiple child models), retrieving models inside a loop causes N+1 `SELECT ... FOR UPDATE` queries. Additionally, after calling `decrement()` on a model, calling `refresh()` is redundant in modern Laravel because `decrement()` implicitly updates the attributes in memory.
**Action:** When locking multiple models for update during a bulk operation, pluck their IDs and run a single batched `whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id')` query before the loop, then look up the model inside the loop using `$collection->get($id)` for O(1) access. Avoid unnecessary `refresh()` calls after `decrement()`.
