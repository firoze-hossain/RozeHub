<?php $__env->startSection('content'); ?>
<div class="admin-page-head admin-doc-project-head">
    <div class="admin-doc-title-wrap">
        <div class="admin-doc-project-mark"><?php echo e($project->icon); ?></div>
        <div>
            <span><?php echo e(strtoupper($project->name)); ?> DOCUMENTATION</span>
            <h2>Documentation workspace</h2>
            <p>Build a clean, version-aware knowledge base for <?php echo e($project->name); ?>.</p>
        </div>
    </div>
    <div class="admin-head-actions">
        <a class="admin-secondary" href="<?php echo e(route('docs.project',$project)); ?>">Public docs ↗</a>
        <a class="admin-primary" href="<?php echo e(route('admin.documentation.pages.create',$project)); ?>">+ New page</a>
    </div>
</div>

<div class="admin-doc-release-strip">
    <div>
        <span>DOCUMENTATION VERSIONING</span>
        <strong>Assign each article to a release when the content is version-specific.</strong>
        <p>Use <b>All versions</b> for stable concepts. Select a release for installation steps, APIs, commands, architecture changes, or release notes that belong to one software version.</p>
    </div>
    <div class="admin-doc-release-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $project->releases->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <span class="admin-doc-release-pill"><b>v<?php echo e($release->version); ?></b><small><?php echo e(strtoupper($release->channel ?: 'release')); ?></small></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <span class="admin-doc-release-pill muted">No releases yet</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<div class="admin-doc-stats">
    <div><span>SECTIONS</span><strong><?php echo e($project->documentationSections->count()); ?></strong><small>navigation groups</small></div>
    <div><span>PAGES</span><strong><?php echo e($project->documentationSections->sum('pages_count')); ?></strong><small>documentation articles</small></div>
    <div><span>RELEASES</span><strong><?php echo e($project->releases->count()); ?></strong><small>available versions</small></div>
    <div><span>PUBLIC SITE</span><strong>LIVE</strong><small>version-aware docs</small></div>
</div>

