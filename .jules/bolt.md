## 2024-06-15 - [StorefrontController N+1 Categories]
**Learning:** The StorefrontController `index` method was executing an extra query to load `category` models for each product via eager loading, even though the exact same categories were already fetched globally for the page header.
**Action:** Use `$model->setRelation()` effectively to inject already retrieved instances into relationships to avoid duplicating queries, especially when iterating over items.

## 2024-06-15 - [O(N*M) lookups inside each()]
**Learning:** When injecting relations in an `each` loop, doing a `$collection->firstWhere()` on every iteration is an $O(N \times M)$ operation.
**Action:** Key the collection outside the loop (`$keyed = $collection->keyBy('id')`) and do an $O(1)$ `$keyed->get()` lookup inside the loop.
