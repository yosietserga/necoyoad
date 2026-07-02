<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Language;
use Illuminate\Http\Request;

/**
 * LanguageContext — resolves the current language from the request.
 *
 * Six-level detection priority (same as original Necoyoad, v5):
 *   1. ?language= GET parameter
 *   2. ?hl= GET parameter (also used for cache key)
 *   3. Session ('language' key)
 *   4. Cookie ('language' key)
 *   5. Browser HTTP_ACCEPT_LANGUAGE header
 *   6. Store's default language (config_language setting)
 *
 * @see v5 (multi-store/multi-language — 6-level language detection)
 */
class LanguageContext
{
    private ?Language $language = null;

    public function __construct(
        private readonly Request $request,
        private readonly StoreContext $storeContext
    ) {}

    public function resolve(): Language
    {
        if ($this->language) {
            return $this->language;
        }

        $languages = Language::where('status', true)->get()->keyBy('code');
        $code = null;

        // 1. ?language= GET
        if ($this->request->has('language') && $languages->has($this->request->query('language'))) {
            $code = $this->request->query('language');
        }
        // 2. ?hl= GET
        elseif ($this->request->has('hl') && $languages->has($this->request->query('hl'))) {
            $code = $this->request->query('hl');
        }
        // 3. Session
        elseif (session()->has('language') && $languages->has(session('language'))) {
            $code = session('language');
        }
        // 4. Cookie
        elseif ($this->request->hasCookie('language') && $languages->has($this->request->cookie('language'))) {
            $code = $this->request->cookie('language');
        }
        // 5. Browser HTTP_ACCEPT_LANGUAGE
        elseif ($this->request->server('HTTP_ACCEPT_LANGUAGE')) {
            $browserLanguages = explode(',', $this->request->server('HTTP_ACCEPT_LANGUAGE'));
            foreach ($browserLanguages as $browserLang) {
                $browserLang = trim(explode(';', $browserLang)[0]);
                foreach ($languages as $langCode => $lang) {
                    $locales = explode(',', $lang->locale);
                    if (in_array($browserLang, $locales)) {
                        $code = $langCode;
                        break 2;
                    }
                }
            }
        }

        // 6. Store's default language
        if (!$code) {
            $code = $this->storeContext->setting('config_language', 'en');
        }

        $this->language = $languages->get($code) ?? $languages->first() ?? new Language(['id' => 1, 'code' => 'en']);

        // Persist to session and queue cookie (cookie() alone doesn't send —
        // must use Cookie::queue() to attach it to the response)
        session(['language' => $code]);
        \Illuminate\Support\Facades\Cookie::queue('language', $code, 60 * 24 * 30); // 30 days

        return $this->language;
    }

    public function id(): int
    {
        return $this->resolve()->id;
    }

    public function code(): string
    {
        return $this->resolve()->code;
    }

    public function model(): Language
    {
        return $this->resolve();
    }
}
