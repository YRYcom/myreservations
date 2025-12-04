@vite('resources/js/filament/reservations/display-finished-toggle.js')

<div
    id="display-finished-toggle-container"
    class="toggle-container-div"
    data-route-url="{{ route('reservations.display-finished') }}"
    data-error-title="{{ __('filament.resources.reservations.filters.error') }}"
    data-error-message="{{ __('filament.resources.reservations.filters.update_error') }}"
    x-data="displayFinishedToggle({{ $checked ? 'true' : 'false' }})"
>
    <input
        id="display-finished-toggle"
        type="checkbox"
        class="filament-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
        x-model="checked"
        @change="toggle()"
    >
    <label for="display-finished-toggle" class="text-sm font-medium text-gray-700 dark:text-white whitespace-nowrap">
        {{ __('filament.resources.reservations.filters.show_finished') }}
    </label>
</div>
