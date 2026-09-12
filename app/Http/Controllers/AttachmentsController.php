<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\RedirectResponse;

class AttachmentsController extends Controller
{
    /**
     * Redirect the stable attachment URL to a short-lived signed file URL.
     */
    public function show(Attachment $attachment): RedirectResponse
    {
        return redirect()->to($attachment->temporaryUrl());
    }
}
