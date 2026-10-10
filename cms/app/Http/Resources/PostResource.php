<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'excerpt'        => $this->excerpt,
            'body'           => $this->when($request->routeIs('*.show'), $this->body),
            'type'           => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
                'icon'  => $this->type->icon(),
            ],
            'status'         => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'featured_image' => $this->featured_image,
            'is_breaking'    => $this->is_breaking,
            'is_featured'    => $this->is_featured,
            'is_exclusive'   => $this->is_exclusive,
            'is_corrected'   => $this->is_corrected,
            'reading_time'   => $this->reading_time_minutes,
            'view_count'     => $this->view_count,

            // Author
            'author'         => $this->whenLoaded('author', fn () => [
                'id'     => $this->author->id,
                'name'   => $this->author->name,
                'avatar' => $this->author->avatar,
            ]),

            // Category
            'category'       => $this->whenLoaded('category', fn () => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),

            // Tags
            'tags'           => $this->whenLoaded('tags', fn () =>
                $this->tags->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug])
            ),

            // SEO (only on detail view)
            'seo'            => $this->when($request->routeIs('*.show'), [
                'title'       => $this->seo_title ?? $this->title,
                'description' => $this->seo_description ?? $this->excerpt,
                'og_image'    => $this->seo_og_image ?? $this->featured_image_url,
                'canonical'   => $this->canonical_url,
                'schema'      => $this->schema_markup,
            ]),

            // Timestamps
            'published_at'   => $this->published_at?->toISOString(),
            'updated_at'     => $this->updated_at->toISOString(),

            // URL
            'url'            => $this->url,
        ];
    }
}
