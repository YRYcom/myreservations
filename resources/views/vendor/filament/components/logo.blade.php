<div class="flex items-center gap-2">
    {{-- Logo clair (light mode) --}}
    <img 
        src="{{ asset('images/my_reserve_logo_dark.svg') }}" 
        alt="Logo" 
        class="h-8 dark:hidden"
    >

    {{-- Logo blanc (dark mode) --}}
    <img 
        src="{{ asset('images/my_reserve_logo_light.svg') }}" 
        alt="Logo" 
        class="h-8 hidden dark:block"
    >

    <span class="text-lg font-bold text-black dark:text-white">
        {{ __('app.name_app') }}
    </span>
</div>