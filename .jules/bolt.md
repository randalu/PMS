## 2024-05-18 - Avoid N+1 queries when parent relation is already loaded
**Learning:** In Laravel, fetching child records from a parent (e.g. `$category->products()`) does not automatically load the parent relation back onto the child models. If views then call `$product->category`, it triggers N+1 queries.
**Action:** Use `$children->each->setRelation('parentName', $parent)` to inject the loaded parent into the child models.
