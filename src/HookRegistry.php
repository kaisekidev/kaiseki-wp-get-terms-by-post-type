<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\GetTermsByPostType;

use Kaiseki\WordPress\Hook\HookCallbackProviderInterface;

use function implode;
use function is_array;
use function is_string;
use function str_replace;

final class HookRegistry implements HookCallbackProviderInterface
{
    public function registerHookCallbacks(): void
    {
        add_filter('terms_clauses', [$this, 'filterTermsClauses'], 10, 3);
    }

    /**
     * @param array<string, string> $clauses
     * @param list<string>          $taxonomies
     * @param array<string, mixed>  $args
     *
     * @return array<string, string>
     */
    public function filterTermsClauses(array $clauses, array $taxonomies, array $args): array
    {
        if (
            /** @phpstan-ignore-next-line */
            empty($args['post_type'])
            || (
                isset($args['fields'])
                && $args['fields'] === 'count'
            )
        ) {
            return $clauses;
        }

        /** @phpstan-ignore-next-line */
        if (empty($args['post_status'])) {
            $args['post_status'] = ['publish'];
        }

        global $wpdb;
        $postTypes = $this->getEntries($args['post_type']);
        $postStatus = $this->getEntries($args['post_status']);

        $clauses['fields'] = 'DISTINCT ' . str_replace(
            'tt.*',
            'tt.term_taxonomy_id, tt.taxonomy, tt.description, tt.parent',
            $clauses['fields']
        ) . ', COUNT(p.post_type) AS count';
        // phpcs:disable Generic.Files.LineLength.TooLong
        $clauses['join'] .= ' LEFT JOIN ' . $wpdb->term_relationships . ' AS r ON r.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN ' . $wpdb->posts . ' AS p ON p.ID = r.object_id';
        $clauses['where'] .= ' AND ( p.post_status IN (' . implode(
            ',',
            $postStatus
        ) . ') AND (p.post_type IN (' . implode(',', $postTypes) . ') OR p.post_type IS NULL) )';
        $clauses['orderby'] = 'GROUP BY t.term_id ' . $clauses['orderby'];

        return $clauses;
    }

    /**
     * @param mixed $arg
     *
     * @return list<string>
     */
    private function getEntries(mixed $arg): array
    {
        $entries = [];
        if (is_array($arg)) {
            foreach ($arg as $entry) {
                $entries[] = "'" . $entry . "'";
            }
        } elseif (is_string($arg)) {
            $entries[] = "'" . $arg . "'";
        }
        return $entries;
    }
}
