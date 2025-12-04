<div
    id="display-finished-toggle-container"
    class="toggle-container-div"
    x-data="{
        checked: {{ $checked ? 'true' : 'false' }},
        toggle() {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('display_finished', this.checked ? '1' : '0');
            formData.append('redirect_to', window.location.href);

            fetch('{{ route('reservations.display-finished') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then((response) => {
                    if (!response.ok) {
                        new FilamentNotification()
                            .title('{{ __('filament.resources.reservations.filters.error') }}')
                            .danger()
                            .body('{{ __('filament.resources.reservations.filters.update_error') }}')
                            .send();
                        return null;
                    }
                    return response.json();
                })
                .then((data) => {
                    if (!data) return;
                    
                    if (data.redirect_to) {
                        window.location = data.redirect_to;
                        return;
                    }

                    window.location.reload();
                })
                .catch((error) => {
                    new FilamentNotification()
                        .title('{{ __('filament.resources.reservations.filters.error') }}')
                        .danger()
                        .body('{{ __('filament.resources.reservations.filters.update_error') }}')
                        .send();
                });
        },
    }"
>
    <input
        id="display-finished-toggle"
        type="checkbox"
        class="filament-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
        x-model="checked"
        @change="toggle"
    >
    <label for="display-finished-toggle" class="text-sm font-medium text-gray-700 dark:text-white whitespace-nowrap">
        {{ __('filament.resources.reservations.filters.show_finished') }}
    </label>
</div>

@vite('resources/js/filament/reservations/display-finished-toggle.js')
