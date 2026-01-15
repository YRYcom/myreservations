<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SetLocaleTest extends TestCase
{
    private SetLocale $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SetLocale();
    }

    public function test_it_sets_french_locale_when_accept_language_is_fr(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('HTTP_ACCEPT_LANGUAGE', 'fr-FR,fr;q=0.9');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('fr', App::getLocale());
            return response('OK');
        });
    }

    public function test_it_sets_english_locale_when_accept_language_is_en(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('HTTP_ACCEPT_LANGUAGE', 'en-US,en;q=0.9');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('en', App::getLocale());
            return response('OK');
        });
    }

    public function test_it_falls_back_to_default_locale_for_unsupported_language(): void
    {
        $defaultLocale = config('app.locale');
        
        $request = Request::create('/test', 'GET');
        $request->server->set('HTTP_ACCEPT_LANGUAGE', 'de-DE,de;q=0.9');

        $this->middleware->handle($request, function ($req) use ($defaultLocale) {
            $this->assertEquals($defaultLocale, App::getLocale());
            return response('OK');
        });
    }

    public function test_it_handles_missing_accept_language_header(): void
    {
        $defaultLocale = config('app.locale');
        
        $request = Request::create('/test', 'GET');
        // No HTTP_ACCEPT_LANGUAGE header set

        $this->middleware->handle($request, function ($req) use ($defaultLocale) {
            // Should fall back to default locale
            $this->assertEquals($defaultLocale, App::getLocale());
            return response('OK');
        });
    }

    public function test_it_extracts_only_first_two_characters_of_locale(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('HTTP_ACCEPT_LANGUAGE', 'fr-CA,fr;q=0.9,en;q=0.8');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('fr', App::getLocale());
            return response('OK');
        });
    }

    public function test_it_passes_request_to_next_middleware(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('HTTP_ACCEPT_LANGUAGE', 'en-US');

        $nextCalled = false;
        
        $response = $this->middleware->handle($request, function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('Success');
        });

        $this->assertTrue($nextCalled);
        $this->assertEquals('Success', $response->getContent());
    }

    public function test_it_handles_short_locale_codes(): void
    {
        $request = Request::create('/test', 'GET');
        $request->server->set('HTTP_ACCEPT_LANGUAGE', 'fr');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('fr', App::getLocale());
            return response('OK');
        });
    }

    public function test_it_handles_single_character_locale_gracefully(): void
    {
        $defaultLocale = config('app.locale');
        
        $request = Request::create('/test', 'GET');
        $request->server->set('HTTP_ACCEPT_LANGUAGE', 'x');

        $this->middleware->handle($request, function ($req) use ($defaultLocale) {
            $this->assertEquals($defaultLocale, App::getLocale());
            return response('OK');
        });
    }

    public function test_it_only_accepts_en_and_fr_locales(): void
    {
        $defaultLocale = config('app.locale');
        
        $unsupportedLocales = ['es', 'de', 'it', 'pt', 'ja', 'zh'];

        foreach ($unsupportedLocales as $locale) {
            $request = Request::create('/test', 'GET');
            $request->server->set('HTTP_ACCEPT_LANGUAGE', $locale);

            $this->middleware->handle($request, function ($req) use ($defaultLocale) {
                $this->assertEquals($defaultLocale, App::getLocale());
                return response('OK');
            });
        }
    }
}
