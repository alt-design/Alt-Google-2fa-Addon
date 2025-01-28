<?php namespace AltDesign\AltGoogle2FA\Http\Controllers;

use Illuminate\Http\Request;

use AltDesign\AltGoogle2FA\Helpers\Data;
use PragmaRX\Google2FA\Google2FA;
use Auth;

class AltGoogle2FAController
{

    /**
     *  Render the default options page.
     */
    public function index()
    {
        $data = new Data('settings');

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
    public function update(Request $request)
    {
        $data = new Data('settings');

        // Set the fields etc
        $blueprint = $data->getBlueprint(true);
        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        // Save the data
        $data->setAll($fields->process()->values()->toArray());

        return true;
    }

    public function enable()
    {
        // Display the 2FA verification form
        return view('alt-google-2fa::enable-2fa', ['csrf_token' => csrf_token()]);
    }

    public function show()
    {
        // Display the 2FA verification form
        return view('alt-google-2fa::attempt-otp', ['csrf_token' => csrf_token()]);
    }

    public function verify(Request $request)
    {
        $google2fa = new Google2FA();

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

}
