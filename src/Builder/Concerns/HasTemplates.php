<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Builder\Concerns;

use Bhry98\LaravelApiResponse\Exceptions\TemplateNotFoundException;

/**
 * Reusable response definitions declared in
 * config('bhry98-api-response.responses'), e.g.:
 *
 *   'user_created' => ['status' => true, 'message' => '...', 'code' => 201],
 *
 * Usage: api()->template('user_created');
 */
trait HasTemplates
{
    /**
     * @param  array<string, mixed>  $overrides
     */
    public function template(string $name, array $overrides = []): static
    {
        $definitions = $this->config->get('bhry98-api-response.responses', []);

        if (! isset($definitions[$name])) {
            throw TemplateNotFoundException::make($name);
        }

        $definition = [...$definitions[$name], ...$overrides];

        $this->success((bool) ($definition['status'] ?? true));
        $this->statusCode((int) ($definition['code'] ?? 200));

        if (array_key_exists('message', $definition)) {
            $this->message((string) $definition['message']);
        }

        if (array_key_exists('data', $definition)) {
            $this->data($definition['data']);
        }

        if (array_key_exists('meta', $definition)) {
            $this->meta((array) $definition['meta']);
        }

        if (array_key_exists('errors', $definition)) {
            $this->errors((array) $definition['errors']);
        }

        return $this;
    }
}
