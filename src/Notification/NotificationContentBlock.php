<?php

namespace Flavorly\InertiaFlash\Notification;

use Flavorly\InertiaFlash\Notification\Enums\ContentBlockTypeEnum;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

class NotificationContentBlock extends Data
{
    use Concerns\HasIcon;
    use Concerns\HasPosition;
    use Concerns\HasProps;

    protected string $id;

    public function __construct(
        protected ContentBlockTypeEnum $type = ContentBlockTypeEnum::Tag,
    ) {
        $this->id = Str::uuid();
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function tag(string $content, string $tag = 'div'): static
    {
        $this->type = ContentBlockTypeEnum::Tag;
        $this->props([
            'text' => $content,
            'tag' => $tag,
        ]);

        return $this;
    }

    public function text(string $text, string $tag = 'p'): static
    {
        $this->type = ContentBlockTypeEnum::Text;
        $this->props([
            'text' => $text,
            'tag' => $tag,
        ]);

        return $this;
    }

    public function html(string $html, bool $safe = false): static
    {
        $this->type = $safe ? ContentBlockTypeEnum::Html : ContentBlockTypeEnum::UnsafeHtml;
        $this->props([
            'html' => $html,
        ]);

        return $this;
    }

    public function image(string $url, string $alt = 'image'): static
    {
        $this->type = ContentBlockTypeEnum::Image;
        $this->props([
            'src' => $url,
            'alt' => $alt,
            'loading' => 'lazy',
        ]);

        return $this;
    }

    public function title(string $title, string $tag = 'h3'): static
    {
        $this->type = ContentBlockTypeEnum::Title;
        $this->props([
            'text' => $title,
            'tag' => $tag,
        ]);

        return $this;
    }
}
