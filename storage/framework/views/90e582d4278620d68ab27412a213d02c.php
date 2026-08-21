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

    if (!form || !editor || !source || !toolbar) return;

    let savedRange = null;

    function escapeHtml(value) {
        return value.replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    }

    function markdownInlineToHtml(value) {
        const fontTokens = [];
        value = value.replace(/\[\[font=([^;\]]*);color=(#[0-9a-fA-F]{6})?(?:;size=(\d+(?:\.\d+)?))?\]\]([\s\S]*?)\[\[\/font\]\]/g, function (_, face, color, size, content) {
            const safeFace = (face || '').replace(/[^a-zA-Z0-9 ,\-]/g, '').trim();
            const safeColor = color || '';
            const token = '___ROZE_FONT_' + fontTokens.length + '___';
            fontTokens.push({token, face: safeFace, color: safeColor, size: size || '', content});
            return token;
        });
        let html = escapeHtml(value);
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__([^_]+)__/g, '<strong>$1</strong>');
        html = html.replace(/~~([^~]+)~~/g, '<s>$1</s>');
        html = html.replace(/\+\+([^+]+)\+\+/g, '<u>$1</u>');
        html = html.replace(/==([^=]+)==/g, '<mark>$1</mark>');
        html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');
        html = html.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2">$1</a>');
        fontTokens.forEach(({token, face, color, content}) => {
            const inner = markdownInlineToHtml(content);
            const styles = [];
            if (face) styles.push('font-family:' + face.replace(/[^a-zA-Z0-9 ,\-]/g, '') + ';');
            if (color) styles.push('color:' + color + ';');
            if (size) styles.push('font-size:' + Number(size) + 'px;');
            html = html.replace(token, '<span style="' + styles.join('') + '">' + inner + '</span>');
        });
        return html;
    }

    function markdownToHtml(markdown) {
        const lines = (markdown || '').replace(/\r/g, '').split('\n');
        let html = '', inCode = false, code = [], lang = '', list = null, inQuote = false;
        const closeList = () => { if (list) { html += '</' + list + '>'; list = null; } };
        const closeQuote = () => { if (inQuote) { html += '</blockquote>'; inQuote = false; } };
        lines.forEach(line => {
            if (/^```/.test(line.trim())) {
                if (!inCode) { closeList(); closeQuote(); inCode = true; lang = line.trim().slice(3).trim(); code = []; }
                else { html += '<pre><code' + (lang ? ' data-lang="' + escapeHtml(lang) + '"' : '') + '>' + escapeHtml(code.join('\n')) + '</code></pre>'; inCode = false; }
                return;
            }
            if (inCode) { code.push(line); return; }
            const t = line.trim();
            if (!t) { closeList(); closeQuote(); return; }
            let m;
            if ((m = t.match(/^(#{1,4})\s+(.+)$/))) { closeList(); closeQuote(); const level = Math.min(4, m[1].length + 1); html += '<h' + level + '>' + markdownInlineToHtml(m[2]) + '</h' + level + '>'; return; }
            if (t.startsWith('> ')) { closeList(); if (!inQuote) { html += '<blockquote>'; inQuote = true; } html += '<p>' + markdownInlineToHtml(t.slice(2)) + '</p>'; return; }
            if ((m = t.match(/^[-*]\s+(.+)$/))) { closeQuote(); if (list !== 'ul') { closeList(); html += '<ul>'; list = 'ul'; } html += '<li>' + markdownInlineToHtml(m[1]) + '</li>'; return; }
            if ((m = t.match(/^\d+\.\s+(.+)$/))) { closeQuote(); if (list !== 'ol') { closeList(); html += '<ol>'; list = 'ol'; } html += '<li>' + markdownInlineToHtml(m[1]) + '</li>'; return; }
            closeList(); closeQuote(); html += '<p>' + markdownInlineToHtml(t) + '</p>';
        });
        if (inCode) html += '<pre><code>' + escapeHtml(code.join('\n')) + '</code></pre>';
        closeList(); closeQuote();
        return html || '<p><br></p>';
    }

    function inlineMarkdownFromNode(node) {
        if (node.nodeType === Node.TEXT_NODE) return node.nodeValue.replace(/\u00a0/g, ' ');
        if (node.nodeType !== Node.ELEMENT_NODE) return '';
        const tag = node.tagName.toLowerCase();
        const content = Array.from(node.childNodes).map(inlineMarkdownFromNode).join('');
        if (tag === 'strong' || tag === 'b') return '**' + content + '**';
        if (tag === 'em' || tag === 'i') return '*' + content + '*';
        if (tag === 'u') return '++' + content + '++';
        if (tag === 's' || tag === 'strike') return '~~' + content + '~~';
        if (tag === 'mark') return '==' + content + '==';
        if (tag === 'code' && node.parentElement?.tagName.toLowerCase() !== 'pre') return '`' + content.replace(/`/g, '\\`') + '`';
        if (tag === 'a') { const href = node.getAttribute('href') || ''; return href ? '[' + content + '](' + href + ')' : content; }
        if (tag === 'font') {
            const face = (node.getAttribute('face') || '').replace(/[^a-zA-Z0-9 ,\-]/g, '').trim();
            const color = (node.getAttribute('color') || '').match(/^#[0-9a-fA-F]{6}$/)?.[0] || '';
            const size = (node.getAttribute('size') || '').match(/^\d+(?:\.\d+)?$/)?.[0] || '';
            if (face || color || size) return '[[font=' + face + ';color=' + color + ';size=' + size + ']]' + content + '[[/font]]';
        }
        if (tag === 'span') {
            const style = node.getAttribute('style') || '';
            const face = (style.match(/font-family\s*:\s*([^;]+)/i)?.[1] || '').replace(/[\"']/g, '').replace(/[^a-zA-Z0-9 ,\-]/g, '').trim();
            const color = (style.match(/(?:^|;)\s*color\s*:\s*(#[0-9a-fA-F]{6})/i)?.[1] || '');
            const size = (style.match(/(?:^|;)\s*font-size\s*:\s*([0-9.]+)px/i)?.[1] || '');
            if (face || color || size) return '[[font=' + face + ';color=' + color + ';size=' + size + ']]' + content + '[[/font]]';
        }
        if (tag === 'br') return '\n';
        return content;
    }

    function domToMarkdown(root) {
        const blocks = [];
        function walk(node) {
            if (node.nodeType !== Node.ELEMENT_NODE) return;
            const tag = node.tagName.toLowerCase();
            if (/^h[1-4]$/.test(tag)) { blocks.push('#'.repeat(Math.max(1, parseInt(tag[1], 10) - 1)) + ' ' + inlineMarkdownFromNode(node).trim()); return; }
            if (tag === 'p') { const value = inlineMarkdownFromNode(node).trim(); if (value) blocks.push(value); return; }
            if (tag === 'blockquote') { const value = Array.from(node.querySelectorAll(':scope > p')).map(p => inlineMarkdownFromNode(p).trim()).filter(Boolean); blocks.push(value.map(v => '> ' + v).join('\n')); return; }
            if (tag === 'pre') { const code = node.querySelector('code'); blocks.push('```' + (code?.dataset?.lang || '') + '\n' + (code?.textContent || '') + '\n```'); return; }
            if (tag === 'ul' || tag === 'ol') {
                const ordered = tag === 'ol'; let i = 1;
                blocks.push(Array.from(node.children).filter(el => el.tagName.toLowerCase() === 'li').map(li => (ordered ? (i++) + '. ' : '- ') + inlineMarkdownFromNode(li).trim()).join('\n')); return;
            }
            if (tag === 'div') { const value = inlineMarkdownFromNode(node).trim(); if (value) blocks.push(value); return; }
            Array.from(node.children).forEach(walk);
        }
        Array.from(root.children).forEach(walk);
        return blocks.join('\n\n').replace(/\n{3,}/g, '\n\n').trim();
    }

    function updateCounts() {
        const text = editor.innerText.replace(/\s+/g, ' ').trim();
        const words = text ? text.split(' ').length : 0;
        wordCount.textContent = words + (words === 1 ? ' word' : ' words');
        charCount.textContent = text.length + ' characters';
    }

    function saveSelection() {
        const selection = window.getSelection();
        if (selection && selection.rangeCount) savedRange = selection.getRangeAt(0).cloneRange();
    }
    function restoreSelection() {
        editor.focus();
        if (savedRange) {
            const selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(savedRange);
        }
    }

    function normalizeSize(value) {
        const allowed = Array.from(fontSize?.options || []).map(option => Number(option.value));
        if (!allowed.length) return 16;
        const numeric = Number(value) || 16;
        return allowed.reduce((closest, candidate) =>
            Math.abs(candidate - numeric) < Math.abs(closest - numeric) ? candidate : closest,
            allowed[0]
        );
    }

    function setFontSize(value) {
        const size = normalizeSize(value);
        restoreSelection();
        document.execCommand('fontSize', false, '7');
        editor.querySelectorAll('font[size="7"]').forEach(font => {
            const span = document.createElement('span');
            span.style.fontSize = size + 'px';
            while (font.firstChild) span.appendChild(font.firstChild);
            font.replaceWith(span);
        });
        if (fontSize) fontSize.value = String(size);
        updateCounts();
        saveSelection();
    }

    function selectedOrCurrentSize() {
        const selection = window.getSelection();
        let node = selection && selection.rangeCount ? selection.anchorNode : null;
        if (node?.nodeType === Node.TEXT_NODE) node = node.parentElement;
        const styled = node?.closest?.('[style*="font-size"], font[size]');
        const match = styled?.getAttribute('style')?.match(/font-size\s*:\s*([0-9.]+)px/i);
        if (match) return normalizeSize(match[1]);
        return normalizeSize(fontSize?.value || 16);
    }

    editor.innerHTML = markdownToHtml(source.value);
    if (styleSelect) styleSelect.value = 'p';
    updateCounts();
    editor.addEventListener('keyup', updateCounts);
    editor.addEventListener('input', updateCounts);
    editor.addEventListener('mouseup', saveSelection);
    editor.addEventListener('keyup', saveSelection);
    editor.addEventListener('blur', saveSelection);

    toolbar.addEventListener('mousedown', function (event) {
        const button = event.target.closest('button');
        if (button) event.preventDefault();
    });

    toolbar.addEventListener('click', function (event) {
        const button = event.target.closest('button');
        const select = event.target.closest('select');
        if (select) return;
        if (!button) return;
        restoreSelection();

        if (button.dataset.insertCode) {
            if (button.dataset.insertCode === 'inline') {
                document.execCommand('formatBlock', false, 'p');
                document.execCommand('insertHTML', false, '<code>code</code>');
            } else {
                document.execCommand('formatBlock', false, 'pre');
            }
            updateCounts(); saveSelection(); return;
        }

        const command = button.dataset.command;
        if (command === 'createLink') {
            const url = window.prompt('Enter URL', 'https://');
            if (url) document.execCommand('createLink', false, url);
        } else if (command === 'formatBlock') {
            document.execCommand('formatBlock', false, button.dataset.value);
        } else if (command) {
            document.execCommand(command, false, null);
        }
        updateCounts(); saveSelection();
    });

    styleSelect?.addEventListener('change', function () {
        restoreSelection();
        document.execCommand('formatBlock', false, this.value);
        updateCounts(); saveSelection();
    });

    fontFamily?.addEventListener('change', function () {
        restoreSelection();
        document.execCommand('fontName', false, this.value);
        updateCounts(); saveSelection();
    });

    fontSize?.addEventListener('change', function () {
        setFontSize(this.value);
    });

    sizeStepButtons.forEach(button => {
        button.addEventListener('click', function () {
            const current = selectedOrCurrentSize();
            const options = Array.from(fontSize?.options || []).map(option => Number(option.value));
            const index = options.indexOf(current);
            const nextIndex = Math.max(0, Math.min(options.length - 1, index + Number(this.dataset.sizeStep || 0)));
            setFontSize(options[nextIndex]);
        });
    });

    editor.addEventListener('keydown', function (event) {
        if (!(event.ctrlKey || event.metaKey)) return;
        const isPlus = event.key === '+' || event.key === '=';
        const isMinus = event.key === '-' || event.key === '_';
        if (!isPlus && !isMinus) return;
        event.preventDefault();
        event.stopPropagation();
        const current = selectedOrCurrentSize();
        const options = Array.from(fontSize?.options || []).map(option => Number(option.value));
        const index = options.indexOf(current);
        const delta = isPlus ? 1 : -1;
        const nextIndex = Math.max(0, Math.min(options.length - 1, index + delta));
        setFontSize(options[nextIndex]);
    });

    textColor?.addEventListener('input', function () {
        restoreSelection();
        document.execCommand('foreColor', false, this.value);
        updateCounts(); saveSelection();
    });

    document.querySelector('.doc-color-reset')?.addEventListener('click', function () {
        restoreSelection();
        document.execCommand('foreColor', false, this.dataset.value || '#25362f');
        if (textColor) textColor.value = this.dataset.value || '#25362f';
        updateCounts(); saveSelection();
    });

    form.addEventListener('submit', function () {
        source.value = domToMarkdown(editor);
        if (!source.value.trim()) {
            source.value = '';
            editor.focus();
        }
    });

    const releaseSelect = document.getElementById('release_id');
    const preview = document.getElementById('resolved-version');
    const note = document.getElementById('resolved-version-note');
    const hidden = document.getElementById('version');
    if (releaseSelect) {
        function updateVersion() {
            const option = releaseSelect.options[releaseSelect.selectedIndex];
            const version = option?.dataset?.version || 'All versions';
            preview.textContent = version === 'All versions' ? version : 'v' + version;
            note.textContent = version === 'All versions' ? 'This page is shared across releases.' : 'This page is scoped to one release.';
            hidden.value = version;
        }
        releaseSelect.addEventListener('change', updateVersion);
        updateVersion();
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/Downloads/RozeHub-rich-documentation-editor-size-controls/RozeHub-final/resources/views/admin/documentation/page-form.blade.php ENDPATH**/ ?>