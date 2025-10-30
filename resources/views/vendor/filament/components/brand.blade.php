<style>
    .brand-logo{position:relative;display:block;overflow:hidden;width:1.25rem;height:1.25rem}
    .brand-logo img{position:absolute;inset:0;margin:auto;width:100%;height:100%;object-fit:contain}
    .brand-logo .logo-light{display:none}
    .brand-logo .logo-dark{display:block}
    html.dark .brand-logo .logo-light{display:block}
    html.dark .brand-logo .logo-dark{display:none}
    :root[data-theme='dark'] .brand-logo .logo-light{display:block}
    :root[data-theme='dark'] .brand-logo .logo-dark{display:none}
    </style>
<div style="display:flex;align-items:center;white-space:nowrap;">
    <div class="brand-logo flex-none">
        <img src="{{ asset('images/logo_light.png') }}" alt="MyReserve" class="logo-light" />
        <img src="{{ asset('images/logo_dark.png') }}" alt="MyReserve" class="logo-dark" />
    </div>

    <span style="margin-left:0.5rem;display:inline-block;" class="font-bold text-lg text-gray-900 dark:text-white leading-tight">
        {{ __('filament.app.name') }}
    </span>
</div>