@extends('admin.layout', ['heading' => $mode === 'create' ? 'Create marketplace item' : 'Edit marketplace item'])

@section('content')
<div class="admin-page-head">
    <div>
        <p class="admin-kicker">Marketplace identity</p>
        <h2>{{ $mode === 'create' ? 'New plugin or extension' : $item->name }}</h2>
        <p class="muted">One marketplace item can have many versions and platform-specific packages.</p>
    </div>
</div>

<form class="admin-form" method="POST" action="{{ $mode === 'create' ? route('admin.marketplace.store') : route('admin.marketplace.update', $item) }}">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="form-grid-2">
        <label>Application
            <select name="software_project_id" required>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(old('software_project_id', $item->software_project_id) == $project->id)>{{ $project->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Type
            <select name="item_type" required>
                <option value="plugin" @selected(old('item_type', $item->item_type) === 'plugin')>Plugin</option>
                <option value="extension" @selected(old('item_type', $item->item_type) === 'extension')>Extension</option>
            </select>
        </label>
        <label>Name
            <input name="name" required maxlength="160" value="{{ old('name', $item->name) }}" placeholder="PostgreSQL Tools">
        </label>
        <label>Stable Item ID
            <input name="item_id" required maxlength="160" value="{{ old('item_id', $item->item_id) }}" placeholder="com.roze.dbnavigator.postgresql">
        </label>
        <label>URL slug
            <input name="slug" maxlength="120" value="{{ old('slug', $item->slug) }}" placeholder="postgresql-tools">
        </label>
        <label>Vendor / publisher
            <input name="vendor" maxlength="160" value="{{ old('vendor', $item->vendor) }}" placeholder="Roze">
        </label>
        <label>Category
            <input name="category" maxlength="100" value="{{ old('category', $item->category) }}" placeholder="Database">
        </label>
        <label>Icon path
            <input name="icon_path" maxlength="255" value="{{ old('icon_path', $item->icon_path) }}" placeholder="images/marketplace/postgresql.png">
        </label>
        <label>Website
            <input type="url" name="website" value="{{ old('website', $item->website) }}" placeholder="https://…">
        </label>
        <label>Repository
            <input type="url" name="repository_url" value="{{ old('repository_url', $item->repository_url) }}" placeholder="https://github.com/…">
        </label>
    </div>

    <label>Short summary
        <textarea name="summary" maxlength="500" rows="2">{{ old('summary', $item->summary) }}</textarea>
    </label>
    <label>Description
        <textarea name="description" rows="8">{{ old('description', $item->description) }}</textarea>
    </label>

    <div class="market-permission-box">
        <h3>Requested capabilities</h3>
        <p class="muted">One permission per line. The desktop client can later show these before installation.</p>
        <textarea name="permissions_text" rows="5" placeholder="project.read&#10;editor.modify&#10;network">{{ old('permissions_text', implode("\n", $item->permissions ?? [])) }}</textarea>
    </div>

    <div class="check-row">
        <label><input type="checkbox" name="is_official" value="1" @checked(old('is_official', $item->is_official))> Official</label>
        <label><input type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $item->is_verified))> Verified publisher</label>
        <label><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))> Publish to public marketplace</label>
    </div>

    <div class="form-actions">
        <a class="admin-button" href="{{ route('admin.marketplace.index') }}">Cancel</a>
        <button class="admin-button primary">{{ $mode === 'create' ? 'Create item' : 'Save changes' }}</button>
    </div>
</form>
@endsection
