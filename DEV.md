## Potential improvements

Both `Generator` and `Parser` have:
  - `public $lines = []`
  - The same constructor (+ promoted properties) — `$encoding` and `$lineSeparator`
  - `records()`

Therefore, it might make sense to create some common abstraction for them. I considered creating an abstract `LineContainer` (for classes that contain lines, like these two) but ended up reverting the change since it didn't seem appropriate. The only reason why `Parser` has the `$encoding` and `$lineSeparator` properties is that it can create a `Generator` instance. So they're not necessarily of the same type, it's more so that one can create the other and for that reason it has properties needed for the instantiation of the other.

Therefore, it'd be more clean to just abstract the line-related code like `records()`, but at that point it'd be just that method and maybe the `$lines` property.

`$lines` & `records()` fit something like a `LineContainer`, and the constructor fits something like a `GeneratesOutput`. Maybe creating `ContainsLines` & `GeneratesOutput` traits would be the most sensible solution to the code duplication, though I don't like putting constructors in traits.
