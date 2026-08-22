<script>
(function () {
    const form = document.currentScript.closest('form');
    if (!form) return;
    const input = form.querySelector('input[type="file"][name="package"]');
    const submit = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-upload-status]');
    if (!input || !submit) return;

    const csrf = form.querySelector('input[name="_token"]')?.value || '';
    const startUrl = <?php echo json_encode(route('admin.release-uploads.start'), 15, 512) ?>;
    const chunkUrl = <?php echo json_encode(route('admin.release-uploads.chunk'), 15, 512) ?>;
    const cancelBase = <?php echo json_encode(url('/admin/release-uploads'), 15, 512) ?>;
    const CHUNK_SIZE = 4 * 1024 * 1024;
    let uploading = false;

    function setStatus(message, percent = null, error = false) {
        if (!status) return;
        status.hidden = false;
        status.classList.toggle('is-error', error);
        status.querySelector('[data-upload-message]').textContent = message;
        const bar = status.querySelector('[data-upload-progress]');
        if (bar && percent !== null) bar.style.width = Math.max(0, Math.min(100, percent)) + '%';
    }

    async function jsonFetch(url, options) {
        const response = await fetch(url, options);
        const text = await response.text();
        let body = {};
        try { body = text ? JSON.parse(text) : {}; } catch (_) {}
        if (!response.ok) {
            throw new Error(body.message || body.error || 'Upload request failed.');
        }
        return body;
    }

    form.addEventListener('submit', async function (event) {
        const file = input.files?.[0];
        if (!file || file.size <= 6 * 1024 * 1024 || uploading) return;

        event.preventDefault();
        uploading = true;
        submit.disabled = true;
        submit.dataset.originalText = submit.textContent;
        submit.textContent = 'Uploading package…';

        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
        let token = null;

        try {
            setStatus('Preparing secure upload…', 0);
            const started = await jsonFetch(startUrl, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'},
                body: new URLSearchParams({
                    file_name: file.name,
                    total_size: String(file.size),
                    total_chunks: String(totalChunks)
                })
            });
            token = started.token;

            for (let index = 0; index < totalChunks; index++) {
                const chunk = file.slice(index * CHUNK_SIZE, Math.min(file.size, (index + 1) * CHUNK_SIZE));
                const data = new FormData();
                data.append('_token', csrf);
                data.append('token', token);
                data.append('chunk_index', String(index));
                data.append('total_chunks', String(totalChunks));
                data.append('chunk', chunk, file.name + '.part');

                let uploaded = false;
                let lastError = null;
                for (let attempt = 1; attempt <= 3 && !uploaded; attempt++) {
                    try {
                        await jsonFetch(chunkUrl, {
                            method: 'POST',
                            headers: {'Accept': 'application/json'},
                            body: data
                        });
                        uploaded = true;
                    } catch (error) {
                        lastError = error;
                        if (attempt < 3) {
                            await new Promise(resolve => setTimeout(resolve, attempt * 700));
                        }
                    }
                }
                if (!uploaded) throw lastError || new Error('Chunk upload failed.');

                const percent = ((index + 1) / totalChunks) * 100;
                setStatus(`Uploading package… ${Math.round(percent)}%`, percent);
            }

            let hidden = form.querySelector('input[name="upload_token"]');
            if (!hidden) {
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'upload_token';
                form.appendChild(hidden);
            }
            hidden.value = token;
            input.disabled = true;
            setStatus('Upload complete. Saving release metadata…', 100);
            form.submit();
        } catch (error) {
            if (token) {
                fetch(cancelBase + '/' + encodeURIComponent(token), {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}
                }).catch(() => {});
            }
            setStatus(error.message || 'Upload failed. Please try again.', 0, true);
            submit.disabled = false;
            submit.textContent = submit.dataset.originalText || 'Save release';
            uploading = false;
        }
    });
})();
</script>
<?php /**PATH /home/firoze/projects/others/RozeHub/resources/views/admin/releases/_chunk-uploader.blade.php ENDPATH**/ ?>