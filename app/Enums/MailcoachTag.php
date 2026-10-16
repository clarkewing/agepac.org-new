<?php

namespace App\Enums;

/**
 * The Mailcoach tags that place a subscriber on one of our mailing lists.
 */
enum MailcoachTag: string
{
    /**
     * Every approved member.
     */
    case NEWSLETTER = 'newsletter';

    /**
     * Members with an active membership.
     */
    case MEMBERS_NEWSLETTER = 'members_newsletter';
}
