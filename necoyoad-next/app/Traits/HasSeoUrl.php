<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\UrlAlias;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * HasSeoUrl — provides per-language SEO URL slugs for any model.
 *
 * This is the url_alias table from the original Necoyoad, reimplemented
 * as an Eloquent morph relation. Each model that uses this trait gets
 * a `seoUrls()` relation scoped by language_id.
 *
 * Usage:
 *   $product->seoUrls()->where('language_id', $langId)->first()->keyword;
 *   $product->getSeoUrl($langId); // returns the slug
 *
 * @see v2 (SEO URL rewriter)
 * @see v5 (polymorphic object spine — url_alias table)
 */
trait HasSeoUrl
{
    public function seoUrls(): MorphMany
    {
        return $this->morphMany(UrlAlias::class, 'aliasable');
    }

    public function getSeoUrl(?int $languageId = null): ?string
    {
        $languageId ??= app('language.context')->id();

        $alias = $this->seoUrls()
            ->where('language_id', $languageId)
            ->first();

        return $alias?->keyword;
    }

    public function setSeoUrl(string $keyword, ?int $languageId = null): void
    {
        $languageId ??= app('language.context')->id();

        $this->seoUrls()->updateOrCreate(
            ['language_id' => $languageId],
            ['keyword' => $keyword]
        );
    }
}
