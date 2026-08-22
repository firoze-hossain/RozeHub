<?php $__env->startSection('content'); ?>
<div class="admin-page-head admin-doc-form-head">
    <div class="admin-doc-title-wrap">
        <div class="admin-doc-project-mark"><?php echo e($project->icon); ?></div>
        <div>
            <span><?php echo e(strtoupper($project->name)); ?> · DOCUMENTATION</span>
            <h2><?php echo e($mode === 'create' ? 'Create documentation page' : 'Edit documentation page'); ?></h2>
            <p>Write rich documentation with familiar editor controls, then publish it as clean RozeHub Markdown.</p>
        </div>
    </div>
    <a class="admin-secondary" href="<?php echo e(route('admin.documentation.project',$project)); ?>">← Documentation workspace</a>
</div>

<form id="documentation-page-form" class="admin-doc-editor-layout" method="POST" action="<?php echo e($mode === 'create' ? route('admin.documentation.pages.store',$project) : route('admin.documentation.pages.update',$page)); ?>">
    <?php echo csrf_field(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode==='edit'): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <main class="admin-form-card admin-doc-editor">
        <div class="admin-editor-section-head">
            <span>ARTICLE</span>
            <h3>Page content</h3>
            <p>Format your documentation like a familiar word processor. Headings, emphasis, lists, links, quotes and code blocks are converted to the documentation format automatically.</p>
        </div>

        <div class="form-grid two">
            <label>Title<input name="title" value="<?php echo e(old('title',$page->title)); ?>" placeholder="e.g. Installation guide" required></label>
            <label>Slug<input name="slug" value="<?php echo e(old('slug',$page->slug)); ?>" placeholder="auto-generated if blank"><small>Used in the public documentation URL.</small></label>
        </div>
        <label>Summary<input name="summary" value="<?php echo e(old('summary',$page->summary)); ?>" maxlength="500" placeholder="One or two sentences describing this page."></label>

        <div class="doc-rich-editor" data-editor>
            <div class="doc-editor-label-row">
                <label for="doc-rich-content">Content <span class="hint">Rich text editor</span></label>
                <span class="doc-save-format">Saved as documentation Markdown</span>
            </div>

            <div class="doc-toolbar" role="toolbar" aria-label="Documentation formatting toolbar">
                <div class="doc-tool-group">
                    <button type="button" class="doc-tool" data-command="undo" title="Undo">↶</button>
                    <button type="button" class="doc-tool" data-command="redo" title="Redo">↷</button>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group doc-style-group">
                    <label class="doc-toolbar-label" for="doc-text-style">Style</label>
                    <select id="doc-text-style" class="doc-style-select" data-format-block aria-label="Text style">
                        <option value="p">Paragraph</option>
                        <option value="h2">Heading 1</option>
                        <option value="h3">Heading 2</option>
                        <option value="h4">Heading 3</option>
                    </select>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group doc-font-group">
                    <label class="doc-toolbar-label" for="doc-font-family">Font</label>
                    <select id="doc-font-family" class="doc-font-select" aria-label="Font family">
                        <option value="Inter">Inter</option>
                        <option value="Arial">Arial</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                        <option value="Courier New">Courier New</option>
                    </select>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group doc-size-group">
                    <label class="doc-toolbar-label" for="doc-font-size">Size</label>
                    <div class="doc-size-controls">
                        <button type="button" class="doc-size-step" data-size-step="-1" title="Decrease text size (Ctrl + -)">−</button>
                        <select id="doc-font-size" class="doc-font-size-select" aria-label="Font size">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [9,10,11,12,13,14,15,16,18,20,22,24,26,28,32,36,40,48,56,64]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($size); ?>" <?php echo e($size === 16 ? 'selected' : ''); ?>><?php echo e($size); ?> px</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <button type="button" class="doc-size-step" data-size-step="1" title="Increase text size (Ctrl + +)">+</button>
                    </div>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group doc-color-group">
                    <label class="doc-toolbar-label" for="doc-text-color">Text</label>
                    <input id="doc-text-color" class="doc-color-input" type="color" value="#25362f" aria-label="Text color" title="Text color">
                    <button type="button" class="doc-tool doc-color-reset" data-command="foreColor" data-value="#25362f" title="Reset text color">A</button>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group">
                    <button type="button" class="doc-tool strong" data-command="bold" title="Bold"><b>B</b></button>
                    <button type="button" class="doc-tool italic" data-command="italic" title="Italic"><i>I</i></button>
                    <button type="button" class="doc-tool underline" data-command="underline" title="Underline"><u>U</u></button>
                    <button type="button" class="doc-tool strike" data-command="strikeThrough" title="Strikethrough"><s>S</s></button>
                    <button type="button" class="doc-tool" data-command="removeFormat" title="Clear formatting">Tx</button>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group">
                    <button type="button" class="doc-tool" data-command="insertUnorderedList" title="Bulleted list">•≡</button>
                    <button type="button" class="doc-tool" data-command="insertOrderedList" title="Numbered list">1≡</button>
                    <button type="button" class="doc-tool" data-command="formatBlock" data-value="blockquote" title="Quote">❝</button>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group">
                    <button type="button" class="doc-tool" data-command="createLink" title="Insert link">↗</button>
                    <button type="button" class="doc-tool doc-code-tool" data-insert-code="inline" title="Inline code">&lt;/&gt;</button>
                    <button type="button" class="doc-tool doc-code-tool" data-insert-code="block" title="Code block">{ }</button>
                </div>
                <div class="doc-toolbar-divider"></div>
                <div class="doc-tool-group">
                    <button type="button" class="doc-tool" data-command="justifyLeft" title="Align left">≡</button>
                    <button type="button" class="doc-tool" data-command="justifyCenter" title="Align center">≡</button>
                    <button type="button" class="doc-tool" data-command="justifyRight" title="Align right">≡</button>
                </div>
            </div>

            <div id="doc-rich-content" class="doc-rich-content" contenteditable="true" role="textbox" aria-multiline="true" spellcheck="true" data-placeholder="Start writing your documentation here…"></div>
            <textarea id="doc-content-source" name="content" class="doc-content-source" required><?php echo e(old('content',$page->content)); ?></textarea>

            <div class="doc-editor-footer">
                <div class="doc-editor-tip"><span>⌘</span><strong>Tip:</strong> Use headings to structure long documentation pages. Code blocks and lists are preserved on the public site.</div>
                <div class="doc-word-count"><span id="doc-word-count">0 words</span><span>·</span><span id="doc-char-count">0 characters</span></div>
            </div>
        </div>
    </main>

    <aside class="admin-doc-editor-side">
        <section class="admin-form-card admin-doc-settings-card">
            <div class="admin-editor-section-head"><span>DOCUMENT IDENTITY</span><h3>Where does this page belong?</h3></div>
            <label>Documentation release
                <select name="release_id" id="release_id">
                    <option value="" data-version="All versions">General · all releases</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($release->id); ?>" data-version="<?php echo e($release->version); ?>" <?php echo e(old('release_id',$page->release_id)==$release->id ? 'selected':''); ?>>
                            v<?php echo e($release->version); ?> · <?php echo e(ucfirst($release->channel ?: 'release')); ?><?php echo e($release->is_published ? '' : ' · unpublished'); ?>

                        </option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
                <small class="field-help">Choose a release for version-specific instructions. General pages remain visible across releases.</small>
            </label>
            <div class="admin-doc-version-preview">
                <span>PUBLIC VERSION</span>
                <strong id="resolved-version"><?php echo e($page->release ? 'v'.$page->release->version : 'All versions'); ?></strong>
                <small id="resolved-version-note"><?php echo e($page->release ? 'This page is scoped to one release.' : 'This page is shared across releases.'); ?></small>
            </div>
            <label>Section<select name="documentation_section_id"><option value="">Unassigned</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($section->id); ?>" <?php echo e(old('documentation_section_id',$page->documentation_section_id)==$section->id?'selected':''); ?>><?php echo e($section->title); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label>
            <label>Page type<select name="kind"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['overview','guide','installation','reference','architecture','api','tutorial','operations','development','troubleshooting','release','release-notes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kind): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($kind); ?>" <?php echo e(old('kind',$page->kind)===$kind?'selected':''); ?>><?php echo e(strtoupper(str_replace('-',' ',$kind))); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label>
            <input type="hidden" name="version" id="version" value="<?php echo e(old('version',$page->version)); ?>">
        </section>

        <section class="admin-form-card admin-doc-publish-card">
            <div class="admin-editor-section-head"><span>PUBLISHING</span><h3>Visibility</h3></div>
            <label class="admin-doc-publish-toggle"><input type="checkbox" name="is_published" value="1" <?php echo e(old('is_published',$page->is_published) ? 'checked':''); ?>><span><strong>Publish publicly</strong><small>Visible immediately in the public documentation site.</small></span></label>
            <div class="form-actions"><a class="admin-secondary" href="<?php echo e(route('admin.documentation.project',$project)); ?>">Cancel</a><button class="admin-primary" type="submit"><?php echo e($mode==='create' ? 'Create page' : 'Save page'); ?></button></div>
        </section>

        <section class="admin-doc-version-help">
            <strong>When should I assign a release?</strong>
            <ul>
                <li><b>General:</b> concepts that apply to every version.</li>
                <li><b>Release:</b> changed commands, APIs, installation, compatibility, or release notes.</li>
                <li><b>NOVAOS:</b> strongly prefer release-specific pages for installation and hardware requirements.</li>
                <li><b>Roze / StratosDB:</b> tie syntax, compiler, SQL, storage, and compatibility changes to a release.</li>
            </ul>
        </section>
    </aside>
</form>

<script>
(function () {
    const form = document.getElementById('documentation-page-form');
    const editor = document.getElementById('doc-rich-content');
    const source = document.getElementById('doc-content-source');
    const toolbar = document.querySelector('.doc-toolbar');
    const wordCount = document.getElementById('doc-word-count');
    const charCount = document.getElementById('doc-char-count');
    const fontFamily = document.getElementById('doc-font-family');
    const textColor = document.getElementById('doc-text-color');
    const styleSelect = document.querySelector('[data-format-block]');
    const fontSize = document.getElementById('doc-font-size');
    const sizeStepButtons = document.querySelectorAll('[data-size-step]');
    const statefulCommands = ['bold', 'italic', 'underline', 'strikeThrough'];

    if (!form || !editor || !source || !toolbar) return;

    let savedRange = null;
    let selectionInsideEditor = false;
    let markerId = 0;

    function isRangeInsideEditor(range) {
        if (!range) return false;
        const node = range.commonAncestorContainer;
        return node === editor || editor.contains(node);
    }

    function getLiveSelection() {
        const selection = window.getSelection();
        if (!selection || !selection.rangeCount) return null;
        const range = selection.getRangeAt(0);
        return isRangeInsideEditor(range) ? range : null;
    }

    function saveSelection() {
        const range = getLiveSelection();
        if (!range) return false;
        savedRange = range.cloneRange();
        selectionInsideEditor = true;
        return true;
    }

    function clearSavedSelection() {
        savedRange = null;
        selectionInsideEditor = false;
    }

    function restoreSelection(focusEditor = true) {
        if (!savedRange || !selectionInsideEditor || !isRangeInsideEditor(savedRange)) return false;
        if (focusEditor) editor.focus({ preventScroll: true });
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(savedRange.cloneRange());
        return true;
    }

    function updateSavedSelectionFromBrowser() {
        const range = getLiveSelection();
        if (range) {
            savedRange = range.cloneRange();
            selectionInsideEditor = true;
        }
    }

    function setToolbarButtonState(button, active) {
        button.classList.toggle('is-active', !!active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
    }

    function updateToolbarState() {
        const range = getLiveSelection() || savedRange;
        if (!range || !isRangeInsideEditor(range)) {
            toolbar.querySelectorAll('[data-command]').forEach(button => {
                if (statefulCommands.includes(button.dataset.command)) setToolbarButtonState(button, false);
            });
            return;
        }

        // queryCommandState follows the browser's editing state at the
        // current caret/selection. Restore the saved range first so the
        // toolbar remains accurate even while focus is on a control.
        const hadFocus = document.activeElement === editor;
        if (!hadFocus) restoreSelection(false);

        statefulCommands.forEach(command => {
            const button = toolbar.querySelector('[data-command="' + command + '"]');
            if (!button) return;
            let active = false;
            try { active = document.queryCommandState(command); } catch (e) {}
            setToolbarButtonState(button, active);
        });

        const styleButton = styleSelect;
        if (styleButton) {
            let block = 'p';
            try {
                const value = String(document.queryCommandValue('formatBlock') || '').toLowerCase().replace(/[<>]/g, '');
                if (['h2', 'h3', 'h4', 'p'].includes(value)) block = value;
            } catch (e) {}
            styleButton.value = block;
        }

        if (fontFamily) {
            try {
                const value = document.queryCommandValue('fontName');
                if (value) {
                    const normalized = String(value).replace(/[&quot;']/g, '').trim();
                    const match = Array.from(fontFamily.options).find(o => o.value.toLowerCase() === normalized.toLowerCase());
                    if (match) fontFamily.value = match.value;
                }
            } catch (e) {}
        }
    }

    function placeMarkers() {
        if (!restoreSelection(true)) return null;
        const selection = window.getSelection();
        if (!selection || !selection.rangeCount) return null;
        const range = selection.getRangeAt(0);
        if (range.collapsed || !isRangeInsideEditor(range)) return null;

        const id = ++markerId;
        const start = document.createElement('span');
        const end = document.createElement('span');
        start.dataset.rozeSelectionMarker = 'start-' + id;
        end.dataset.rozeSelectionMarker = 'end-' + id;
        start.className = 'roze-selection-marker';
        end.className = 'roze-selection-marker';
        start.setAttribute('aria-hidden', 'true');
        end.setAttribute('aria-hidden', 'true');
        start.textContent = '\u200b';
        end.textContent = '\u200b';

        const endRange = range.cloneRange();
        endRange.collapse(false);
        endRange.insertNode(end);

        const startRange = range.cloneRange();
        startRange.collapse(true);
        startRange.insertNode(start);

        const work = document.createRange();
        work.setStartAfter(start);
        work.setEndBefore(end);
        selection.removeAllRanges();
        selection.addRange(work);
        return { start, end };
    }

    function restoreBetweenMarkers(markers) {
        if (!markers || !markers.start.isConnected || !markers.end.isConnected) return false;
        const selection = window.getSelection();
        const range = document.createRange();
        range.setStartAfter(markers.start);
        range.setEndBefore(markers.end);
        selection.removeAllRanges();
        selection.addRange(range);
        return true;
    }

    function removeMarkersAndKeepSelection(markers) {
        if (!markers) return;
        restoreBetweenMarkers(markers);
        const selection = window.getSelection();
        let range = selection && selection.rangeCount ? selection.getRangeAt(0).cloneRange() : null;

        markers.start.remove();
        markers.end.remove();

        if (range) {
            // Rebuild the range from the text nodes around the former markers.
            // The browser normally adjusts the boundary automatically; if it
            // remains valid, explicitly restore it so repeated toolbar actions
            // continue using the same selection.
            if (isRangeInsideEditor(range)) {
                selection.removeAllRanges();
                selection.addRange(range);
            }
        }
        saveSelection();
    }

    function runWithSelection(command) {
        if (!restoreSelection(true)) return false;
        command();
        saveSelection();
        return true;
    }

    function runWithMarkedSelection(command) {
        const markers = placeMarkers();
        if (!markers) return false;
        try {
            restoreBetweenMarkers(markers);
            command();
            restoreBetweenMarkers(markers);
        } finally {
            removeMarkersAndKeepSelection(markers);
        }
        return true;
    }

    function normalizeSize(value) {
        const options = Array.from(fontSize?.options || []).map(o => Number(o.value));
        if (!options.length) return 16;
        const numeric = Number(value) || 16;
        return options.reduce((a, b) => Math.abs(b - numeric) < Math.abs(a - numeric) ? b : a, options[0]);
    }

    function selectedOrCurrentSize() {
        const selection = window.getSelection();
        let node = selection && selection.rangeCount ? selection.anchorNode : null;
        if (node && node.nodeType === Node.TEXT_NODE) node = node.parentElement;
        const element = node?.closest?.('[style*="font-size"], font[size]');
        const styleMatch = element?.style?.fontSize?.match(/([0-9.]+)px/i);
        if (styleMatch) return normalizeSize(styleMatch[1]);
        const fontSizeAttr = element?.getAttribute?.('size');
        if (fontSizeAttr) {
            const map = {1: 10, 2: 13, 3: 16, 4: 18, 5: 24, 6: 32, 7: 48};
            if (map[fontSizeAttr]) return normalizeSize(map[fontSizeAttr]);
        }
        return normalizeSize(fontSize?.value || 16);
    }

    function setFontSize(value) {
        const live = getLiveSelection();
        if (live && !live.collapsed) saveSelection();
        if (!savedRange || !selectionInsideEditor || savedRange.collapsed) return false;

        const size = normalizeSize(value);
        const changed = runWithMarkedSelection(() => {
            document.execCommand('fontSize', false, '7');
            editor.querySelectorAll('font[size="7"]').forEach(font => {
                const span = document.createElement('span');
                span.style.fontSize = size + 'px';
                while (font.firstChild) span.appendChild(font.firstChild);
                font.replaceWith(span);
            });
        });

        if (fontSize) fontSize.value = String(size);
        updateCounts();
        updateToolbarState();
        return changed;
    }

    function stepFontSize(delta) {
        if (!savedRange || !selectionInsideEditor || savedRange.collapsed) return;
        const options = Array.from(fontSize?.options || []).map(o => Number(o.value));
        if (!options.length) return;
        const current = selectedOrCurrentSize();
        let index = options.indexOf(current);
        if (index < 0) index = options.reduce((best, value, i) =>
            Math.abs(value - current) < Math.abs(options[best] - current) ? i : best, 0);
        const next = options[Math.max(0, Math.min(options.length - 1, index + delta))];
        setFontSize(next);
    }

    function updateCounts() {
        const text = (editor.innerText || '').replace(/\u200b/g, ' ').trim();
        const words = text ? text.split(/\s+/).length : 0;
        if (wordCount) wordCount.textContent = words + (words === 1 ? ' word' : ' words');
        if (charCount) charCount.textContent = text.length + (text.length === 1 ? ' character' : ' characters');
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function markdownToHtml(markdown) {
        let text = String(markdown || '').replace(/\r\n?/g, '\n');
        if (!text.trim()) return '<p><br></p>';

        const blocks = [];
        let inCode = false;
        let code = [];
        let language = '';
        text.split('\n').forEach(line => {
            const fence = line.match(/^```\s*(.*)$/);
            if (fence) {
                if (!inCode) { inCode = true; language = fence[1] || ''; code = []; }
                else { blocks.push('<pre><code data-language="' + escapeHtml(language) + '">' + escapeHtml(code.join('\n')) + '</code></pre>'); inCode = false; }
                return;
            }
            if (inCode) { code.push(line); return; }
            blocks.push(line);
        });
        if (inCode) blocks.push('<pre><code>' + escapeHtml(code.join('\n')) + '</code></pre>');

        let html = '';
        let list = null;
        const closeList = () => { if (list) { html += '</' + list + '>'; list = null; } };
        const inline = value => {
            let s = escapeHtml(value);
            s = s.replace(/`([^`]+)`/g, '<code>$1</code>');
            s = s.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
            s = s.replace(/__([^_]+)__/g, '<strong>$1</strong>');
            s = s.replace(/\*([^*]+)\*/g, '<em>$1</em>');
            s = s.replace(/_([^_]+)_/g, '<em>$1</em>');
            s = s.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');
            return s;
        };

        blocks.forEach(line => {
            if (line.startsWith('<pre><code')) { closeList(); html += line; return; }
            if (!line.trim()) { closeList(); return; }
            let m;
            if ((m = line.match(/^###\s+(.+)$/))) { closeList(); html += '<h4>' + inline(m[1]) + '</h4>'; return; }
            if ((m = line.match(/^##\s+(.+)$/))) { closeList(); html += '<h3>' + inline(m[1]) + '</h3>'; return; }
            if ((m = line.match(/^#\s+(.+)$/))) { closeList(); html += '<h2>' + inline(m[1]) + '</h2>'; return; }
            if ((m = line.match(/^>\s?(.*)$/))) { closeList(); html += '<blockquote>' + inline(m[1]) + '</blockquote>'; return; }
            if ((m = line.match(/^[-*]\s+(.+)$/))) {
                if (list !== 'ul') { closeList(); html += '<ul>'; list = 'ul'; }
                html += '<li>' + inline(m[1]) + '</li>'; return;
            }
            if ((m = line.match(/^\d+\.\s+(.+)$/))) {
                if (list !== 'ol') { closeList(); html += '<ol>'; list = 'ol'; }
                html += '<li>' + inline(m[1]) + '</li>'; return;
            }
            closeList();
            html += '<p>' + inline(line) + '</p>';
        });
        closeList();
        return html || '<p><br></p>';
    }

    function domToMarkdown(root) {
        function inline(node) {
            if (node.nodeType === Node.TEXT_NODE) return node.nodeValue.replace(/\u200b/g, '');
            if (node.nodeType !== Node.ELEMENT_NODE) return '';
            const tag = node.tagName.toLowerCase();
            const content = Array.from(node.childNodes).map(inline).join('');
            if (tag === 'strong' || tag === 'b') return '**' + content + '**';
            if (tag === 'em' || tag === 'i') return '*' + content + '*';
            if (tag === 'u') return '<u>' + content + '</u>';
            if (tag === 's' || tag === 'strike') return '~~' + content + '~~';
            if (tag === 'code' && node.parentElement?.tagName.toLowerCase() !== 'pre') return '`' + content + '`';
            if (tag === 'a') return '[' + content + '](' + (node.getAttribute('href') || '') + ')';
            return content;
        }
        function block(node) {
            if (node.nodeType === Node.TEXT_NODE) return node.nodeValue.trim();
            if (node.nodeType !== Node.ELEMENT_NODE) return '';
            const tag = node.tagName.toLowerCase();
            if (tag === 'pre') return '```\n' + node.innerText.replace(/\u200b/g, '') + '\n```';
            if (tag === 'h2') return '# ' + inline(node);
            if (tag === 'h3') return '## ' + inline(node);
            if (tag === 'h4') return '### ' + inline(node);
            if (tag === 'blockquote') return '> ' + inline(node);
            if (tag === 'ul' || tag === 'ol') {
                const ordered = tag === 'ol';
                return Array.from(node.children).map((li, i) => (ordered ? (i + 1) + '. ' : '- ') + inline(li)).join('\n');
            }
            if (tag === 'li') return inline(node);
            return inline(node);
        }
        return Array.from(root.childNodes).map(block).filter(Boolean).join('\n\n').replace(/\n{3,}/g, '\n\n').trim();
    }

    editor.innerHTML = markdownToHtml(source.value);
    updateCounts();

    editor.addEventListener('input', updateCounts);
    editor.addEventListener('mouseup', function () {
        saveSelection();
        updateToolbarState();
    });
    editor.addEventListener('keyup', function (event) {
        updateCounts();
        if (!(event.ctrlKey || event.metaKey)) saveSelection();
        updateToolbarState();
    });
    editor.addEventListener('click', updateToolbarState);
    editor.addEventListener('focus', updateToolbarState);

    document.addEventListener('selectionchange', function () {
        const range = getLiveSelection();
        if (range) {
            savedRange = range.cloneRange();
            selectionInsideEditor = true;
            updateToolbarState();
        } else if (document.activeElement !== editor && !toolbar.contains(document.activeElement)) {
            clearSavedSelection();
            updateToolbarState();
        }
    });

    // Preserve the editor selection when the mouse moves from the editor to
    // the toolbar. Buttons must not steal focus before their command runs.
    toolbar.addEventListener('pointerdown', function (event) {
        if (getLiveSelection()) saveSelection();
        updateToolbarState();
        if (event.target.closest('button')) event.preventDefault();
    });

    toolbar.addEventListener('click', function (event) {
        const button = event.target.closest('button');
        if (!button) return;
        if (button.dataset.sizeStep) return;

        if (button.dataset.insertCode) {
            runWithSelection(() => {
                if (button.dataset.insertCode === 'inline') {
                    document.execCommand('insertHTML', false, '<code>code</code>');
                } else {
                    document.execCommand('formatBlock', false, 'pre');
                }
            });
            updateCounts();
            return;
        }

        const command = button.dataset.command;
        if (!command) return;

        if (command === 'createLink') {
            if (!restoreSelection(true)) return;
            const url = window.prompt('Enter URL', 'https://');
            if (url) document.execCommand('createLink', false, url);
            saveSelection();
        } else if (command === 'formatBlock') {
            runWithSelection(() => document.execCommand('formatBlock', false, button.dataset.value));
        } else if (command === 'undo' || command === 'redo') {
            editor.focus({ preventScroll: true });
            document.execCommand(command, false, null);
            saveSelection();
        } else {
            runWithSelection(() => document.execCommand(command, false, null));
        }
        updateCounts();
        updateToolbarState();
    });

    styleSelect?.addEventListener('change', function () {
        runWithSelection(() => document.execCommand('formatBlock', false, this.value));
        updateToolbarState();
    });

    fontFamily?.addEventListener('change', function () {
        runWithSelection(() => document.execCommand('fontName', false, this.value));
        updateToolbarState();
    });

    fontSize?.addEventListener('change', function () {
        setFontSize(this.value);
    });

    sizeStepButtons.forEach(button => {
        button.addEventListener('click', function () {
            stepFontSize(Number(this.dataset.sizeStep || 0));
        });
    });

    // Browser zoom normally owns Ctrl+Plus/Ctrl+Minus. Capture the shortcut
    // at document level and handle it only when a real text selection is in
    // this editor. This prevents the whole website from zooming.
    document.addEventListener('keydown', function (event) {
        if (!(event.ctrlKey || event.metaKey) || event.altKey) return;
        const range = getLiveSelection() || savedRange;
        if (!range || range.collapsed || !isRangeInsideEditor(range)) return;

        const plus = event.code === 'Equal' || event.code === 'NumpadAdd' || event.key === '+';
        const minus = event.code === 'Minus' || event.code === 'NumpadSubtract' || event.key === '-';
        if (!plus && !minus) return;

        event.preventDefault();
        event.stopPropagation();
        if (event.stopImmediatePropagation) event.stopImmediatePropagation();
        saveSelection();
        stepFontSize(plus ? 1 : -1);
        updateToolbarState();
    }, true);

    textColor?.addEventListener('input', function () {
        runWithSelection(() => document.execCommand('foreColor', false, this.value));
        updateToolbarState();
    });

    document.querySelector('.doc-color-reset')?.addEventListener('click', function () {
        runWithSelection(() => document.execCommand('foreColor', false, this.dataset.value || '#25362f'));
        if (textColor) textColor.value = this.dataset.value || '#25362f';
    });

    form.addEventListener('submit', function () {
        source.value = domToMarkdown(editor);
    });

    const releaseSelect = document.getElementById('release_id');
    const preview = document.getElementById('resolved-version');
    const note = document.getElementById('resolved-version-note');
    const hidden = document.getElementById('version');
    if (releaseSelect && preview && note && hidden) {
        function updateVersion() {
            const option = releaseSelect.options[releaseSelect.selectedIndex];
            const version = option?.dataset?.version || 'All versions';
            preview.textContent = version === 'All versions' ? version : 'v' + version;
            note.textContent = version === 'All versions'
                ? 'This page is shared across releases.'
                : 'This page is scoped to one release.';
            hidden.value = version;
        }
        releaseSelect.addEventListener('change', updateVersion);
        updateVersion();
    }

    updateToolbarState();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/projects/others/RozeHub/resources/views/admin/documentation/page-form.blade.php ENDPATH**/ ?>