<?php

use Illuminate\Routing\Route as RouteObject;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    pest()->browser()->withHost(Route::getRoutes()->getByName('public.home')->getDomain());

    $this->routes = collect(Route::getRoutes())
        ->filter(fn (RouteObject $route) => str_starts_with((string) $route->getName(), 'public.')
                                            && in_array('GET', $route->methods())
                                            && ! str_contains($route->uri(), '{'))
        ->mapWithKeys(fn (RouteObject $route) => [
            $route->getName() => '/'.ltrim($route->uri(), '/'),
        ]);

    expect($this->routes)->not->toBeEmpty();
});

it('renders every public page', function () {
    foreach ($this->routes->keys() as $name) {
        $this->get(route($name))->assertSuccessful();
    }
});

it('does not log anything to the console', function () {
    visit($this->routes->values()->all())->assertNoSmoke();
});

it('does not change appearance', function () {
    visit($this->routes->values()->all())->assertScreenshotMatches();
});
