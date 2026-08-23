<script>
(function () {
    const script = document.currentScript;
    const form = script ? script.closest('form') : null;
    if (!form) return;

    const input = form.querySelector('input[type="file"][name="package"]');
    const submit = form.querySelector('button[type="submit"]');
    const tokenInput = form.querySelector('input[name="upload_token"]');
    const status = form.querySelector('[data-upload-status]');
    if (!input || !submit || !tokenInput || !status) return;

    const csrf = form.querySelector('input[name="_token"]')?.value || '';
    const startUrl = @json(route('developer.uploads.start'));
    const chunkUrl = @json(route('developer.uploads.chunk'));
    const cancelBase = @json(url('/developer/uploads'));

    // Keep every request safely below the default PHP upload_max_filesize=2M.
    // Multiple requests are uploaded concurrently, so a 50-100 MB package is much faster
    // than the previous sequential 4 MB uploader without requiring php.ini changes.
    const chunkSize = 1.75 * 1024 * 1024;
    const concurrency = 6;
    const maxRetries = 2;

    let uploading = false;
    let bypassSubmitHandler = false;
    let activeToken = null;
    let cancelled = false;

    function setStatus(message, progress = 0, error = false) {
        status.hidden = false;
        status.classList.toggle('is-error', error);
        const messageNode = status.querySelector('[data-upload-message]');
        const progressNode = status.querySelector('[data-upload-progress]');
        if (messageNode) messageNode.textContent = message;
        if (progressNode) progressNode.style.width = Math.max(0, Math.min(100, progress)) + '%';
    }

    async function jsonFetch(url, options) {
        const response = await fetch(url, options);
        const text = await response.text();
        let body = {};
        try { body = text ? JSON.parse(text) : {}; } catch (_) {}
        if (!response.ok) {
            throw new Error(body.message || body.error || ('Upload request failed (' + response.status + ').'));
        }
        return body;
    }

    async function sendChunk(file, token, index, totalChunks) {
        for (let attempt = 0; attempt <= maxRetries; attempt++) {
            try {
                if (cancelled) throw new Error('Upload cancelled.');
                const from = index * chunkSize;
                const to = Math.min(file.size, from + chunkSize);
                const chunk = file.slice(from, to);
                const data = new FormData();
                data.append('_token', csrf);
                data.append('token', token);
                data.append('chunk_index', String(index));
                data.append('total_chunks', String(totalChunks));
                data.append('chunk', chunk, file.name + '.part-' + index);

                return await jsonFetch(chunkUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: data
                });
            } catch (error) {
                if (attempt >= maxRetries) throw error;
                await new Promise(resolve => setTimeout(resolve, 500 * (attempt + 1)));
            }
        }
    }

    async function uploadInChunks(file) {
        const totalChunks = Math.ceil(file.size / chunkSize);
        let token = null;
        activeToken = null;
        cancelled = false;

        try {
            setStatus('Preparing fast upload…', 0);

            const started = await jsonFetch(startUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                },
                body: new URLSearchParams({
                    file_name: file.name,
                    total_size: String(file.size),
                    total_chunks: String(totalChunks)
                })
            });

            token = started.token;
            activeToken = token;
            if (!token) throw new Error('RozeHub did not return an upload token.');

            let nextIndex = 0;
            let completed = 0;
            let completedBytes = 0;

            // A small worker pool keeps the server busy without creating dozens of requests.
            async function worker() {
                while (true) {
                    const index = nextIndex++;
                    if (index >= totalChunks) return;
                    await sendChunk(file, token, index, totalChunks);
                    completed++;
                    completedBytes = Math.min(file.size, completedBytes + Math.min(chunkSize, file.size - index * chunkSize));
                    const percent = (completedBytes / file.size) * 100;
                    setStatus(
                        'Uploading package… ' + Math.round(percent) + '% · ' + completed + '/' + totalChunks + ' chunks',
                        percent
                    );
                }
            }

            await Promise.all(Array.from({ length: Math.min(concurrency, totalChunks) }, () => worker()));

            tokenInput.value = token;
            input.disabled = true;
            setStatus('Upload complete. Finalizing package…', 100);

            // The normal form POST consumes the completed external upload token.
            bypassSubmitHandler = true;
            form.requestSubmit(submit);
        } catch (error) {
            if (token) {
                fetch(cancelBase + '/' + encodeURIComponent(token), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    keepalive: true
                }).catch(() => {});
            }
            activeToken = null;
            uploading = false;
            submit.disabled = false;
            input.disabled = false;
            setStatus(error.message || 'Package upload failed.', 0, true);
        }
    }

    form.addEventListener('submit', function (event) {
        if (bypassSubmitHandler) {
            bypassSubmitHandler = false;
            return;
        }

        const file = input.files && input.files[0];
        if (!file || file.size <= 6 * 1024 * 1024) return;

        event.preventDefault();
        if (uploading) return;
        uploading = true;
        submit.disabled = true;
        uploadInChunks(file);
    });

    input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (!file) {
            status.hidden = true;
            return;
        }
        if (file.size > 6 * 1024 * 1024) {
            const chunks = Math.ceil(file.size / chunkSize);
            setStatus(
                'Large package selected (' + (file.size / 1048576).toFixed(1) + ' MB). Fast upload will use ' + concurrency + ' parallel streams (' + chunks + ' chunks).',
                0
            );
        } else {
            status.hidden = true;
        }
    });

    window.addEventListener('beforeunload', function (event) {
        if (uploading) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
})();
</script>
