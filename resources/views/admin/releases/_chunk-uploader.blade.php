<script>
(function () {
    const form = document.currentScript.closest('form');
    if (!form) return;
    const input = form.querySelector('input[type="file"][name="package"]');
    const submit = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-upload-status]');
    if (!input || !submit) return;

    const csrf = form.querySelector('input[name="_token"]')?.value || '';
    const startUrl = @json(route('admin.release-uploads.start'));
    const chunkUrl = @json(route('admin.release-uploads.chunk'));
    const cancelBase = @json(url('/admin/release-uploads'));
    const CHUNK_SIZE = 1.75 * 1024 * 1024;
    const CONCURRENCY = 6;
    const RETRIES = 2;
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
        if (!response.ok) throw new Error(body.message || body.error || 'Upload request failed.');
        return body;
    }

    async function sendChunk(file, token, index, totalChunks) {
        for (let attempt = 0; attempt <= RETRIES; attempt++) {
            try {
                const from = index * CHUNK_SIZE;
                const to = Math.min(file.size, from + CHUNK_SIZE);
                const chunk = file.slice(from, to);
                const data = new FormData();
                data.append('_token', csrf);
                data.append('token', token);
                data.append('chunk_index', String(index));
                data.append('total_chunks', String(totalChunks));
                data.append('chunk', chunk, file.name + '.part-' + index);
                return await jsonFetch(chunkUrl, {method:'POST',headers:{'Accept':'application/json'},body:data});
            } catch (error) {
                if (attempt >= RETRIES) throw error;
                await new Promise(resolve => setTimeout(resolve, 400 * (attempt + 1)));
            }
        }
    }

    form.addEventListener('submit', async function (event) {
        const file = input.files?.[0];
        if (!file || file.size <= 6 * 1024 * 1024 || uploading) return;

        event.preventDefault();
        uploading = true;
        submit.disabled = true;
        submit.dataset.originalText = submit.textContent;
        submit.textContent = 'Uploading…';
        const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
        let token = null;
        let nextIndex = 0;
        let completed = 0;

        try {
            setStatus('Preparing fast upload…', 0);
            const started = await jsonFetch(startUrl, {
                method:'POST',
                headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'},
                body:new URLSearchParams({file_name:file.name,total_size:String(file.size),total_chunks:String(totalChunks)})
            });
            token = started.token;

            async function worker() {
                while (true) {
                    const index = nextIndex++;
                    if (index >= totalChunks) return;
                    await sendChunk(file, token, index, totalChunks);
                    completed++;
                    const percent = (completed / totalChunks) * 100;
                    setStatus(`Uploading package… ${Math.round(percent)}% · ${completed}/${totalChunks} chunks`, percent);
                }
            }
            await Promise.all(Array.from({length:Math.min(CONCURRENCY,totalChunks)}, () => worker()));

            let hidden = form.querySelector('input[name="upload_token"]');
            if (!hidden) { hidden=document.createElement('input'); hidden.type='hidden'; hidden.name='upload_token'; form.appendChild(hidden); }
            hidden.value = token;
            input.disabled = true;
            setStatus('Upload complete. Finalizing package…', 100);
            form.submit();
        } catch (error) {
            if (token) fetch(cancelBase+'/'+encodeURIComponent(token), {method:'DELETE',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},keepalive:true}).catch(()=>{});
            setStatus(error.message || 'Upload failed. Please try again.', 0, true);
            submit.disabled = false;
            submit.textContent = submit.dataset.originalText || 'Save release';
            uploading = false;
        }
    });
})();
</script>
