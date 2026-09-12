<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PagesController extends Controller
{
    /**
     * Serve a page on the squawk domain.
     * Published public pages live on the public domain
     * and redirect there.
     */
    public function show(Page $page): View|RedirectResponse
    {
        if (! $page->restricted && $page->isPublished()) {
            return redirect()->route('public.pages.show', $page);
        }

        Gate::authorize('view', $page);

        return view('page', ['page' => $page]);
    }

    /**
     * Serve a page on the public domain.
     * Restricted pages redirect to their canonical squawk
     * URL, where authentication takes over.
     */
    public function showPublic(Page $page): View|RedirectResponse
    {
        if ($page->restricted && $page->isPublished()) {
            return redirect()->route('pages.show', $page);
        }

        abort_unless(! $page->restricted && $page->isPublished(), 404);

        return view('public.page', ['page' => $page]);
    }
}
