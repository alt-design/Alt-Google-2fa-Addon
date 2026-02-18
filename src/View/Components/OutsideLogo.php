<?php

namespace AltDesign\AltGoogle2FA\View\Components;

use Statamic\Statamic;

class OutsideLogo extends \Statamic\View\Components\OutsideLogo
{
    public function render()
    {
        return view('alt-google-2fa::components.outside-logo', [
            'customLogo' => $this->customLogo(),
            'customDarkLogo' => $this->customLogo(dark: true),
            'customLogoText' => $this->customLogo(text: true),
        ]);
    }
}
