import './bootstrap';
import 'bootstrap';
import Swal from 'sweetalert2';

window.addEventListener('app-error', (event) => Swal.fire({ title: 'Gagal', text: event.detail, icon: 'error' }));

window.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-confirm-delete]');
    if (!button || button.dataset.confirmed === 'true') return;

    event.preventDefault();
    event.stopImmediatePropagation();
    const result = await Swal.fire({
        title: 'Hapus data?',
        text: 'Data yang sudah memiliki histori tidak dapat dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
    });

    if (result.isConfirmed) {
        button.dataset.confirmed = 'true';
        button.click();
        delete button.dataset.confirmed;
    }
}, true);

window.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        document.getElementById('livewire-error')?.classList.add('d-none');

        fail(({ preventDefault }) => {
            preventDefault();
            const alert = document.getElementById('livewire-error');
            if (alert) {
                alert.textContent = 'Permintaan gagal. Periksa koneksi lalu coba lagi.';
                alert.classList.remove('d-none');
            }
            Swal.fire({ title: 'Permintaan gagal', text: 'Periksa koneksi lalu coba lagi.', icon: 'error' });
        });
    });
});
