<div class="my-5 p-10 relative z-10">
    <div class="flex items-center justify-center">
        <div class="max-h-[400px]">
            @if ($customLogo)
                <img src="{{ $customLogo }}" alt="{{ config('statamic.cp.custom_cms_name') }}" class="white-label-logo dark:hidden">
                <img src="{{ $customDarkLogo }}" alt="{{ config('statamic.cp.custom_cms_name') }}" class="white-label-logo hidden dark:block">
            @elseif ($customLogoText)
                <div class="max-w-xs mx-auto text-lg font-medium text-center opacity-50 dark:text-white">{{ $customLogoText }}</div>
            @else
                @cp_svg('statamic-logo-lime', 'h-12')
            @endif
        </div>
    </div>
</div>