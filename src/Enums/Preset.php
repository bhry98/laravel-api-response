<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Enums;

/**
 * Built-in preset names. Custom presets registered via PresetManager are
 * referenced by plain string and are not required to appear here.
 */
enum Preset: string
{
    case Default = 'default';
    case Minimal = 'minimal';
    case Mobile = 'mobile';
    case JsonApi = 'jsonapi';
}
