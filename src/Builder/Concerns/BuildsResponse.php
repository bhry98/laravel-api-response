<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Builder\Concerns;

use Bhry98\LaravelApiResponse\Events\ResponseSent;
use Illuminate\Http\JsonResponse;

/**
 * Terminal methods: run the pipeline, format the payload, and produce (or
 * describe) the final JsonResponse.
 */
trait BuildsResponse
{
    /**
     * Run the assembly pipeline and format the payload, without sending.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $resolved = $this->pipeline->through($this->payload);

        return $this->presets->formatterFor($resolved->preset ?? 'default')->format($resolved);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->build();
    }

    /**
     * Allows `return api()->created($user);` directly from a controller -
     * Laravel calls this automatically for any Responsable return value.
     */
    public function toResponse($request): JsonResponse
    {
        return $this->send();
    }

    public function send(): JsonResponse
    {
        $noBody = in_array($this->payload->statusCode, [204, 304], true);

        $response = $noBody
            ? $this->responseFactory->json(null, $this->payload->statusCode)
            : $this->responseFactory->json($this->build(), $this->payload->statusCode);

        if ($noBody) {
            // HTTP forbids a body on 204/304; JsonResponse::json(null, ...)
            // still encodes the literal string "null" otherwise.
            $response->setContent('');
        }

        foreach ($this->payload->headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        foreach ($this->payload->cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        $this->events->dispatch(new ResponseSent($response));

        return $response;
    }
}
