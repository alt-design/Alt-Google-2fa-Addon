<?php namespace AltDesign\AltGoogle2FA\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use AltDesign\AltGoogle2FA\Helpers\Data;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use Auth;

class AltGoogle2FAController
{

    /**
     *  Render the default options page.
     */
    public function index(Data $data)
    {
        $blueprint = $data->getBlueprint(true);
        $fields = $blueprint->fields()->addValues($data->all())->preProcess();

        // Check if the asset container exists
        $contents = $blueprint->contents();

        $blueprint->setContents($contents);

        return view('alt-google-2fa::index', [
            'blueprint' => $blueprint->toPublishArray(),
            'values'    => $fields->values(),
            'meta'      => $fields->meta(),
        ]);
    }

    /**
     * Update the settings.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request, Data $data)
    {
        // Set the fields etc
        $blueprint = $data->getBlueprint(true);
        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        // Save the data
        $data->setAll($fields->process()->values()->toArray());

        return true;
    }

    /**
     * Returns the "Enable" view, where users can setup 2FA.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function enable()
    {
        return view('alt-google-2fa::enable-2fa');
    }

    /**
     * Returns the template that starts the OTP check
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show()
    {
        return view('alt-google-2fa::attempt-otp');
    }

    /**
     * Returns the form that allows users to disable 2FA on their account.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function disableForm()
    {
        return view('alt-google-2fa::disable-2fa');
    }

    /**
     * Verifies the OTP with the 2FA service, flags a user as verified if it checks out.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function verify(Request $request, Google2FA $google2fa)
    {

        // Retrieve logged-in user's 2FA secret
        $user = Auth::user();
        $secret = $user->google_secret_2fa_key;

        // Validate the token entered by the user
        $valid = $google2fa->verifyKey($secret, $request->input('one_time_password'));

        if ($valid) {
            // Mark session as 2FA-verified
            session(['2fa_verified' => true]);

            // Set this user to have enabled 2FA
            $user->enabled_2fa = true;
            $user->saveQuietly();

            return redirect()->intended(); // Redirect to intended destination
        }

        // Invalid token, return back with error
        return redirect()->back()->withErrors(['Invalid token provided.']);
    }

    /**
     * Disables 2FA on a users model.
     *
     * @return RedirectResponse
     */
    public function disable()
    {
        $user = Auth::user();
        $user->google_secret_2fa_key = null;
        $user->enabled_2fa = false;
        $user->saveQuietly();

        return redirect()->to('/');
    }
}
