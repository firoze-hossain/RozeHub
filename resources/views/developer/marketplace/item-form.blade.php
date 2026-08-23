@extends('developer.layout',['title'=>($mode==='create'?'Create':'Edit').' Marketplace Item · RozeHub'])
@section('content')
<div class="dev-page-head">
    <div>
        <span class="eyebrow">{{ $mode==='create'?'CREATE':'EDIT' }} MARKETPLACE ITEM</span>
        <h1>{{ $mode==='create'?'Start a plugin or extension':'Edit '.$item->name }}</h1>
        <p>Only Lumina and DBNavigator accept community marketplace submissions. Create the item first, then create a release and submit that release for moderation.</p>
    </div>
</div>

<form class="dev-form" method="POST" action="{{ $mode==='create'?route('developer.marketplace.store'):route('developer.marketplace.update',$item) }}">
    @csrf
    @if($mode==='edit') @method('PUT') @endif

    <div class="form-grid">
        <label>Application
            <select name="software_project_id" required>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" @selected(old('software_project_id',$item->software_project_id)==$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Type
            <select name="item_type" required>
                <option value="plugin" @selected(old('item_type',$item->item_type)==='plugin')>Plugin</option>
                <option value="extension" @selected(old('item_type',$item->item_type)==='extension')>Extension</option>
            </select>
        </label>
        <label>Name
            <input name="name" required value="{{ old('name',$item->name) }}" placeholder="PostgreSQL Tools">
        </label>
        <label>Stable ID
            <input name="item_id" required value="{{ old('item_id',$item->item_id) }}" placeholder="com.example.postgresql-tools">
        </label>
        <label>Slug
            <input name="slug" value="{{ old('slug',$item->slug) }}">
        </label>
        <label>Publisher
            <input name="vendor" value="{{ old('vendor',$item->vendor) }}">
        </label>
        <label>Category
            <input name="category" value="{{ old('category',$item->category) }}" placeholder="Database">
        </label>
        <label>Icon path
            <input name="icon_path" value="{{ old('icon_path',$item->icon_path) }}">
        </label>
        <label>Website
            <input type="url" name="website" value="{{ old('website',$item->website) }}">
        </label>
        <label>Repository
            <input type="url" name="repository_url" value="{{ old('repository_url',$item->repository_url) }}">
        </label>
    </div>

    <label>Short summary
        <textarea name="summary" maxlength="500" rows="2">{{ old('summary',$item->summary) }}</textarea>
    </label>
    <label>Description
        <textarea name="description" rows="8">{{ old('description',$item->description) }}</textarea>
    </label>

    <div class="permission-panel">
        <h3>Requested permissions</h3>
        <p>Declare only capabilities your package actually needs. Administrators use these declarations during security review.</p>
        <textarea name="permissions_text" rows="6" placeholder="project.read&#10;project.write&#10;network&#10;process.execute">{{ old('permissions_text',implode("\n",$item->permissions??[])) }}</textarea>
    </div>

    <div class="dev-form-actions">
        <a href="{{ route('developer.dashboard') }}">Cancel</a>
        <button class="dev-primary" type="submit">{{ $mode==='create'?'Create draft':'Save draft' }}</button>
    </div>
</form>

@if($mode==='edit')
<section class="release-workflow">
    <div class="dev-section-head release-workflow-head">
        <div>
            <span class="eyebrow">RELEASE &amp; SUBMISSION WORKFLOW</span>
            <h2>Versions ready for review</h2>
            <p>Create a version below, upload its package, then submit that exact version to the RozeHub moderation queue.</p>
        </div>
        <a class="dev-primary" href="{{ route('developer.marketplace.releases.create',$item) }}">+ Create release</a>
    </div>

    @forelse($item->releases as $release)
        @php
            $latestSubmission = $release->submissions->first();
            $status = $latestSubmission?->status ?? 'DRAFT';
            $canSubmit = $release->file_path && (!$latestSubmission || in_array($status,[\App\Models\MarketplaceSubmission::DRAFT,\App\Models\MarketplaceSubmission::NEEDS_CHANGES],true));
            $isPending = in_array($status,[\App\Models\MarketplaceSubmission::SUBMITTED,\App\Models\MarketplaceSubmission::UNDER_REVIEW],true);
        @endphp
        <article class="release-card">
            <div class="release-card-main">
                <div class="release-title-row">
                    <div>
                        <div class="release-version">v{{ $release->version }}</div>
                        <div class="release-meta">{{ $release->platform }} · {{ $release->architecture }} · {{ $release->channel }} · {{ strtoupper($release->package_type) }}</div>
                    </div>
                    <span class="status-pill status-{{ strtolower($status) }}">{{ str_replace('_',' ',$status) }}</span>
                </div>
                <div class="release-facts">
                    <span>{{ $release->file_name ? 'Package uploaded' : 'Package missing' }}</span>
                    @if($release->file_size)<span>{{ number_format($release->file_size/1048576,1) }} MB</span>@endif
                    @if($latestSubmission)<span>Risk: {{ $latestSubmission->risk_level }} · {{ $latestSubmission->risk_score }}</span>@endif
                </div>
                @if($latestSubmission && $latestSubmission->status===\App\Models\MarketplaceSubmission::NEEDS_CHANGES && $latestSubmission->reviewer_notes)
                    <div class="release-review-note"><strong>Changes requested</strong><p>{{ $latestSubmission->reviewer_notes }}</p></div>
                @endif
            </div>
            <div class="release-card-actions">
                <a class="dev-secondary" href="{{ route('developer.marketplace.release.edit',$release) }}">Edit release</a>
                @if($canSubmit)
                    <form method="POST" action="{{ $latestSubmission && $status===\App\Models\MarketplaceSubmission::NEEDS_CHANGES ? route('developer.marketplace.resubmit',$latestSubmission) : route('developer.marketplace.submit',$release) }}">
                        @csrf
                        <input type="hidden" name="developer_message" value="{{ $latestSubmission && $status===\App\Models\MarketplaceSubmission::NEEDS_CHANGES ? 'Resubmitting the updated release after requested changes.' : 'Submitting this release for RozeHub review.' }}">
                        <button class="dev-primary" type="submit">{{ $latestSubmission && $status===\App\Models\MarketplaceSubmission::NEEDS_CHANGES ? 'Resubmit for review' : 'Submit for review' }}</button>
                    </form>
                @elseif($isPending)
                    <span class="release-pending">✓ In moderation queue</span>
                @elseif(!$release->file_path)
                    <span class="release-pending warning">Upload package first</span>
                @endif
            </div>
        </article>
    @empty
        <div class="release-empty">
            <div class="release-empty-icon">↑</div>
            <h3>No release created yet</h3>
            <p>Your marketplace item is still a draft. Create the first version, upload the package, and you will get the <strong>Submit for review</strong> button on that release.</p>
            <a class="dev-primary" href="{{ route('developer.marketplace.releases.create',$item) }}">Create first release</a>
        </div>
    @endforelse
</section>
@endif
@endsection
