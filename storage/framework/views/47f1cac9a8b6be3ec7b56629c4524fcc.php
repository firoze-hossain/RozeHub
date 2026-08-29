<?php $__env->startSection('content'); ?>
<div class="dev-page-head">
    <div>
        <span class="eyebrow"><?php echo e($mode==='create'?'CREATE':'EDIT'); ?> ECOSYSTEM ITEM</span>
        <h1><?php echo e($mode==='create'?'Publish to the Roze ecosystem':'Edit '.$item->name); ?></h1>
        <p>RozeHub now uses a project-specific extension model. Choose the product you are extending and the allowed item types, capabilities and release targets are loaded dynamically.</p>
    </div>
</div>

<form class="dev-form" method="POST" action="<?php echo e($mode==='create'?route('developer.marketplace.store'):route('developer.marketplace.update',$item)); ?>">
    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode==='edit'): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ecosystem-picker">
        <div>
            <span class="eyebrow">TARGET ECOSYSTEM</span>
            <h3 id="ecosystem-title">Select a RozeHub project</h3>
            <p id="ecosystem-description">The extension model, capabilities and supported integrations will follow the selected project.</p>
        </div>
        <span class="ecosystem-kind" id="ecosystem-kind">—</span>
    </div>

    <div class="form-grid">
        <label>Project
            <select name="software_project_id" id="project-select" required>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($p->id); ?>" <?php if(old('software_project_id',$item->software_project_id)==$p->id): echo 'selected'; endif; ?>><?php echo e($p->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </label>
        <label>Extension type
            <select name="item_type" id="item-type" required></select>
        </label>
        <label>Name
            <input name="name" required value="<?php echo e(old('name',$item->name)); ?>" placeholder="PostgreSQL Tools">
        </label>
        <label>Stable ID
            <input name="item_id" required value="<?php echo e(old('item_id',$item->item_id)); ?>" placeholder="com.roze.dbnavigator.postgresql">
        </label>
        <label>Slug
            <input name="slug" value="<?php echo e(old('slug',$item->slug)); ?>">
        </label>
        <label>Publisher
            <input name="vendor" value="<?php echo e(old('vendor',$item->vendor)); ?>" placeholder="Your name or organization">
        </label>
        <label>Category
            <input name="category" value="<?php echo e(old('category',$item->category)); ?>" placeholder="Database">
        </label>
        <label>License
            <select name="license">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['MIT','Apache-2.0','GPL-3.0','BSD-2-Clause','BSD-3-Clause','MPL-2.0','LGPL-3.0','Proprietary']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $license): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($license); ?>" <?php if(old('license',$item->license)===$license): echo 'selected'; endif; ?>><?php echo e($license); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </label>
        <label>Website
            <input type="url" name="website" value="<?php echo e(old('website',$item->website)); ?>" placeholder="https://…">
        </label>
        <label>Repository
            <input type="url" name="repository_url" value="<?php echo e(old('repository_url',$item->repository_url)); ?>" placeholder="https://github.com/…">
        </label>
        <label>Support URL
            <input type="url" name="support_url" value="<?php echo e(old('support_url',$item->support_url)); ?>" placeholder="Issues, docs or support page">
        </label>
        <label>Icon path
            <input name="icon_path" value="<?php echo e(old('icon_path',$item->icon_path)); ?>" placeholder="images/marketplace/example.png">
        </label>
    </div>

    <label>Short summary
        <textarea name="summary" maxlength="500" rows="2" placeholder="What does this extension add?"><?php echo e(old('summary',$item->summary)); ?></textarea>
    </label>
    <label>Description
        <textarea name="description" rows="8" placeholder="Explain the use case, workflow and important limitations."><?php echo e(old('description',$item->description)); ?></textarea>
    </label>

    <div class="ecosystem-grid">
        <section class="permission-panel">
            <h3>Capabilities</h3>
            <p id="capability-help">Declare only the capabilities your package actually needs.</p>
            <div id="capability-suggestions" class="chip-list"></div>
            <textarea name="capabilities_text" rows="5" placeholder="database.connect&#10;schema.read"><?php echo e(old('capabilities_text',implode("\n",$item->capabilities??[]))); ?></textarea>
        </section>
        <section class="permission-panel">
            <h3>Compatibility & integrations</h3>
            <p>List products, protocols, runtimes or services this package integrates with.</p>
            <div id="integration-suggestions" class="chip-list"></div>
            <textarea name="compatibility_text" rows="5" placeholder="PostgreSQL&#10;StratosDB"><?php echo e(old('compatibility_text',implode("\n",$item->compatibility['targets']??[]))); ?></textarea>
            <input name="minimum_project_version" value="<?php echo e(old('minimum_project_version',$item->compatibility['minimumProjectVersion']??'')); ?>" placeholder="Minimum project version, e.g. 1.4.0">
        </section>
    </div>

    <div class="dev-form-actions"><a href="<?php echo e(route('developer.dashboard')); ?>">Cancel</a><button class="dev-primary" type="submit"><?php echo e($mode==='create'?'Create draft':'Save draft'); ?></button></div>
