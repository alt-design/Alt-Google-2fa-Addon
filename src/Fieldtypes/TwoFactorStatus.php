<?php

declare(strict_types=1);

namespace AltDesign\AltGoogle2FA\Fieldtypes;

use Statamic\Fields\Field;
use Statamic\Fields\Fieldtype;

class TwoFactorStatus extends Fieldtype
{
    protected static $title = 'Alt - Two Factor Status';
    protected static $handle = 'alt_google_2fa_status';

    /**
     * The blank/default value.
     *
     * @return array
     */
    public function defaultValue()
    {
        return null;
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return $data;
    }
}
