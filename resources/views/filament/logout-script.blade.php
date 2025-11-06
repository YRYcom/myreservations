<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutLink = document.querySelector('a[href="#logout"]');
        
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("filament.admin.auth.logout") }}';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }
                
                document.body.appendChild(form);
                form.submit();
            });
        }
    });
</script>

