<main class="shell">
        <header class="topbar">
            <a class="brand" href="<?php echo e(route('hub')); ?>"><span class="brand-logo"><img src="<?php echo e(asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2'); ?>" alt="RozeHub"></span><span>RozeHub</span></a>
            <nav><a class="active" href="#catalog">Explore</a><a href="<?php echo e(route('marketplace.index')); ?>">Extensions</a><a href="#releases">Releases</a><a href="<?php echo e(route('docs.index')); ?>">Documentation</a><a href="#community">Community</a></nav>
            <a class="studio-link" href="<?php echo e(route('admin.login')); ?>">Admin login <span>↗</span></a>
        </header>

        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow"><i></i> Open Source · Built for Developers</p>
                <h1>Build. Code.<br><em>Innovate.</em></h1>
                <p class="hero-text">RozeHub is the home of independent software built with passion and purpose. Discover developer tools, desktop applications, programming technology, and infrastructure — all in one place.</p>
                <div class="hero-actions"><a class="button primary" href="#catalog">Browse software <span>↓</span></a><a class="text-link" href="#releases">Latest releases <span>→</span></a></div>
            </div>
            <div class="hero-art ecosystem-art" aria-label="RozeHub product ecosystem">
                <div class="ecosystem-glow"></div>
                <div class="ecosystem-visual">
                    <img class="ecosystem-image" src="<?php echo e(asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2'); ?>" alt="RozeHub ecosystem showing Lumina, DBNavigator, Thundercall, TrackEye, Roze, StratosDB and NOVAOS">

                    <?php
                        $ecosystemProducts = [
                            ['slug' => 'lumina', 'name' => 'Lumina', 'image' => 'lumina.png', 'class' => 'lumina'],
                            ['slug' => 'dbnavigator', 'name' => 'DBNavigator', 'image' => 'dbnavigator.png', 'class' => 'dbnavigator'],
                            ['slug' => 'thundercall', 'name' => 'ThunderCall', 'image' => 'thundercall.png', 'class' => 'thundercall'],
                            ['slug' => 'novaos', 'name' => 'NOVAOS', 'image' => 'novaos.png', 'class' => 'novaos'],
                            ['slug' => 'trackline', 'name' => 'TrackEye', 'image' => 'trackeye.png', 'class' => 'trackeye'],
                            ['slug' => 'roze-language', 'name' => 'Roze', 'image' => 'roze.png', 'class' => 'roze'],
                            ['slug' => 'stratosdb', 'name' => 'StratosDB', 'image' => 'stratosdb.png', 'class' => 'stratosdb'],
                        ];
                    ?>

                    <div class="ecosystem-hotspots" aria-label="Explore RozeHub projects">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ecosystemProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a class="ecosystem-hotspot hotspot-<?php echo e($product['class']); ?>" href="#catalog" aria-label="Preview <?php echo e($product['name']); ?>">
                                <span class="sr-only"><?php echo e($product['name']); ?></span>
                                <span class="ecosystem-preview" role="img" aria-label="<?php echo e($product['name']); ?> preview">
                                    <img src="<?php echo e(asset('images/projects/'.$product['image'])); ?>" alt="<?php echo e($product['name']); ?>">
                                    <strong><?php echo e($product['name']); ?></strong>
                                    <small>View project</small>
                                </span>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="pulse" aria-label="RozeHub statistics"><div><strong><?php echo e(number_format($totalDownloads)); ?></strong><span>downloads delivered</span></div><div><strong><?php echo e($projects->count() + ($novaos ? 1 : 0)); ?></strong><span>projects in the ecosystem</span></div><div><strong><?php echo e($releaseCount); ?></strong><span>release packages available</span></div><div><strong>3</strong><span>platforms supported</span></div></section>

        <section id="catalog" class="catalog-section">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Software catalog</p>
                    <h2>Find your next workflow.</h2>
                </div>
                <p>Explore desktop tools, developer software and infrastructure projects. NOVAOS has its own dedicated operating-system area below.</p>
            </div>

            <div class="filterbar">
                <label class="search"><span>⌕</span><input wire:model.live.debounce.250ms="search" placeholder="Search software, category, capability"></label>
                <div class="platforms">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['All platforms', 'Windows', 'macOS', 'Linux', 'NOVAOS']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button wire:click="setPlatform('<?php echo e($item); ?>')" class="<?php echo e($platform === $item ? 'selected' : ''); ?>"><?php echo e($item); ?></button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($platform === 'NOVAOS'): ?>
                <div class="catalog-label novaos-filter-label">
                    <div><span class="catalog-dot novaos-dot"></span><strong>NOVAOS operating system</strong></div>
                    <span>Independent OS</span>
                </div>
            <?php else: ?>
                <div class="catalog-label">
                    <div><span class="catalog-dot"></span><strong>Applications & developer tools</strong></div>
                    <span><?php echo e($projects->count()); ?> projects</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($platform !== 'NOVAOS'): ?>
            <div class="project-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php ($latest = $project->releases->first()); ?>
                    <?php ($projectImage = $this->projectImageFor($project->slug)); ?>
                    <article class="project-card <?php echo e($selected?->id === $project->id ? 'is-selected' : ''); ?>" wire:click="selectProject(<?php echo e($project->id); ?>)">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($projectImage): ?>
                            <div class="project-hover-image" aria-hidden="true">
                                <img src="<?php echo e(asset('images/projects/'.$projectImage)); ?>" alt="">
                                <span class="project-hover-label">View <?php echo e($project->name); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="project-top"><span class="project-icon accent-<?php echo e($project->accent); ?>"><?php echo e($project->icon); ?></span><span class="category"><?php echo e($project->category); ?></span></div>
                        <h3><?php echo e($project->name); ?></h3>
                        <p><?php echo e($project->tagline); ?></p>
                        <footer><span><?php echo e($latest ? 'v'.$latest->version : 'Coming soon'); ?></span><span><?php echo e($project->releases->sum('downloads_count') > 0 ? number_format($project->releases->sum('downloads_count')).' downloads' : 'New project'); ?></span></footer>
                    </article>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="empty">No software matches that search.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($novaos && in_array($platform, ['All platforms', 'NOVAOS'], true)): ?>
                <?php ($novaLatest = $novaos->releases->first()); ?>
                <section class="novaos-catalog-card" aria-labelledby="novaos-catalog-title">
                    <div class="novaos-catalog-art">
                        <div class="novaos-catalog-glow"></div>
                        <img src="<?php echo e(asset('images/projects/novaos.png')); ?>" alt="NOVAOS independent operating system">
                        <span class="novaos-catalog-orbit orbit-one"></span>
                        <span class="novaos-catalog-orbit orbit-two"></span>
                    </div>

                    <div class="novaos-catalog-content">
                        <div class="novaos-catalog-topline">
                            <p class="eyebrow"><i></i> Independent operating system</p>
                            <span class="novaos-catalog-badge">Not an application</span>
                        </div>
                        <h2 id="novaos-catalog-title">NOVAOS</h2>
                        <p class="novaos-catalog-lead">An independent operating system project with its own builds, architecture targets and release lifecycle.</p>

                        <div class="novaos-facts">
                            <div><span>Latest build</span><strong><?php echo e($novaLatest ? 'v'.$novaLatest->version : 'In development'); ?></strong></div>
                            <div><span>Published builds</span><strong><?php echo e($novaos->releases->count()); ?></strong></div>
                            <div><span>Downloads</span><strong><?php echo e(number_format($novaos->releases->sum('downloads_count'))); ?></strong></div>
                        </div>

                        <div class="novaos-catalog-actions">
                            <a class="button primary" href="#releases" wire:click="selectProject(<?php echo e($novaos->id); ?>)">Explore NOVAOS <span>→</span></a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($novaos->github_url): ?>
                                <a class="github-button" href="<?php echo e($novaos->github_url); ?>" target="_blank" rel="noopener noreferrer">
                                    <span aria-hidden="true">◈</span> View source &amp; contribute on GitHub <span aria-hidden="true">↗</span>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span class="novaos-platform-note"><b>◈</b> NOVAOS is an independent operating system and is never grouped under Windows, macOS or Linux.</span>
                        </div>
                    </div>

                    <div class="novaos-catalog-builds">
                        <div class="novaos-mini-heading"><span>Latest NOVAOS builds</span><span><?php echo e($novaos->releases->count()); ?> total</span></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $novaos->releases->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="#releases" wire:click="selectProject(<?php echo e($novaos->id); ?>)" class="novaos-mini-build">
                                <span class="novaos-mini-version"><?php echo e($release->version); ?></span>
                                <span><b><?php echo e($release->channel); ?></b><small><?php echo e($release->architecture); ?> · <?php echo e($release->published_at?->format('M j, Y')); ?></small></span>
                                <span class="novaos-mini-arrow">→</span>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <p class="empty">No published NOVAOS builds yet.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->isNovaosProject($selected)): ?>
                <section id="releases" class="detail-section novaos-detail">
                    <div class="novaos-heading">
                        <div class="novaos-brand">
                            <span class="novaos-icon"><img src="<?php echo e(asset('images/projects/novaos.png')); ?>" alt="NOVAOS"></span>
                            <div>
                                <p class="eyebrow">Independent operating system</p>
                                <h2>NOVAOS</h2>
                                <p>NOVAOS is distributed as its own operating-system builds. It does not belong under Windows, macOS, or Linux.</p>
                            </div>
                        </div>
                        <div class="novaos-heading-actions">
                            <span class="novaos-badge">Independent OS</span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->github_url): ?>
                                <a class="github-button" href="<?php echo e($selected->github_url); ?>" target="_blank" rel="noopener noreferrer">
                                    <span aria-hidden="true">◈</span> GitHub <span aria-hidden="true">↗</span>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="novaos-release-shell">
                        <div class="novaos-builds">
                            <div class="panel-title"><div><p class="eyebrow">NOVAOS releases</p><h3>Choose a build</h3></div><span><?php echo e($selected->releases->count()); ?> builds</span></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selected->releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <details class="novaos-build" <?php if($loop->first): ?> open <?php endif; ?>>
                                    <summary>
                                        <span class="novaos-version">Version <strong><?php echo e($release->version); ?></strong></span>
                                        <span class="novaos-build-meta"><b><?php echo e($release->channel); ?></b> · <?php echo e($release->published_at?->format('M j, Y')); ?> · <?php echo e($release->architecture); ?></span>
                                        <span class="novaos-chevron">⌄</span>
                                    </summary>
                                    <div class="novaos-build-body">
                                        <div class="novaos-build-info">
                                            <div><span>Major version</span><strong><?php echo e($release->major_version ?: $release->version); ?></strong></div>
                                            <div><span>Build</span><strong><?php echo e($release->build_number ?: '—'); ?></strong></div>
                                            <div><span>Architecture</span><strong><?php echo e($release->architecture); ?></strong></div>
                                            <div><span>Channel</span><strong><?php echo e($release->channel); ?></strong></div>
                                            <div><span>Published</span><strong><?php echo e($release->published_at?->format('F j, Y')); ?></strong></div>
                                            <div><span>Codename</span><strong><?php echo e($release->codename ?: '—'); ?></strong></div>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($release->notes): ?>
                                            <p class="novaos-notes"><?php echo e($release->notes); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="novaos-download">
                                            <div><strong><?php echo e($release->file_name ?: 'NOVAOS build '.$release->version); ?></strong><small><?php echo e($release->file_size ? number_format($release->file_size / 1048576, 1).' MB' : 'Official release package'); ?> · <?php echo e(number_format($release->downloads_count)); ?> downloads</small><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($release->sha256): ?><small class="novaos-sha">SHA-256: <?php echo e($release->sha256); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                                            <a class="download-button" href="<?php echo e(route('releases.download', $release)); ?>" title="Download NOVAOS <?php echo e($release->version); ?>">↓ Download</a>
                                        </div>
                                    </div>
                                </details>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <p class="empty">No published NOVAOS builds yet.</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <aside class="novaos-side">
                            <img src="<?php echo e(asset('images/projects/novaos.png')); ?>" alt="NOVAOS artwork">
                            <p class="eyebrow">About NOVAOS</p>
                            <h3>Independent by design.</h3>
                            <p>System releases are managed separately from application platforms, so NOVAOS users always see NOVAOS builds instead of Windows, macOS, or Linux labels.</p>
                            <div class="compat"><span>◈</span><div><strong>Official builds</strong><small>Published packages are delivered directly from RozeHub.</small></div></div>
                        </aside>
                    </div>
                </section>
            <?php else: ?>
                <section id="releases" class="detail-section">
                    <div class="detail-intro">
    <span class="project-icon large accent-<?php echo e($selected->accent); ?>"><?php echo e($selected->icon); ?></span>
    <div class="detail-project-copy">
        <p class="eyebrow">Selected project</p>
        <h2><?php echo e($selected->name); ?></h2>
        <p><?php echo e($selected->description); ?></p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selected->github_url): ?>
            <div class="project-source-actions">
                <a class="github-button" href="<?php echo e($selected->github_url); ?>" target="_blank" rel="noopener noreferrer">
                    <span aria-hidden="true">◈</span> View source &amp; contribute on GitHub <span aria-hidden="true">↗</span>
                </a>
                <span class="source-note">Open source · Issues &amp; pull requests welcome</span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
                    <div class="detail-layout"><div class="release-panel"><div class="panel-title"><h3>Available downloads</h3><span><?php echo e($selected->releases->count()); ?> builds</span></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selected->releases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $release): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="release-row"><div class="os-icon"><?php echo e($release->platform === 'Windows' ? '⊞' : ($release->platform === 'macOS' ? '◉' : '◈')); ?></div><div><strong><?php echo e($release->platform); ?> · <?php echo e($release->architecture); ?></strong><p>v<?php echo e($release->version); ?> · <?php echo e($release->channel); ?> · <?php echo e($release->published_at?->format('M j, Y')); ?></p></div><div class="release-count"><?php echo e(number_format($release->downloads_count)); ?><small>downloads</small></div><a class="download-button" href="<?php echo e(route('releases.download', $release)); ?>" title="Download <?php echo e($release->file_name); ?>">↓</a></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?> <p class="empty">No published packages yet.</p> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <aside class="release-notes"><p class="eyebrow">Release notes</p><h3><?php echo e($selected->releases->first()?->version ? 'What\'s new in v'.$selected->releases->first()->version : 'Next release in progress'); ?></h3><p><?php echo e($selected->releases->first()?->notes ?: 'The next version is being prepared. Check the publisher studio when it is ready.'); ?></p><div class="compat"><span>◎</span><div><strong>Verified releases</strong><small>Packages are listed only after publishing.</small></div></aside></div>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <section id="community" class="community-section"><div class="section-heading"><div><p class="eyebrow">Community notes</p><h2>Built in public.</h2></div><p>Share feedback about <?php echo e($selected?->name ?? 'the ecosystem'); ?> to help shape the next release.</p></div>
            <div class="community-layout"><div class="review-list"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $selected?->reviews ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><article class="review"><div class="review-avatar"><?php echo e(strtoupper(substr($review->author_name, 0, 1))); ?></div><div><div class="review-meta"><strong><?php echo e($review->author_name); ?></strong><span><?php echo e(str_repeat('★', $review->rating)); ?><i><?php echo e(str_repeat('★', 5 - $review->rating)); ?></i></span></div><p><?php echo e($review->body); ?></p></div></article><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?> <p class="empty">Be the first to leave a review for this project.</p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                <form class="review-form" wire:submit="saveReview"><p class="eyebrow">Leave a review</p><h3>Your release notes, in context.</h3><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('review-sent')): ?><p class="success"><?php echo e(session('review-sent')); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><label>Your name<input wire:model="reviewName" placeholder="Name or handle"></label><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['reviewName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><label>Rating<select wire:model="reviewRating"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($rating = 5; $rating >= 1; $rating--): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($rating); ?>"><?php echo e($rating); ?> stars</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></label><label>What worked for you?<textarea wire:model="reviewBody" placeholder="Share a useful, specific note..."></textarea></label><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['reviewBody'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="error"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><button class="button primary" type="submit">Publish review <span>→</span></button></form>
            </div>
        </section>
        <footer class="site-footer"><a class="brand" href="#"><span class="brand-logo"><img src="<?php echo e(asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2'); ?>" alt="RozeHub"></span><span>RozeHub</span></a><p>Software made with intent. Built by Firoze.</p><a href="<?php echo e(route('admin.login')); ?>">Admin login →</a></footer>
</main>
<?php /**PATH /home/firoze/Downloads/RozeHub-Phase2-Professional-Marketplace/RozeHub-Phase1-Architecture-Foundation-FIXED/resources/views/livewire/hub.blade.php ENDPATH**/ ?>