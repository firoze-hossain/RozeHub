<?php $__env->startSection('content'); ?>
<div class="admin-page-head">
    <div>
        <p class="admin-kicker">Marketplace identity</p>
        <h2><?php echo e($mode === 'create' ? 'New plugin or extension' : $item->name); ?></h2>
        <p class="muted">One marketplace item can have many versions and platform-specific packages.</p>
    </div>
</div>

<form class="admin-form" method="POST" action="<?php echo e($mode === 'create' ? route('admin.marketplace.store') : route('admin.marketplace.update', $item)); ?>">
    <?php echo csrf_field(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'edit'): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="form-grid-2">
        <label>Application
            <select name="software_project_id" required>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($project->id); ?>" <?php if(old('software_project_id', $item->software_project_id) == $project->id): echo 'selected'; endif; ?>><?php echo e($project->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </label>
        <label>Type
            <select name="item_type" required>
                <option value="plugin" <?php if(old('item_type', $item->item_type) === 'plugin'): echo 'selected'; endif; ?>>Plugin</option>
                <option value="extension" <?php if(old('item_type', $item->item_type) === 'extension'): echo 'selected'; endif; ?>>Extension</option>
            </select>
        </label>
        <label>Name
            <input name="name" required maxlength="160" value="<?php echo e(old('name', $item->name)); ?>" placeholder="PostgreSQL Tools">
        </label>
        <label>Stable Item ID
            <input name="item_id" required maxlength="160" value="<?php echo e(old('item_id', $item->item_id)); ?>" placeholder="com.roze.dbnavigator.postgresql">
        </label>
        <label>URL slug
            <input name="slug" maxlength="120" value="<?php echo e(old('slug', $item->slug)); ?>" placeholder="postgresql-tools">
        </label>
        <label>Vendor / publisher
            <input name="vendor" maxlength="160" value="<?php echo e(old('vendor', $item->vendor)); ?>" placeholder="Roze">
        </label>
        <label>Category
            <input name="category" maxlength="100" value="<?php echo e(old('category', $item->category)); ?>" placeholder="Database">
        </label>
        <label>Icon path
            <input name="icon_path" maxlength="255" value="<?php echo e(old('icon_path', $item->icon_path)); ?>" placeholder="images/marketplace/postgresql.png">
        </label>
        <label>Website
            <input type="url" name="website" value="<?php echo e(old('website', $item->website)); ?>" placeholder="https://…">
        </label>
        <label>Repository
            <input type="url" name="repository_url" value="<?php echo e(old('repository_url', $item->repository_url)); ?>" placeholder="https://github.com/…">
        </label>
    </div>

    <label>Short summary
        <textarea name="summary" maxlength="500" rows="2"><?php echo e(old('summary', $item->summary)); ?></textarea>
    </label>
    <label>Description
        <textarea name="description" rows="8"><?php echo e(old('description', $item->description)); ?></textarea>
    </label>

    <div class="market-permission-box">
        <h3>Requested capabilities</h3>
        <p class="muted">One permission per line. The desktop client can later show these before installation.</p>
        <textarea name="permissions_text" rows="5" placeholder="project.read&#10;editor.modify&#10;network"><?php echo e(old('permissions_text', implode("\n", $item->permissions ?? []))); ?></textarea>
    </div>

    <div class="check-row">
        <label><input type="checkbox" name="is_official" value="1" <?php if(old('is_official', $item->is_official)): echo 'checked'; endif; ?>> Official</label>
        <label><input type="checkbox" name="is_verified" value="1" <?php if(old('is_verified', $item->is_verified)): echo 'checked'; endif; ?>> Verified publisher</label>
        <label><input type="checkbox" name="is_published" value="1" <?php if(old('is_published', $item->is_published)): echo 'checked'; endif; ?>> Publish to public marketplace</label>
    </div>

    <div class="form-actions">
        <a class="admin-button" href="<?php echo e(route('admin.marketplace.index')); ?>">Cancel</a>
        <button class="admin-button primary"><?php echo e($mode === 'create' ? 'Create item' : 'Save changes'); ?></button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', ['heading' => $mode === 'create' ? 'Create marketplace item' : 'Edit marketplace item'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/Documents/RozeHub-marketplace-review-fix-v8/resources/views/admin/marketplace/item-form.blade.php ENDPATH**/ ?>