<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\GetTermsByPostType;

use Kaiseki\WordPress\Hook\HookProviderInterface;
use wpdb;

use function add_filter;
use function implode;
use function is_array;
use function is_string;
use function str_replace;

final class HookRegistry implements HookProviderInterface
{
    public function addHooks(): void
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
        if (isset($args['fields']) && $args['fields'] === 'count') {
            return $clauses;
        }

        $postTypes = $this->getEntries($args['post_type'] ?? null);
        if ($postTypes === []) {
            return $clauses;
        }

        global $wpdb;
        if (!$wpdb instanceof wpdb) {
            return $clauses;
        }

        $postStatus = $this->getEntries($args['post_status'] ?? null);
        if ($postStatus === []) {
            $postStatus = ["'publish'"];
        }

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
                if (is_string($entry)) {
                    $entries[] = "'" . $entry . "'";
                }
            }
        } elseif (is_string($arg)) {
            $entries[] = "'" . $arg . "'";
        }

        return $entries;
    }
}
