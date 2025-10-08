# Share Counts (Completed)

Server-side share count manager, DB schema, cache layer, network adapters and aggregator adapter were implemented. See `src/ShareCounts/ShareCountManager.php` and `src/ShareCounts/Adapters/` for details.

Recent updates:

- Admin settings added to configure an external aggregator endpoint and optional API key.
- AggregatorAdapter refactored to make HTTP request logic overridable for unit testing.
- Unit tests added to validate aggregator parsing behavior (`tests/Unit/ShareCounts/AggregatorAdapterTest.php`).
