@props(['type', 'label'])
<div class="d-flex gap-2" data-data-actions="{{ $type }}">
    <a class="btn btn-sm btn-outline-primary" href="{{ route('imports.type', $type) }}">Import</a>
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('reports.csv', $type) }}{{ request()->query('search') ? '?search='.urlencode(request()->query('search')) : '' }}" data-export-csv>Export CSV</a>
    <button class="btn btn-sm btn-outline-secondary" type="button" data-export-xlsx="{{ $type }}">Export Excel</button>
</div>
<script>
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-export-xlsx]');
        if (!button || typeof XLSX === 'undefined') return;
        const type = button.dataset.exportXlsx;
        const search = document.querySelector('[wire\:model="search"], [wire\:model\.live*="search"]')?.value || '';
        try {
            const response = await fetch(`{{ url('/laporan') }}/${type}.csv${search ? `?search=${encodeURIComponent(search)}` : ''}`);
            if (!response.ok) throw new Error('Export gagal.');
            const workbook = XLSX.read(await response.text(), { type: 'string' });
            XLSX.writeFile(workbook, `${type === 'books' ? 'katalog-buku' : type}_${new Date().toISOString().slice(0, 10)}.xlsx`);
        } catch (error) {
            window.dispatchEvent(new CustomEvent('app-error', { detail: error.message }));
        }
    });
</script>