</form>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode==='edit'): ?>
<section class="release-workflow">
    <div class="dev-section-head release-workflow-head"><div><span class="eyebrow">RELEASE WORKFLOW</span><h2>Versions ready for review</h2><p>Each release is checked against the selected project's platform, architecture and package policy.</p></div><a class="dev-primary" href="<?php echo e(route('developer.marketplace.releases.create',$item)); ?>">+ Create release</a></div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $item->releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php $latestSubmission=$release->submissions->first(); $status=$latestSubmission?->status ?? 'DRAFT'; $canSubmit=$release->file_path && (!$latestSubmission || in_array($status,[\App\Models\MarketplaceSubmission::DRAFT,\App\Models\MarketplaceSubmission::NEEDS_CHANGES],true)); $isPending=in_array($status,[\App\Models\MarketplaceSubmission::SUBMITTED,\App\Models\MarketplaceSubmission::UNDER_REVIEW],true); ?>
        <article class="release-card"><div class="release-card-main"><div class="release-title-row"><div><div class="release-version">v<?php echo e($release->version); ?></div><div class="release-meta"><?php echo e($release->platform); ?> · <?php echo e($release->architecture); ?> · <?php echo e($release->channel); ?> · <?php echo e(strtoupper($release->package_type)); ?></div></div><span class="status-pill status-<?php echo e(strtolower($status)); ?>"><?php echo e(str_replace('_',' ',$status)); ?></span></div><div class="release-facts"><span><?php echo e($release->file_name ? 'Package uploaded' : 'Package missing'); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($release->file_size): ?><span><?php echo e(number_format($release->file_size/1048576,1)); ?> MB</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestSubmission): ?><span>Risk: <?php echo e($latestSubmission->risk_level); ?> · <?php echo e($latestSubmission->risk_score); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestSubmission && $latestSubmission->status===\App\Models\MarketplaceSubmission::NEEDS_CHANGES && $latestSubmission->reviewer_notes): ?><div class="release-review-note"><strong>Changes requested</strong><p><?php echo e($latestSubmission->reviewer_notes); ?></p></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div><div class="release-card-actions"><a class="dev-secondary" href="<?php echo e(route('developer.marketplace.release.edit',$release)); ?>">Edit release</a><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSubmit): ?><form method="POST" action="<?php echo e($latestSubmission && $status===\App\Models\MarketplaceSubmission::NEEDS_CHANGES ? route('developer.marketplace.resubmit',$latestSubmission) : route('developer.marketplace.submit',$release)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="developer_message" value="Submitting the updated release for RozeHub review."><button class="dev-primary" type="submit"><?php echo e($latestSubmission && $status===\App\Models\MarketplaceSubmission::NEEDS_CHANGES ? 'Resubmit for review' : 'Submit for review'); ?></button></form><?php elseif($isPending): ?><span class="release-pending">✓ In moderation queue</span><?php elseif(!$release->file_path): ?><span class="release-pending warning">Upload package first</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></article>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="release-empty"><div class="release-empty-icon">↑</div><h3>No release created yet</h3><p>Create the first version, upload the package and submit it for moderation.</p><a class="dev-primary" href="<?php echo e(route('developer.marketplace.releases.create',$item)); ?>">Create first release</a></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php
    $ecosystemPayload = $projects->mapWithKeys(function ($p) {
        $profile = $p->ecosystemProfile;

        return [
            $p->id => [
                'name' => $p->name,
                'kind' => $profile?->ecosystem_type,
                'title' => $profile?->title,
                'description' => $profile?->description,
                'types' => $profile?->item_types ?? [],
                'capabilities' => $profile?->capabilities ?? [],
                'integrations' => $profile?->integration_targets ?? [],
            ],
        ];
    })->toArray();
?>
<script>
const ecosystems = <?php echo e(Illuminate\Support\Js::from($ecosystemPayload)); ?>;
const selectedType = <?php echo json_encode(old('item_type', $item->item_type), 512) ?>;
const projectSelect=document.getElementById('project-select'), typeSelect=document.getElementById('item-type');
function renderEcosystem(){
    const e=ecosystems[projectSelect.value]; if(!e)return;
    document.getElementById('ecosystem-title').textContent=e.title||e.name;
    document.getElementById('ecosystem-description').textContent=e.description||'';
    document.getElementById('ecosystem-kind').textContent=(e.kind||'ecosystem').replaceAll('_',' ').toUpperCase();
    typeSelect.innerHTML=e.types.map(t=>`<option value="${t}">${t.replaceAll('-',' ').replace(/\b\w/g,c=>c.toUpperCase())}</option>`).join('');
    if(e.types.includes(selectedType) && !typeSelect.dataset.changed) typeSelect.value=selectedType;
    document.getElementById('capability-suggestions').innerHTML=e.capabilities.map(x=>`<button type="button" class="chip" onclick="appendLine('capabilities_text','${x}')">${x}</button>`).join('');
    document.getElementById('integration-suggestions').innerHTML=e.integrations.map(x=>`<button type="button" class="chip" onclick="appendLine('compatibility_text','${x}')">${x}</button>`).join('');
}
function appendLine(name,value){const el=document.querySelector(`[name="${name}"]`);const lines=el.value.split(/\r?\n/).map(x=>x.trim()).filter(Boolean);if(!lines.includes(value))lines.push(value);el.value=lines.join('\n');}
projectSelect.addEventListener('change',()=>{typeSelect.dataset.changed='1';renderEcosystem();});
renderEcosystem();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('developer.layout',['title'=>($mode==='create'?'Create':'Edit').' Marketplace Item · RozeHub'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/projects/others/RozeHub/resources/views/developer/marketplace/item-form.blade.php ENDPATH**/ ?>