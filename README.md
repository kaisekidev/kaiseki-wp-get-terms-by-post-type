# kaiseki/wp-get-terms-by-post-type

Extend WordPress `get_terms()` to count and constrain terms by the post types attached to them.

A single `kaiseki/wp-hook` `HookProviderInterface` (`HookRegistry`) that filters `terms_clauses` so a
`get_terms()` query restricted with a `post_type` argument returns only terms attached to those post
types, each with a `count` of matching posts. By default only `publish`ed posts are counted; pass
`post_status` to override.

## Installation

```bash
composer require kaiseki/wp-get-terms-by-post-type
```

Requires PHP 8.2 or newer.

## Usage

Register `ConfigProvider` with your laminas-style config aggregator and activate the provider via
`kaiseki/wp-hook`:

```php
use Kaiseki\WordPress\GetTermsByPostType\HookRegistry;

return [
    'hook' => [
        'provider' => [
            HookRegistry::class,
        ],
    ],
];
```

With the provider active, pass `post_type` (and optionally `post_status`) to `get_terms()`:

```php
$terms = get_terms([
    'taxonomy'    => 'category',
    'post_type'   => ['post', 'page'],   // string or list of strings
    'post_status' => ['publish'],        // optional; defaults to ['publish']
    'hide_empty'  => true,
]);

// Each returned term now carries a `count` of posts in the given post types.
```

Queries that request `'fields' => 'count'` are passed through unchanged.

## Development

```bash
composer install
composer check   # check-deps, cs-check, phpstan
```

## License

MIT — see [LICENSE](LICENSE).
