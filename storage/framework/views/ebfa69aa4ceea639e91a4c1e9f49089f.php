<?php $__env->startSection('content'); ?>
<div class="novaos-admin-hero">
    <div class="novaos-admin-hero-copy">
        <span class="novaos-admin-kicker">INDEPENDENT OPERATING SYSTEM</span>
        <h2>NOVAOS Release Center</h2>
        <p>Manage operating-system images independently from application releases. Publish Stable, Beta and Nightly builds with architecture, build metadata and automatically generated SHA-256 checksums.</p>
        <div class="novaos-admin-actions"><a class="admin-primary" href="<?php echo e(route('admin.novaos.releases.create')); ?>">+ Create NOVAOS release</a><a class="admin-secondary" href="<?php echo e(route('admin.novaos.releases.index')); ?>">Manage releases →</a></div>
    </div>
    <img src="<?php echo e(asset('images/projects/novaos.png')); ?>" alt="NOVAOS">
</div>
<div class="metric-grid novaos-metrics">
    <div class="metric"><span>Latest Stable</span><strong><?php echo e($latestStable?->version ?? '—'); ?></strong><small><?php echo e($latestStable?->codename ?: 'No stable build published'); ?></small></div>
    <div class="metric"><span>Published Builds</span><strong><?php echo e($publishedCount); ?></strong><small><?php echo e($stableCount); ?> stable builds</small></div>
    <div class="metric"><span>Downloads</span><strong><?php echo e(number_format($downloadCount)); ?></strong><small>all NOVAOS builds</small></div>
    <div class="metric"><span>Release Channels</span><strong>3</strong><small>Stable · Beta · Nightly</small></div>
</div>
<div class="admin-card novaos-admin-recent">
    <div class="card-heading"><div><span>RELEASE ARCHIVE</span><h2>Recent NOVAOS builds</h2></div><a href="<?php echo e(route('admin.novaos.releases.index')); ?>">View all →</a></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $releases->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="novaos-admin-release-row">
            <div class="novaos-admin-version"><strong><?php echo e($release->version); ?></strong><small><?php echo e($release->major_version); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($release->codename): ?> · <?php echo e($release->codename); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></small></div>
            <div><span class="novaos-channel channel-<?php echo e(strtolower($release->channel)); ?>"><?php echo e($release->channel); ?></span><small><?php echo e($release->architecture); ?> · build <?php echo e($release->build_number); ?></small></div>
            <div><small><?php echo e($release->file_name ?: 'No package'); ?></small><small><?php echo e($release->sha256 ? 'SHA-256 ready' : 'Checksum unavailable'); ?></small></div>
            <span class="status <?php echo e($release->is_published?'published':'draft'); ?>"><?php echo e($release->is_published?'Published':'Draft'); ?></span>
            <a href="<?php echo e(route('admin.novaos.releases.edit',$release)); ?>">Edit</a>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <p class="admin-empty">No NOVAOS releases yet. Create the first operating-system build.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/projects/others/RozeHub/resources/views/admin/novaos/index.blade.php ENDPATH**/ ?>