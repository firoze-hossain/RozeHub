@extends('admin.layout', ['heading' => $mode === 'create' ? 'Create marketplace item' : 'Edit marketplace item'])
@section('content')
<div class="admin-page-head"><div><p class="admin-kicker">Ecosystem marketplace</p><h2>{{ $mode === 'create' ? 'New ecosystem item' : $item->name }}</h2><p class="muted">The selected project controls which extension types, capabilities and integrations are valid.</p></div></div>
<form class="admin-form" method="POST" action="{{ $mode === 'create' ? route('admin.marketplace.store') : route('admin.marketplace.update', $item) }}">@csrf @if($mode === 'edit') @method('PUT') @endif
<div class="form-grid-2">
<label>Project<select name="software_project_id" id="admin-project" required>@foreach($projects as $project)<option value="{{ $project->id }}" @selected(old('software_project_id',$item->software_project_id)==$project->id)>{{ $project->name }}</option>@endforeach</select></label>
<label>Type<select name="item_type" id="admin-type" required></select></label>
<label>Name<input name="name" required maxlength="160" value="{{ old('name',$item->name) }}"></label><label>Stable Item ID<input name="item_id" required maxlength="160" value="{{ old('item_id',$item->item_id) }}"></label>
<label>URL slug<input name="slug" maxlength="120" value="{{ old('slug',$item->slug) }}"></label><label>Vendor / publisher<input name="vendor" maxlength="160" value="{{ old('vendor',$item->vendor) }}"></label>
<label>Category<input name="category" maxlength="100" value="{{ old('category',$item->category) }}"></label><label>License<select name="license">@foreach(['MIT','Apache-2.0','GPL-3.0','BSD-2-Clause','BSD-3-Clause','MPL-2.0','LGPL-3.0','Proprietary'] as $license)<option value="{{ $license }}" @selected(old('license',$item->license)===$license)>{{ $license }}</option>@endforeach</select></label>
<label>Website<input type="url" name="website" value="{{ old('website',$item->website) }}"></label><label>Repository<input type="url" name="repository_url" value="{{ old('repository_url',$item->repository_url) }}"></label><label>Support URL<input type="url" name="support_url" value="{{ old('support_url',$item->support_url) }}"></label><label>Icon path<input name="icon_path" value="{{ old('icon_path',$item->icon_path) }}"></label>
</div>
<label>Short summary<textarea name="summary" maxlength="500" rows="2">{{ old('summary',$item->summary) }}</textarea></label><label>Description<textarea name="description" rows="8">{{ old('description',$item->description) }}</textarea></label>
<div class="ecosystem-grid"><section class="market-permission-box"><h3>Capabilities</h3><p class="muted" id="admin-capability-help"></p><div id="admin-capability-chips" class="chip-list"></div><textarea name="capabilities_text" rows="5">{{ old('capabilities_text',implode("\n",$item->capabilities??[])) }}</textarea></section><section class="market-permission-box"><h3>Compatibility & integrations</h3><div id="admin-integration-chips" class="chip-list"></div><textarea name="compatibility_text" rows="5">{{ old('compatibility_text',implode("\n",$item->compatibility['targets']??[])) }}</textarea><input name="minimum_project_version" value="{{ old('minimum_project_version',$item->compatibility['minimumProjectVersion']??'') }}" placeholder="Minimum project version"></section></div>
<div class="check-row"><label><input type="checkbox" name="is_official" value="1" @checked(old('is_official',$item->is_official))> Official</label><label><input type="checkbox" name="is_verified" value="1" @checked(old('is_verified',$item->is_verified))> Verified publisher</label><label><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$item->is_published))> Publish</label></div>
<div class="form-actions"><a class="admin-button" href="{{ route('admin.marketplace.index') }}">Cancel</a><button class="admin-button primary">{{ $mode === 'create' ? 'Create item' : 'Save changes' }}</button></div></form>
@php
    $adminEcosystemPayload = $projects->mapWithKeys(function ($p) {
        $profile = $p->ecosystemProfile;

        return [
            $p->id => [
                'types' => $profile?->item_types ?? [],
                'capabilities' => $profile?->capabilities ?? [],
                'integrations' => $profile?->integration_targets ?? [],
            ],
        ];
    })->toArray();
@endphp
<script>
const adminEcosystems={{ Illuminate\Support\Js::from($adminEcosystemPayload) }};
const adminSelectedType=@json(old('item_type',$item->item_type));const ap=document.getElementById('admin-project'),at=document.getElementById('admin-type');
function adminRender(){const e=adminEcosystems[ap.value]||{types:[],capabilities:[],integrations:[]};at.innerHTML=e.types.map(x=>`<option value="${x}">${x.replaceAll('-',' ').replace(/\b\w/g,c=>c.toUpperCase())}</option>`).join('');if(!at.dataset.changed&&e.types.includes(adminSelectedType))at.value=adminSelectedType;document.getElementById('admin-capability-chips').innerHTML=e.capabilities.map(x=>`<button type="button" class="chip" onclick="adminAdd('capabilities_text','${x}')">${x}</button>`).join('');document.getElementById('admin-integration-chips').innerHTML=e.integrations.map(x=>`<button type="button" class="chip" onclick="adminAdd('compatibility_text','${x}')">${x}</button>`).join('');}
function adminAdd(name,v){const el=document.querySelector(`[name="${name}"]`);let a=el.value.split(/\r?\n/).map(x=>x.trim()).filter(Boolean);if(!a.includes(v))a.push(v);el.value=a.join('\n');}ap.addEventListener('change',()=>{at.dataset.changed='1';adminRender()});adminRender();
</script>
@endsection
