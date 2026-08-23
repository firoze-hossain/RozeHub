<?php $__env->startSection('content'); ?>
<div class="admin-page-head">
    <div>
        <p class="admin-kicker"><?php echo e(ucfirst($item->item_type)); ?> · <?php echo e($item->project->name); ?></p>
        <h2><?php echo e($item->name); ?></h2>
        <p class="muted"><?php echo e($item->item_id); ?> · Manage compatibility, packages and channels.</p>
    </div>
    <div class="market-actions">
        <a class="admin-button" href="<?php echo e(route('admin.marketplace.edit', $item)); ?>">Edit item</a>
        <a class="admin-button primary" href="<?php echo e(route('admin.marketplace.releases.create', $item)); ?>">＋ New release</a>
    </div>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
<thead><tr><th>Version</th><th>Compatibility</th><th>Target</th><th>Channel</th><th>Package</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<tr>
    <td><strong><?php echo e($release->version); ?></strong><br><small><?php echo e($release->package_type); ?></small></td>
    <td><?php echo e($release->minimum_app_version ?: 'Any'); ?> → <?php echo e($release->maximum_app_version ?: 'Any'); ?></td>
    <td><?php echo e($release->platform); ?> · <?php echo e($release->architecture); ?></td>
    <td><?php echo e($release->channel); ?></td>
    <td><?php echo e($release->file_name ?: '—'); ?><br><small><?php echo e($release->file_size ? number_format($release->file_size / 1048576, 1).' MB' : '—'); ?></small></td>
    <td>
        <span class="market-badge <?php echo e($release->is_published ? 'verified' : 'muted-badge'); ?>"><?php echo e($release->is_published ? 'Published' : 'Draft'); ?></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($release->is_mandatory): ?><span class="market-badge danger-badge">Mandatory</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </td>
    <td>
        <div class="market-actions">
            <a class="admin-button small" href="<?php echo e(route('admin.marketplace.releases.edit', $release)); ?>">Edit</a>
            <form method="POST" action="<?php echo e(route('admin.marketplace.releases.toggle', $release)); ?>"><?php echo csrf_field(); ?><button class="admin-button small"><?php echo e($release->is_published ? 'Unpublish' : 'Publish'); ?></button></form>
            <form method="POST" action="<?php echo e(route('admin.marketplace.releases.destroy', $release)); ?>" onsubmit="return confirm('Delete this release and package?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="admin-button small danger">Delete</button></form>
        </div>
    </td>
</tr>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<tr><td colspan="7" class="admin-empty">No releases for this marketplace item.</td></tr>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</tbody>
</table>
</div>

<?php echo e($releases->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', ['heading' => $item->name.' releases'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/projects/others/RozeHub/resources/views/admin/marketplace/releases/index.blade.php ENDPATH**/ ?>