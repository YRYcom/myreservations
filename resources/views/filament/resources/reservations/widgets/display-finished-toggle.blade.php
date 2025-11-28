<div
    class="flex items-center gap-2"
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
                    if (! response.ok) {
                        throw new Error('Request failed');
                    }

                    return response.json();
                })
                .then((data) => {
                    if (data.redirect_to) {
                        window.location = data.redirect_to;
                        return;
                    }

                    window.location.reload();
                })
                .catch(() => window.location.reload());
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
    <label for="display-finished-toggle" class="text-sm font-medium text-gray-700 dark:text-white">
        {{ __('filament.resources.reservations.filters.show_finished') }}
    </label>
</div>

