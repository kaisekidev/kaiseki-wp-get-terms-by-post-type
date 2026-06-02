# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.0 - 2026-06-02

First tagged release.

### Added

- `HookRegistry` hook provider (and `ConfigProvider`) that filters `terms_clauses` so `get_terms()`
  can be constrained by `post_type` and counts matching posts per term, defaulting `post_status` to
  `['publish']`.

### Changed

- PHP requirement is `^8.2` (PHP 8.4 is the primary target).
- Modernized the dev toolchain (PHPStan 2, PHPUnit 11 schema, composer-require-checker 4); now depends
  on `kaiseki/php-coding-standard: ^1.0` with the shared PHPStan config; `kaiseki/config` and
  `kaiseki/wp-hook` pinned to `^2.0`. CI now runs via the reusable workflow in `kaisekidev/.github`.

### Fixed

- PHPStan 2 (level max): removed two `@phpstan-ignore-next-line` suppressions. `filterTermsClauses`
  now narrows the `$wpdb` global with an `instanceof` guard and skips when the query has no usable
  `post_type`, and `getEntries` filters its inputs to strings before quoting them. No behaviour change
  for valid term queries.
