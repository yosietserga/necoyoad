<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Description;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * HasDescriptions — provides per-language localised content for any model.
 *
 * This is the polymorphic description table from the original Necoyoad,
 * reimplemented as an Eloquent morph relation. Each model that uses this
 * trait gets a `descriptions()` relation scoped by language_id.
 *
 * Usage:
 *   $product->descriptions()->where('language_id', $langId)->first()->title;
 *   $post->getDescription($langId)->body;
 *
 * @see v5 (multi-store/multi-language architecture)
 * @see v8 (CMS posts/pages and widget composition)
 */
trait HasDescriptions
{
    public function descriptions(): MorphMany
    {
        return $this->morphMany(Description::class, 'describable');
    }

    public function getDescription(?int $languageId = null): ?Description
    {
        $languageId ??= app('language.context')->id();

        return $this->descriptions()
            ->where('language_id', $languageId)
            ->first();
    }

    public function getDescriptionField(string $field, ?int $languageId = null): ?string
    {
        $description = $this->getDescription($languageId);

        return $description?->{$field};
    }

    public function getTitle(?int $languageId = null): ?string
    {
        return $this->getDescriptionField('title', $languageId);
    }

    public function getBody(?int $languageId = null): ?string
    {
        return $this->getDescriptionField('description', $languageId);
    }

    public function getSeoTitle(?int $languageId = null): ?string
    {
        return $this->getDescriptionField('seo_title', $languageId);
    }

    public function getMetaDescription(?int $languageId = null): ?string
    {
        return $this->getDescriptionField('meta_description', $languageId);
    }

    public function getMetaKeywords(?int $languageId = null): ?string
    {
        return $this->getDescriptionField('meta_keywords', $languageId);
    }
}