<div class="admin-doc-workspace">
    <section class="admin-card admin-doc-sections">
        <div class="admin-card-heading">
            <div><span>DOCUMENTATION NAVIGATION</span><h3>Sections & pages</h3></div>
            <small><?php echo e($project->documentationSections->count()); ?> sections</small>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $project->documentationSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <details class="admin-doc-section" open>
                <summary>
                    <span class="section-grip"><?php echo e($section->icon); ?></span>
                    <div><strong><?php echo e($section->title); ?></strong><small><?php echo e($section->description ?: 'Documentation section'); ?></small></div>
                    <em><?php echo e($section->pages_count); ?> pages</em>
                    <span class="admin-doc-chevron">⌄</span>
                </summary>
                <div class="admin-doc-section-body">
                    <div class="admin-doc-section-toolbar">
                        <form method="POST" action="<?php echo e(route('admin.documentation.sections.update',$section)); ?>" class="admin-doc-inline-form">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <label>Section title<input name="title" value="<?php echo e($section->title); ?>" required></label>
                            <label>Icon<input name="icon" value="<?php echo e($section->icon); ?>" maxlength="20"></label>
                            <label>Order<input type="number" name="sort_order" value="<?php echo e($section->sort_order); ?>" min="0"></label>
                            <label class="wide">Description<input name="description" value="<?php echo e($section->description); ?>"></label>
                            <button class="admin-secondary" type="submit">Save section</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('admin.documentation.sections.destroy',$section)); ?>" onsubmit="return confirm('Delete this section? Pages will become unassigned.')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="admin-danger" type="submit">Delete section</button>
                        </form>
                    </div>

                    <div class="admin-doc-page-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $section->pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="admin-doc-page-row">
                                <div class="admin-doc-page-main">
                                    <div class="admin-doc-page-tags">
                                        <span class="docs-kind"><?php echo e(strtoupper(str_replace('-',' ',$page->kind))); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page->release): ?>
                                            <span class="doc-version-chip">v<?php echo e($page->release->version); ?></span>
                                        <?php else: ?>
                                            <span class="doc-version-chip general">ALL VERSIONS</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span class="admin-doc-status <?php echo e($page->is_published ? 'published' : 'draft'); ?>"><?php echo e($page->is_published ? 'Published' : 'Draft'); ?></span>
                                    </div>
                                    <strong><?php echo e($page->title); ?></strong>
                                    <small><?php echo e($page->summary ?: 'No summary added.'); ?></small>
                                </div>
                                <div class="row-actions">
                                    <a href="<?php echo e(route('admin.documentation.pages.edit',$page)); ?>">Edit</a>
                                    <form method="POST" action="<?php echo e(route('admin.documentation.pages.toggle',$page)); ?>"><?php echo csrf_field(); ?><button><?php echo e($page->is_published ? 'Unpublish' : 'Publish'); ?></button></form>
                                    <form method="POST" action="<?php echo e(route('admin.documentation.pages.destroy',$page)); ?>" onsubmit="return confirm('Delete this page?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button>Delete</button></form>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="admin-empty">No pages in this section yet. Create the first article for this part of the documentation.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </details>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="admin-doc-empty-state">
                <div>⌘</div><h3>No documentation sections yet</h3><p>Create a navigation section such as Overview, Installation, Reference, Architecture, or Troubleshooting.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>

    <aside class="admin-doc-create-section">
        <div class="admin-card admin-doc-section-builder">
            <div class="admin-doc-builder-head">
                <div class="admin-doc-builder-icon">＋</div>
                <div>
                    <span class="admin-card-kicker">STRUCTURE</span>
                    <h3>Add documentation section</h3>
                    <p>Sections become the navigation groups visitors use to explore this documentation.</p>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('admin.documentation.sections.store',$project)); ?>" class="admin-doc-section-form">
                <?php echo csrf_field(); ?>

                <label class="admin-doc-field">
                    <span>Section title <i>Required</i></span>
                    <div class="admin-doc-input-wrap">
                        <span class="admin-doc-input-icon">▤</span>
                        <input name="title" maxlength="160" placeholder="e.g. API Reference" required>
                        <small data-count-for="title">0 / 160</small>
                    </div>
                </label>

                <div class="admin-doc-form-two">
                    <label class="admin-doc-field">
                        <span>Icon <i>Optional</i></span>
                        <div class="admin-doc-input-wrap compact">
                            <span class="admin-doc-input-icon">◈</span>
                            <input name="icon" maxlength="20" placeholder="⌘">
                        </div>
                    </label>

                    <label class="admin-doc-field">
                        <span>Order</span>
                        <div class="admin-doc-input-wrap compact">
                            <input type="number" name="sort_order" value="99" min="0">
                            <span class="admin-doc-number-arrows">↕</span>
                        </div>
                    </label>
                </div>

                <label class="admin-doc-field">
                    <span>Description <i>Optional</i></span>
                    <div class="admin-doc-input-wrap textarea-wrap">
                        <textarea name="description" maxlength="500" rows="3" placeholder="What belongs in this section?"></textarea>
                        <small data-count-for="description">0 / 500</small>
                    </div>
                </label>

                <button class="admin-primary admin-doc-add-button" type="submit">
                    <span>＋</span> Add section
                </button>
            </form>
        </div>

        <div class="admin-doc-tip admin-doc-version-tip">
            <div class="admin-doc-tip-icon">✦</div>
            <div>
                <strong>Versioning tip</strong>
                <p>For NOVAOS, Roze, and StratosDB, use release-specific pages for compatibility-sensitive documentation. Keep conceptual pages unassigned when they apply across releases.</p>
            </div>
        </div>
    </aside>

    <script>
        (() => {
            const bindCounter = (field, counter) => {
                if (!field || !counter) return;
                const update = () => counter.textContent = `${field.value.length} / ${field.maxLength}`;
                field.addEventListener('input', update);
                update();
            };
            bindCounter(document.querySelector('[name="title"]'), document.querySelector('[data-count-for="title"]'));
            bindCounter(document.querySelector('[name="description"]'), document.querySelector('[data-count-for="description"]'));
        })();
    </script>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/Documents/RozeHub-github-contributions-integrated-fixed/resources/views/admin/documentation/project.blade.php ENDPATH**/ ?>