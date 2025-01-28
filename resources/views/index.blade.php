@extends('statamic::layout')

@section('content')
    <div id="alt-google-2fa">
        <!-- Header Content -->
        <section>
            <h1 class="mb-2">Alt Google 2FA</h1>
            <p>Settings for the Google 2FA integration. You may not have user roles if you're rocking the Free version of Statamic, in which case, 2FA for super users should be enough!</p>
        </section>
        <!-- End Header Content -->

        <div>
            <publish-form
                action="{{ cp_route('alt-google-2fa.save') }}"
                :blueprint='@json($blueprint)'
                :meta='@json($meta)'
                :values='@json($values)'
            ></publish-form>
        </div>
    </div>
@endsection
