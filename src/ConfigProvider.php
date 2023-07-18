<?php

declare(strict_types=1);

namespace Kaiseki\WordPress\GetTermsByPostType;

final class ConfigProvider
{
    /**
     * @return array<mixed>
     */
    public function __invoke(): array
    {
        return [
            'hook' => [
                'provider' => [
                    HookRegistry::class,
                ],
            ],
        ];
    }
}
