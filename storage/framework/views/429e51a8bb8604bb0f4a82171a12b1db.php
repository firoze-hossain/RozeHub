<?php $__env->startSection('content'); ?>
<div class="admin-page-head">
    <div>
        <p class="admin-kicker">Plugins & extensions</p>
        <h2>Desktop Marketplace</h2>
        <p class="muted">Manage installable capabilities for Lumina, DBNavigator, and future RozeHub desktop applications.</p>
    </div>
    <a class="admin-button primary" href="<?php echo e(route('admin.marketplace.create')); ?>">＋ New marketplace item</a>
</div>

<div class="market-filter">
    <form method="GET">
        <input name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name, ID, vendor…">
        <select name="project">
            <option value="">All applications</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <option value="<?php echo e($project->id); ?>" <?php if(request('project') == $project->id): echo 'selected'; endif; ?>><?php echo e($project->name); ?></option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </select>
        <select name="type">
            <option value="">Plugins + extensions</option>
            <option value="plugin" <?php if(request('type') === 'plugin'): echo 'selected'; endif; ?>>Plugins</option>
            <option value="extension" <?php if(request('type') === 'extension'): echo 'selected'; endif; ?>>Extensions</option>
        </select>
        <button class="admin-button" type="submit">Filter</button>
    </form>
</div>

<div class="market-grid">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <article class="market-card">
        <div class="market-card-top">
            <div class="market-icon"><?php echo e($item->icon_path ? '' : ($item->item_type === 'extension' ? '◈' : '◆')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->icon_path): ?><img src="<?php echo e(asset($item->icon_path)); ?>" alt=""><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="market-badges">
                <span class="market-badge <?php echo e($item->item_type); ?>"><?php echo e(ucfirst($item->item_type)); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->is_official): ?><span class="market-badge official">Official</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->is_verified): ?><span class="market-badge verified">Verified</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <h3><?php echo e($item->name); ?></h3>
        <p class="market-meta"><?php echo e($item->project->name); ?> · <?php echo e($item->item_id); ?></p>
        <p><?php echo e($item->summary ?: 'No summary yet.'); ?></p>
        <div class="market-card-footer">
            <span><?php echo e(number_format($item->downloads_count)); ?> downloads</span>
            <div class="market-actions">
                <a class="admin-button small" href="<?php echo e(route('admin.marketplace.releases.index', $item)); ?>">Releases</a>
                <a class="admin-button small" href="<?php echo e(route('admin.marketplace.edit', $item)); ?>">Edit</a>
                <form method="POST" action="<?php echo e(route('admin.marketplace.destroy', $item)); ?>" onsubmit="return confirm('Delete this marketplace item and all packages?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="admin-button small danger">Delete</button>
                </form>
            </div>
        </div>
    </article>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div class="admin-empty">No marketplace items yet. Create the first plugin or extension.</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php echo e($items->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', ['heading' => 'Marketplace'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/Downloads/RozeHub-Ecosystem-Extensions-Full-Updated/RozeHub-Ecosystem-Extensions-Updated/resources/views/admin/marketplace/index.blade.php ENDPATH**/ ?>