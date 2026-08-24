<script>
(function () {
    const form = document.currentScript.closest('form');
    if (!form) return;
    const submit = form.querySelector('button[type="submit"]');
    const csrf = form.querySelector('input[name="_token"]')?.value || '';
    const startUrl = <?php echo json_encode(route('admin.release-uploads.start'), 15, 512) ?>;
    const chunkUrl = <?php echo json_encode(route('admin.release-uploads.chunk'), 15, 512) ?>;
    const cancelBase = <?php echo json_encode(url('/admin/release-uploads'), 15, 512) ?>;
    const CHUNK_SIZE = 1.75 * 1024 * 1024;
    const CONCURRENCY = 6;
    const RETRIES = 2;

    function status(container, message, percent = null, error = false) {
        const box = container.querySelector('[data-upload-status]');
        if (!box) return;
        box.hidden = false;
        box.classList.toggle('is-error', error);
        box.querySelector('[data-upload-message]').textContent = message;
        const bar = box.querySelector('[data-upload-progress]');
        if (bar && percent !== null) bar.style.width = Math.max(0, Math.min(100, percent)) + '%';
    }
    async function jsonFetch(url, options) {
        const r = await fetch(url, options); const text = await r.text(); let body = {};
        try { body = text ? JSON.parse(text) : {}; } catch (_) {}
        if (!r.ok) throw new Error(body.message || body.error || 'Upload request failed.');
        return body;
    }
    async function chunk(file, token, index, total) {
        for (let a = 0; a <= RETRIES; a++) {
            try {
                const from = index * CHUNK_SIZE, to = Math.min(file.size, from + CHUNK_SIZE);
                const data = new FormData();
                data.append('_token', csrf); data.append('token', token); data.append('chunk_index', index);
                data.append('total_chunks', total); data.append('chunk', file.slice(from, to), file.name + '.part-' + index);
                return await jsonFetch(chunkUrl, {method:'POST', headers:{Accept:'application/json'}, body:data});
            } catch (e) { if (a >= RETRIES) throw e; await new Promise(x => setTimeout(x, 400 * (a + 1))); }
        }
    }
    async function upload(container) {
        const input = container.querySelector('input[type=file]'), file = input?.files?.[0];
        if (!file || file.size <= 6 * 1024 * 1024) return;
        const total = Math.ceil(file.size / CHUNK_SIZE); let token = null, next = 0, done = 0;
        try {
            status(container, 'Preparing fast upload…', 0);
            const started = await jsonFetch(startUrl, {method:'POST', headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json','Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'}, body:new URLSearchParams({file_name:file.name,total_size:file.size,total_chunks:total})});
            token = started.token;
            async function worker() { while (true) { const i = next++; if (i >= total) return; await chunk(file, token, i, total); done++; status(container, `Uploading ${file.name}… ${Math.round(done/total*100)}% · ${done}/${total} chunks`, done/total*100); } }
            await Promise.all(Array.from({length:Math.min(CONCURRENCY,total)}, worker));
            const name = input.name === 'package' ? 'upload_token' : 'update_upload_token';
            let hidden = form.querySelector(`input[name="${name}"]`);
            if (!hidden) { hidden=document.createElement('input'); hidden.type='hidden'; hidden.name=name; form.appendChild(hidden); }
            hidden.value = token; input.disabled = true; status(container, 'Upload complete. Finalizing package…', 100);
        } catch (e) {
            if (token) fetch(cancelBase+'/'+encodeURIComponent(token), {method:'DELETE', headers:{'X-CSRF-TOKEN':csrf,Accept:'application/json'}, keepalive:true}).catch(()=>{});
            status(container, e.message || 'Upload failed. Please try again.', 0, true); throw e;
        }
    }
    form.addEventListener('submit', async function (event) {
        if (form.dataset.chunkHandled === '1') return;
        const containers = [...form.querySelectorAll('[data-chunk-upload]')];
        const large = containers.filter(c => (c.querySelector('input[type=file]')?.files?.[0]?.size || 0) > 6 * 1024 * 1024);
        if (!large.length) return;
        event.preventDefault(); submit.disabled = true; const original = submit.textContent; submit.textContent = 'Uploading artifacts…';
        try { await Promise.all(large.map(upload)); form.dataset.chunkHandled = '1'; form.submit(); }
        catch (_) { submit.disabled = false; submit.textContent = original; }
    });
})();
</script>
<?php /**PATH /home/firoze/Downloads/RozeHub-release-artifacts-v1/RozeHub-final/resources/views/admin/releases/_chunk-uploader.blade.php ENDPATH**/ ?>