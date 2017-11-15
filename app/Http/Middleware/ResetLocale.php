<?php

/**
 * 本地化语言重置
 */

namespace App\Http\Middleware;

use Closure;

class ResetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowLang = ['cn', 'en', 'ru'];

        $locale = $request->header('Accept-Language', 'cn');
        $locale = strtolower($locale);

        $langMap = [
            'cn' => 'cn',
            'zh_cn' => 'cn',
            'en' => 'en',
            'en_us' => 'en',
            'ru' => 'ru',
            'ru_ru' => 'ru'
        ];
        $locale = $langMap[$locale] ?? 'cn';

        if (!empty($locale) &&
            in_array($locale, $allowLang)) {
            app('translator')->setLocale($locale);
        }

        return $next($request);
    }
}