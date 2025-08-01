<?php

namespace Statamic\GraphQL\Types;

use Rebing\GraphQL\Support\InterfaceType;
use Statamic\Contracts\Structures\Nav;
use Statamic\Facades\GraphQL;
use Statamic\Facades\Nav as NavAPI;
use Statamic\Support\Str;

class NavPageInterface extends InterfaceType
{
    protected $nav;

    public function __construct(Nav $nav)
    {
        $this->nav = $nav;
        $this->attributes['name'] = static::buildName($nav);
    }

    public function fields(): array
    {
        if ($fields = $this->nav->blueprint()->fields()->toGql()->all()) {
            return $fields;
        }

        return collect([
            '_' => [
                'type' => GraphQL::string(),
            ],
        ])->all();
    }

    public static function buildName(Nav $nav): string
    {
        return 'NavPage_'.Str::studly($nav->handle());
    }

    public static function addTypes()
    {
        GraphQL::addTypes(NavAPI::all()->each(function ($nav) {
            optional($nav->blueprint())->addGqlTypes();
        })->mapInto(NavBasicPageType::class)->all());
    }
}
