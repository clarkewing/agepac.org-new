<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Laravel\Nightwatch\Contracts\Ingest;
use Laravel\Nightwatch\Core;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

/*
|--------------------------------------------------------------------------
| Nightwatch exception reporting
|--------------------------------------------------------------------------
|
| Nightwatch's GlobalMiddleware calls $request->getMethod() to build an execution
| preview. On a request carrying a malformed `_method` override that throws a
| SuspiciousOperationException, which Nightwatch used to hand straight to
| Core::report() — bypassing the exception handler, and with it internalDontReport,
| where the framework deliberately places RequestExceptionInterface. Bots probing
| the site turned that into unfilterable noise.
|
| Fixed upstream in laravel/nightwatch#404, released in v1.28.5. These tests guard
| the behaviour so a downgrade or regression is caught here.
|
| See laravel/nightwatch#403 and laravel/framework#50735.
|
*/

beforeEach(function () {
    config()->set('nightwatch.enabled', true);
    config()->set('nightwatch.sampling.exceptions', 1);

    $this->ingest = new class implements Ingest
    {
        public array $writes = [];

        public function write(array $record): void
        {
            $this->writes[] = $record;
        }

        public function writeNow(array $record): void
        {
            $this->writes[] = $record;
        }

        public function ping(): void {}

        public function shouldDigest(bool $bool = true): void {}

        public function shouldDigestWhenBufferIsFull(bool $bool = true): void {}

        public function digest(): void {}

        public function flush(): void {}
    };

    $this->core = app(Core::class);
    $this->core->ingest = $this->ingest;
});

it('treats a malformed method override as non-reportable', function () {
    expect(app(ExceptionHandler::class)->shouldReport(
        new SuspiciousOperationException('Invalid HTTP method override.')
    ))->toBeFalse();
});

it('does not ingest exceptions reported from a hook that the handler ignores', function () {
    // `handled: true` is what GlobalMiddleware passes when captureRequestPreview() throws.
    $this->core->report(
        new SuspiciousOperationException('Invalid HTTP method override.'),
        handled: true,
    );

    expect($this->ingest->writes)->toBeEmpty();
});

it('still ingests ordinary exceptions reported from a hook', function () {
    $this->core->report(new RuntimeException('Whoops!'), handled: true);

    expect($this->ingest->writes)->not->toBeEmpty();
});
