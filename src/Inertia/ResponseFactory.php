<?php

namespace Flavorly\InertiaFlash\Inertia;
use Illuminate\Contracts\Support\Arrayable;
use Inertia\Response;
use \Inertia\ResponseFactory as BaseResponseFactory;

class ResponseFactory extends BaseResponseFactory
{
    /**
     * This is required to override the default behavior of Inertia\ResponseFactory
     * Because at certain point if the user shared a prop via flash on a controller after the request pipeline was already finished
     * We still need to inject this and merge with the existing props
     * When we do full page reloads Inertia Flash will work just fine,
     * but when partial reloads or Inertia Link is clicked a XHR request is made and the props are "lost" in a limbo.
     *
     * @param  string  $component
     * @param  array|Arrayable  $props
     * @return Response
     */
    public function render(string $component, $props = []): Response
    {
        if ($props instanceof Arrayable) {
            $props = $props->toArray();
        }

        return new Response(
            $component,
            array_merge(
                $this->sharedProps,
                $props,
                inertia_flash()->getShared()
            ),
            $this->rootView,
            $this->getVersion()
        );
    }
}
