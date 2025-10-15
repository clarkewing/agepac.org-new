<?php

namespace App\Actions\Mailcoach;

use App\Services\Mailcoach\MailcoachApi;

readonly class MailcoachAction
{
    public function __construct(protected MailcoachApi $mailcoachApi) {}
}
