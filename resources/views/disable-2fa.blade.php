@extends('statamic::outside')

@section('content')
    @include('statamic::partials.outside-logo')
    <div class="flex flex-col justify-center items-center">
        <h1 class="mb-4">Disable 2FA</h1>
        <p class="my-4 text-center">If you'd like to disable 2FA on your account, <br />click the button below</p>

        <form action="{{ route('alt-google-2fa.disable') }}" class="flex flex-col justify-center" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Disable</button>
        </form>

        <a href="/" class="underline mt-4 text-sm">
            Cancel
        </a>
    </div>
@endsection
