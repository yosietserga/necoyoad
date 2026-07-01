<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Post;
use App\Models\Category;
use App\Services\TemplateResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * StorefrontController — handles all storefront page rendering.
 *
 * Every page follows the same pattern (from v8):
 *   1. Set session object_type/object_id (for per-entity widget overrides)
 *   2. Set landing_page (for widget filtering)
 *   3. Resolve the template (per-entity override → config → default)
 *   4. Render the storefront layout (which triggers WidgetComposer)
 *
 * The WidgetComposer runs automatically before the view renders,
 * populating $widgets[$position] with the widget tree.
 *
 * @see v8 (CMS widget composition — the common page pattern)
 */
class StorefrontController extends Controller
{
    public function __construct(
        private readonly TemplateResolver $templateResolver
    ) {}

    public function home(): Response
    {
        session()->forget(['object_type', 'object_id']);
        session(['landing_page' => 'common.home']);

        $template = $this->templateResolver->resolve(
            entityTemplate: null,
            type: 'home',
            fallback: 'content.home',
        );

        return response()->view($template, [
            'title' => config('app.name'),
            'templateType' => 'home',
        ]);
    }

    public function product(Product $product): Response
    {
        session(['object_type' => 'product', 'object_id' => $product->id]);
        session(['landing_page' => 'store.product']);

        $product->load(['descriptions', 'categories.descriptions']);
        $title = $product->getTitle() ?? $product->sku;

        $template = $this->templateResolver->resolve(
            entityTemplate: $product->getProperty('style', 'view'),
            type: 'product',
            fallback: 'store.product',
        );

        return response()->view($template, [
            'product' => $product,
            'title' => $title,
            'templateType' => 'product',
            'breadcrumbs' => [
                ['href' => route('common.home'), 'text' => 'Home'],
                ['href' => route('store.product', $product), 'text' => $title],
            ],
        ]);
    }

    public function category(Category $category): Response
    {
        session(['object_type' => 'category', 'object_id' => $category->id]);
        session(['landing_page' => 'store.category']);

        $category->load(['descriptions']);
        $title = $category->getTitle() ?? 'Category';

        $template = $this->templateResolver->resolve(
            entityTemplate: $category->getProperty('style', 'view'),
            type: 'category',
            fallback: 'store.category',
        );

        return response()->view($template, [
            'category' => $category,
            'title' => $title,
            'templateType' => 'category',
        ]);
    }

    public function post(Post $post): Response
    {
        // Only show published posts
        if (!$post->publish || !$post->status) {
            abort(404);
        }

        session(['object_type' => 'post', 'object_id' => $post->id]);
        session(['landing_page' => 'content.post']);

        $post->load(['descriptions']);
        $title = $post->getTitle() ?? 'Post';

        $template = $this->templateResolver->resolve(
            entityTemplate: $post->getProperty('style', 'view'),
            type: 'post',
            fallback: 'content.post',
        );

        return response()->view($template, [
            'post' => $post,
            'title' => $title,
            'templateType' => 'post',
        ]);
    }

    public function page(Post $page): Response
    {
        // Ensure it's a page type
        if ($page->type !== 'page') {
            abort(404);
        }

        session(['object_type' => 'page', 'object_id' => $page->id]);
        session(['landing_page' => 'content.page']);

        $page->load(['descriptions']);
        $title = $page->getTitle() ?? 'Page';

        $template = $this->templateResolver->resolve(
            entityTemplate: $page->getProperty('style', 'view'),
            type: 'page',
            fallback: 'content.page',
        );

        return response()->view($template, [
            'page' => $page,
            'title' => $title,
            'templateType' => 'page',
        ]);
    }

    public function search(Request $request): Response
    {
        $q = $request->get('q', '');
        session(['landing_page' => 'store.search']);

        $products = Product::search($q)->limit(20)->get();

        return response()->view('store.search', [
            'products' => $products,
            'query' => $q,
            'title' => "Search: {$q}",
            'templateType' => 'search',
        ]);
    }

    public function allProducts(): Response
    {
        session()->forget(['object_type', 'object_id']);
        session(['landing_page' => 'store.product.all']);

        return response()->view('store.products', [
            'title' => 'All Products',
            'templateType' => 'products',
        ]);
    }

    public function allCategories(): Response
    {
        session()->forget(['object_type', 'object_id']);
        session(['landing_page' => 'store.category.all']);

        return response()->view('store.categories', [
            'title' => 'All Categories',
            'templateType' => 'categories',
        ]);
    }

    public function allPosts(): Response
    {
        session()->forget(['object_type', 'object_id']);
        session(['landing_page' => 'content.post.all']);

        return response()->view('content.posts', [
            'title' => 'Blog',
            'templateType' => 'posts',
        ]);
    }

    /**
     * Email open tracking pixel (campaign).
     */
    public function trackOpen(int $campaign, int $contact): Response
    {
        \App\Models\CampaignStat::create([
            'campaign_id' => $campaign,
            'contact_id' => $contact,
            'store_url' => request()->fullUrl(),
            'server' => json_encode(request()->server->all()),
            'session' => json_encode(session()->all()),
            'request' => json_encode(request()->all()),
            'ref' => request()->header('referer'),
            'browser' => request()->userAgent(),
            'ip' => request()->ip(),
        ]);

        // Return a 1x1 transparent pixel
        return response(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='))
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache');
    }

    /**
     * Link click tracking + redirect (campaign).
     */
    public function trackClick(string $nonce): Response
    {
        $link = \App\Models\CampaignLink::where('link', $nonce)->first();

        if ($link) {
            \App\Models\CampaignLinkStat::create([
                'campaign_id' => $link->campaign_id,
                'link' => $nonce,
                'store_url' => request()->fullUrl(),
                'server' => json_encode(request()->server->all()),
                'browser' => request()->userAgent(),
                'ip' => request()->ip(),
            ]);

            return redirect($link->redirect);
        }

        return redirect('/');
    }

    /**
     * Unsubscribe from campaigns.
     */
    public function unsubscribe(string $token): Response
    {
        $contact = \App\Models\Contact::where('unsubscribe_token', $token)->first();

        if ($contact) {
            $contact->update(['is_active' => false]);
            return response()->view('marketing.unsubscribed', [
                'title' => 'Unsubscribed',
                'templateType' => 'unsubscribe',
            ]);
        }

        abort(404);
    }
}
