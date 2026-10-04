<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item->name }} · RozeHub Extensions</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/rozehub-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rozehub.css').'?v=20260829' }}">
    <style>
        :root {
            --mp-bg: #f6f7f6;
            --mp-card-bg: #ffffff;
            --mp-border: #e0e6e2;
            --mp-border-subtle: #edf1ee;
            --mp-text: #132420;
            --mp-muted: #5e6d68;
            --mp-brand: #0c2923;
            --mp-mint: #25a878;
            --mp-mint-soft: #eaf7f1;
            --mp-amber: #e69216;
            --mp-amber-soft: #fef7ec;
            --mp-blue: #2563eb;
            --mp-blue-soft: #eff6ff;
            --mp-lilac-soft: #f4f0fd;
            --mp-lilac: #7c3aed;
            --mp-shadow: 0 4px 20px rgba(12, 41, 35, 0.05);
            --mp-shadow-lg: 0 12px 36px rgba(12, 41, 35, 0.09);
        }

        body {
            background-color: var(--mp-bg);
            color: var(--mp-text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .mp-shell {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px 80px;
        }

        /* Breadcrumb navigation */
        .mp-breadcrumb-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 0 16px;
            font-size: 13px;
        }
        .mp-breadcrumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--mp-muted);
            flex-wrap: wrap;
        }
        .mp-breadcrumbs a {
            color: var(--mp-muted);
            text-decoration: none;
            transition: color 0.15s;
        }
        .mp-breadcrumbs a:hover {
            color: var(--mp-text);
            text-decoration: underline;
        }
        .mp-breadcrumbs .sep {
            color: #b5c2bd;
            font-size: 11px;
        }
        .mp-breadcrumbs .current {
            color: var(--mp-text);
            font-weight: 600;
        }
        .mp-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--mp-brand);
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            background: #fff;
            border: 1px solid var(--mp-border);
            transition: all 0.15s;
        }
        .mp-back-link:hover {
            background: var(--mp-border-subtle);
            border-color: #cbd7d1;
        }

        /* Hero Banner */
        .mp-hero {
            background: var(--mp-card-bg);
            border: 1px solid var(--mp-border);
            border-radius: 18px;
            padding: 36px 40px;
            box-shadow: var(--mp-shadow);
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .mp-hero-top {
            display: flex;
            gap: 28px;
            align-items: flex-start;
        }

        /* Hero Icon */
        .mp-hero-icon-wrapper {
            position: relative;
            flex-shrink: 0;
        }
        .mp-hero-icon {
            width: 88px;
            height: 88px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid var(--mp-border);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .mp-hero-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .mp-theme-preview-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%);
            display: flex;
            flex-direction: column;
            padding: 10px;
            box-sizing: border-box;
            position: relative;
        }
        .theme-icon-bar {
            display: flex;
            gap: 4px;
            margin-bottom: 8px;
        }
        .theme-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #cbd5e1;
        }
        .theme-dot.red { background: #ff5f56; }
        .theme-dot.yellow { background: #ffbd2e; }
        .theme-dot.green { background: #27c93f; }
        .theme-code-lines {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .theme-line {
            height: 4px;
            border-radius: 2px;
        }
        .theme-line-1 { width: 70%; background: #0033b3; }
        .theme-line-2 { width: 90%; background: #067d17; }
        .theme-line-3 { width: 55%; background: #3574f0; }
        .theme-line-4 { width: 80%; background: #871094; }
        .theme-accent-badge {
            position: absolute;
            bottom: 6px;
            right: 6px;
            background: #ffffff;
            border: 1px solid #d0d7de;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 9px;
            font-weight: 700;
            color: #1a7f37;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        /* Hero Content */
        .mp-hero-content {
            flex: 1;
            min-width: 0;
        }
        .mp-badges-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .mp-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 9px;
            border-radius: 6px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .mp-badge-type {
            background: #f1f4f2;
            color: var(--mp-brand);
            border: 1px solid #d5ded8;
        }
        .mp-badge-project {
            background: var(--mp-lilac-soft);
            color: var(--mp-lilac);
            border: 1px solid #e2d7f7;
            text-decoration: none;
        }
        .mp-badge-project img {
            width: 13px;
            height: 13px;
            object-fit: contain;
        }
        .mp-badge-official {
            background: var(--mp-mint-soft);
            color: #0e6245;
            border: 1px solid #b7e6d2;
        }
        .mp-badge-verified {
            background: var(--mp-blue-soft);
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .mp-hero h1 {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 10px;
            line-height: 1.15;
            color: var(--mp-text);
            letter-spacing: -0.6px;
        }
        .mp-hero-meta-row {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 13px;
            color: var(--mp-muted);
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .mp-meta-vendor {
            font-weight: 600;
            color: var(--mp-text);
        }
        .mp-plugin-id-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f3;
            border: 1px solid #d8e2dc;
            padding: 3px 8px;
            border-radius: 5px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
            color: #244138;
        }
        .mp-btn-copy-mini {
            background: transparent;
            border: 0;
            padding: 0;
            cursor: pointer;
            color: var(--mp-muted);
            display: flex;
            align-items: center;
            transition: color 0.15s;
        }
        .mp-btn-copy-mini:hover {
            color: var(--mp-brand);
        }
        .mp-meta-stat {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .mp-stars {
            color: var(--mp-amber);
            font-size: 14px;
            letter-spacing: 1px;
        }

        .mp-hero-summary {
            font-size: 15px;
            line-height: 1.6;
            color: #43544f;
            margin: 0 0 24px;
            max-width: 820px;
        }

        /* Hero Actions CTA */
        .mp-hero-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            padding-top: 18px;
            border-top: 1px solid var(--mp-border-subtle);
        }
        .mp-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--mp-brand);
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.18s ease;
            box-shadow: 0 4px 14px rgba(12, 41, 35, 0.16);
            border: 0;
            cursor: pointer;
        }
        .mp-btn-primary:hover {
            background: #174238;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(12, 41, 35, 0.22);
            color: #ffffff;
        }
        .mp-btn-primary svg {
            color: #66ddae;
        }
        .mp-btn-primary .subtext {
            font-size: 12px;
            font-weight: normal;
            opacity: 0.85;
            padding-left: 4px;
            border-left: 1px solid rgba(255,255,255,0.25);
            margin-left: 4px;
        }
        .mp-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: var(--mp-brand);
            font-weight: 700;
            font-size: 14px;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid var(--mp-border);
            cursor: pointer;
            transition: all 0.15s;
        }
        .mp-btn-secondary:hover {
            background: #f7faf8;
            border-color: #cbd8d1;
        }
        .mp-btn-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--mp-brand);
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 6px;
            transition: background 0.15s;
        }
        .mp-btn-link:hover {
            background: #eef3f0;
            text-decoration: underline;
        }

        /* Two-Column Grid */
        .mp-grid-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 32px;
            align-items: start;
        }

        /* Left Column Content */
        .mp-main-col {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        /* Sections */
        .mp-card {
            background: var(--mp-card-bg);
            border: 1px solid var(--mp-border);
            border-radius: 14px;
            padding: 28px 32px;
            box-shadow: var(--mp-shadow);
        }
        .mp-section-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0 0 18px;
            color: var(--mp-text);
            display: flex;
            align-items: center;
            justify-content: space-between;
            letter-spacing: -0.3px;
        }
        .mp-section-title .count-badge {
            font-size: 12px;
            font-weight: 600;
            color: var(--mp-muted);
            background: #f0f4f1;
            padding: 3px 8px;
            border-radius: 12px;
        }

        /* Quick In-IDE Install Guide Callout */
        .mp-install-callout {
            background: linear-gradient(135deg, #f8fbf9 0%, #eef6f2 100%);
            border: 1px solid #cce2d6;
            border-radius: 14px;
            padding: 22px 26px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .mp-install-callout-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #0c2923;
            color: #66ddae;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
        }
        .mp-install-callout-body {
            flex: 1;
        }
        .mp-install-callout-body h3 {
            margin: 0 0 6px;
            font-size: 16px;
            font-weight: 700;
            color: var(--mp-brand);
        }
        .mp-install-callout-body p {
            margin: 0 0 12px;
            font-size: 13px;
            line-height: 1.55;
            color: #3f524c;
        }
        .mp-install-steps {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .mp-step-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            background: #ffffff;
            border: 1px solid #d2ded7;
            padding: 6px 11px;
            border-radius: 6px;
            color: var(--mp-text);
        }
        .mp-step-num {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--mp-brand);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
        }

        /* Description Body */
        .mp-description-body {
            font-size: 15px;
            line-height: 1.7;
            color: #374742;
        }
        .mp-description-body p {
            margin: 0 0 16px;
        }
        .mp-description-body p:last-child {
            margin-bottom: 0;
        }

        /* Capabilities & Permissions */
        .mp-cap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }
        .mp-cap-item {
            background: #f8faf8;
            border: 1px solid var(--mp-border);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .mp-cap-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #edf3f0;
            color: var(--mp-brand);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }
        .mp-cap-text strong {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--mp-text);
            margin-bottom: 3px;
        }
        .mp-cap-text code {
            font-family: ui-monospace, monospace;
            font-size: 11px;
            color: #245b4c;
            background: #e7f0ec;
            padding: 1px 5px;
            border-radius: 4px;
        }
        .mp-cap-text p {
            margin: 5px 0 0;
            font-size: 12px;
            color: var(--mp-muted);
            line-height: 1.4;
        }

        /* Compatibility Pills */
        .mp-compat-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .mp-compat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f7faf8;
            border: 1px solid var(--mp-border);
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 13px;
            color: var(--mp-text);
        }
        .mp-compat-pill svg {
            color: var(--mp-mint);
        }

        /* Versions List */
        .mp-version-card {
            border: 1px solid var(--mp-border);
            border-radius: 10px;
            padding: 20px;
            background: #ffffff;
            margin-bottom: 14px;
            transition: border-color 0.15s;
        }
        .mp-version-card:last-child {
            margin-bottom: 0;
        }
        .mp-version-card:hover {
            border-color: #bccbc3;
        }
        .mp-version-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .mp-version-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .mp-version-title strong {
            font-size: 16px;
            font-weight: 700;
            color: var(--mp-text);
        }
        .mp-channel-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            background: var(--mp-mint-soft);
            color: #0e6245;
            border: 1px solid #b7e6d2;
        }
        .mp-version-date {
            font-size: 12px;
            color: var(--mp-muted);
        }
        .mp-version-meta-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .mp-meta-chip {
            font-size: 11px;
            color: var(--mp-muted);
            background: #f3f6f4;
            padding: 3px 7px;
            border-radius: 4px;
            border: 1px solid #e1e7e4;
        }
        .mp-version-notes {
            font-size: 13px;
            line-height: 1.6;
            color: #40514c;
            background: #fafbfa;
            border-left: 3px solid #66ddae;
            padding: 10px 14px;
            border-radius: 0 6px 6px 0;
            margin: 0 0 14px;
        }
        .mp-checksum-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f7faf8;
            border: 1px solid #e2e8e4;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 11px;
            margin-top: 10px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .mp-checksum-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            color: #2d554a;
            word-break: break-all;
        }
        .mp-btn-copy-hash {
            background: #fff;
            border: 1px solid #d2ded7;
            padding: 3px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            color: var(--mp-brand);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s;
        }
        .mp-btn-copy-hash:hover {
            background: var(--mp-border-subtle);
            border-color: #cbd7d1;
        }

        /* Ratings & Reviews */
        .mp-ratings-summary {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--mp-border-subtle);
            margin-bottom: 24px;
            align-items: center;
        }
        .mp-score-box {
            text-align: center;
            background: #f8faf8;
            border: 1px solid var(--mp-border);
            border-radius: 12px;
            padding: 18px 14px;
        }
        .mp-score-number {
            font-size: 42px;
            font-weight: 800;
            line-height: 1;
            color: var(--mp-text);
            margin-bottom: 6px;
        }
        .mp-score-stars {
            color: var(--mp-amber);
            font-size: 18px;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }
        .mp-score-count {
            font-size: 12px;
            color: var(--mp-muted);
        }
        .mp-score-bars {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .mp-bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--mp-muted);
        }
        .mp-bar-label {
            width: 38px;
            text-align: right;
            font-weight: 600;
        }
        .mp-bar-track {
            flex: 1;
            height: 8px;
            background: #e9eee9;
            border-radius: 4px;
            overflow: hidden;
        }
        .mp-bar-fill {
            height: 100%;
            background: var(--mp-amber);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .mp-bar-count {
            width: 32px;
            font-size: 11px;
            color: var(--mp-muted);
        }

        /* Review Items */
        .mp-review-card {
            border: 1px solid var(--mp-border);
            border-radius: 10px;
            padding: 18px 20px;
            background: #fafcfa;
            margin-bottom: 14px;
        }
        .mp-review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .mp-reviewer-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .mp-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e4ede7;
            color: var(--mp-brand);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }
        .mp-reviewer-name {
            font-weight: 700;
            font-size: 13px;
            color: var(--mp-text);
        }
        .mp-review-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--mp-text);
            margin: 0 0 6px;
        }
        .mp-review-body {
            font-size: 13px;
            line-height: 1.6;
            color: #40524c;
            margin: 0;
        }

        /* Review Submission Form (Proper Vertical Layout) */
        .mp-review-form-card {
            background: #ffffff;
            border: 1px solid var(--mp-border);
            border-radius: 12px;
            padding: 24px;
            margin-top: 24px;
        }
        .mp-review-form-card h3 {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 800;
            color: var(--mp-text);
        }
        .mp-review-form-card p {
            margin: 0 0 18px;
            font-size: 13px;
            color: var(--mp-muted);
        }
        .mp-form-group {
            margin-bottom: 16px;
        }
        .mp-form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--mp-text);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .mp-form-input, .mp-form-textarea, .mp-form-select {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccd8d2;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: inherit;
            color: var(--mp-text);
            background: #ffffff;
            transition: all 0.15s;
        }
        .mp-form-input:focus, .mp-form-textarea:focus, .mp-form-select:focus {
            border-color: #2b8a73;
            outline: 0;
            box-shadow: 0 0 0 3px rgba(43, 138, 115, 0.12);
        }
        .mp-form-textarea {
            min-height: 90px;
            resize: vertical;
        }
        .mp-star-picker {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .mp-star-btn {
            background: transparent;
            border: 0;
            font-size: 24px;
            color: #d1dbd5;
            cursor: pointer;
            padding: 0;
            transition: color 0.12s;
            line-height: 1;
        }
        .mp-star-btn.active, .mp-star-btn:hover {
            color: var(--mp-amber);
        }
        .mp-star-rating-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--mp-text);
            margin-left: 8px;
        }
        .mp-btn-submit-review {
            background: var(--mp-brand);
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
            border: 0;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .mp-btn-submit-review:hover {
            background: #174238;
        }
        .mp-guest-review-box {
            background: #f7faf8;
            border: 1px dashed #c8d7cf;
            border-radius: 12px;
            padding: 22px;
            text-align: center;
            margin-top: 20px;
        }
        .mp-guest-review-box p {
            margin: 0 0 12px;
            font-size: 13px;
            color: var(--mp-muted);
        }

        /* Right Column Sidebar */
        .mp-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .mp-side-card {
            background: var(--mp-card-bg);
            border: 1px solid var(--mp-border);
            border-radius: 14px;
            padding: 24px;
            box-shadow: var(--mp-shadow);
        }
        .mp-side-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--mp-text);
            margin: 0 0 16px;
            letter-spacing: -0.2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mp-meta-table {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .mp-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            font-size: 13px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--mp-border-subtle);
        }
        .mp-meta-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
        .mp-meta-label {
            color: var(--mp-muted);
            font-size: 12px;
            flex-shrink: 0;
        }
        .mp-meta-val {
            font-weight: 600;
            color: var(--mp-text);
            text-align: right;
            word-break: break-all;
        }
        .mp-meta-val code {
            font-family: ui-monospace, monospace;
            font-size: 11px;
            background: #f1f4f2;
            padding: 2px 5px;
            border-radius: 4px;
        }

        /* Publisher Side Card */
        .mp-publisher-box {
            display: flex;
            gap: 14px;
            align-items: center;
            margin-bottom: 12px;
        }
        .mp-pub-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #e6f0eb;
            color: var(--mp-brand);
            font-size: 18px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #d5e4dc;
        }
        .mp-pub-info strong {
            display: block;
            font-size: 15px;
            color: var(--mp-text);
        }
        .mp-pub-bio {
            font-size: 13px;
            line-height: 1.5;
            color: var(--mp-muted);
            margin: 0 0 14px;
        }
        .mp-pub-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
        }
        .mp-pub-link {
            color: var(--mp-brand);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .mp-pub-link:hover {
            text-decoration: underline;
        }

        /* Ecosystem Side Card */
        .mp-eco-card {
            background: linear-gradient(135deg, #f7faf8 0%, #edf5f1 100%);
            border: 1px solid #cce2d6;
            border-radius: 14px;
            padding: 20px;
        }
        .mp-eco-card strong {
            display: block;
            font-size: 14px;
            color: var(--mp-brand);
            margin-bottom: 6px;
        }
        .mp-eco-card p {
            margin: 0 0 12px;
            font-size: 12px;
            line-height: 1.55;
            color: #4b6058;
        }
        .mp-eco-link {
            font-size: 12px;
            font-weight: 700;
            color: #0b5c4c;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .mp-eco-link:hover {
            text-decoration: underline;
        }

        /* Modal Dialog */
        .mp-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(16, 34, 30, 0.55);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .mp-modal-backdrop.is-active {
            display: flex;
        }
        .mp-modal {
            background: #ffffff;
            border-radius: 18px;
            max-width: 580px;
            width: 100%;
            padding: 32px;
            box-shadow: var(--mp-shadow-lg);
            position: relative;
            animation: modalFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .mp-modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f1f4f2;
            border: 0;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--mp-muted);
            transition: all 0.15s;
        }
        .mp-modal-close:hover {
            background: #e2eae5;
            color: var(--mp-text);
        }
        .mp-modal-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid var(--mp-border);
            margin: 20px 0 18px;
        }
        .mp-modal-tab-btn {
            background: transparent;
            border: 0;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 700;
            color: var(--mp-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: all 0.15s;
        }
        .mp-modal-tab-btn.active {
            color: var(--mp-brand);
            border-color: var(--mp-brand);
        }
        .mp-modal-pane {
            display: none;
            font-size: 13px;
            line-height: 1.6;
            color: #3b4c46;
        }
        .mp-modal-pane.active {
            display: block;
        }
        .mp-code-box {
            background: #112620;
            color: #66ddae;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
        }
        .mp-code-box code {
            overflow-x: auto;
        }
        .mp-btn-copy-code {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 0;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
        }
        .mp-btn-copy-code:hover {
            background: rgba(255,255,255,0.22);
        }

        /* Toast notification */
        .mp-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #112620;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            z-index: 2000;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mp-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .mp-toast svg {
            color: #66ddae;
        }

        /* Responsive Breakpoints */
        @media (max-width: 960px) {
            .mp-grid-layout {
                grid-template-columns: 1fr;
            }
            .mp-ratings-summary {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .mp-bar-label {
                width: 32px;
            }
            .mp-hero {
                padding: 24px;
            }
            .mp-hero-top {
                flex-direction: column;
                gap: 16px;
            }
            .mp-hero h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>

    <!-- Standard RozeHub Topbar -->
    <header class="topbar mp-shell" style="padding-bottom: 0;">
        <a class="brand" href="{{ route('hub') }}">
            <span class="brand-logo">
                <img src="{{ asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2' }}" alt="RozeHub">
            </span>
            <span>RozeHub</span>
        </a>
        <nav>
            <a href="{{ route('hub') }}">Explore</a>
            <a class="active" href="{{ route('marketplace.index') }}">Extensions</a>
            <a href="{{ route('hub') }}#releases">Releases</a>
            <a href="{{ route('docs.index') }}">Documentation</a>
            <a href="{{ route('hub') }}#community">Community</a>
        </nav>
        @auth
            <a class="studio-link" href="{{ route('developer.dashboard') }}">Developer Portal <span>↗</span></a>
        @else
            <a class="studio-link" href="{{ route('admin.login') }}">Admin login <span>↗</span></a>
        @endauth
    </header>

    <main class="mp-shell">
        @php
            $compatTargets = [];
            if (is_array($item->compatibility)) {
                $compatTargets = $item->compatibility['targets'] ?? (array_is_list($item->compatibility) ? $item->compatibility : []);
            }
            $latestRelease = $item->releases->first();
            $publisherName = $item->owner?->publisherProfile?->display_name ?? ($item->vendor ?: 'Lumina Community');
            $ratingAvg = (float)($rating['average'] ?? 0);
            $ratingCnt = (int)($rating['count'] ?? 0);
            $dist = $rating['distribution'] ?? [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
        @endphp

        <!-- Breadcrumb Navigation -->
        <div class="mp-breadcrumb-bar">
            <nav class="mp-breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ route('hub') }}">RozeHub</a>
                <span class="sep">/</span>
                <a href="{{ route('marketplace.index') }}">Extensions</a>
                <span class="sep">/</span>
                <a href="{{ route('marketplace.index', ['project' => $item->project->slug]) }}">{{ $item->project->name }}</a>
                <span class="sep">/</span>
                <span class="current">{{ $item->name }}</span>
            </nav>
            <a href="{{ route('marketplace.index', ['project' => $item->project->slug]) }}" class="mp-back-link">
                ← All {{ $item->project->name }} Extensions
            </a>
        </div>

        @if(session('success'))
            <div style="background:#eaf7f1; border:1px solid #b7e6d2; color:#0e6245; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Extension Hero Card -->
        <section class="mp-hero">
            <div class="mp-hero-top">
                <!-- Visual Icon -->
                <div class="mp-hero-icon-wrapper">
                    <div class="mp-hero-icon">
                        @if($item->icon_path)
                            <img src="{{ asset('storage/'.$item->icon_path) }}" alt="{{ $item->name }}">
                        @elseif($item->item_type === 'theme' || str_contains(strtolower($item->name), 'theme'))
                            <!-- High fidelity IntelliJ Clean White Theme glyph -->
                            <div class="mp-theme-preview-icon" title="IntelliJ Clean White Light Theme">
                                <div class="theme-icon-bar">
                                    <div class="theme-dot red"></div>
                                    <div class="theme-dot yellow"></div>
                                    <div class="theme-dot green"></div>
                                </div>
                                <div class="theme-code-lines">
                                    <div class="theme-line theme-line-1"></div>
                                    <div class="theme-line theme-line-2"></div>
                                    <div class="theme-line theme-line-3"></div>
                                    <div class="theme-line theme-line-4"></div>
                                </div>
                                <span class="theme-accent-badge">LIGHT</span>
                            </div>
                        @else
                            <img src="{{ asset('images/projects/'.$item->project->slug.'.png') }}" alt="{{ $item->name }}">
                        @endif
                    </div>
                </div>

                <!-- Hero Content -->
                <div class="mp-hero-content">
                    <div class="mp-badges-row">
                        <span class="mp-badge mp-badge-type">{{ str_replace('-', ' ', ucfirst($item->item_type)) }}</span>
                        <a href="{{ route('marketplace.index', ['project' => $item->project->slug]) }}" class="mp-badge mp-badge-project">
                            <img src="{{ asset('images/projects/'.$item->project->slug.'.png') }}" alt="">
                            {{ $item->project->name }}
                        </a>
                        @if($item->is_official)
                            <span class="mp-badge mp-badge-official">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Official
                            </span>
                        @endif
                        @if($item->is_verified)
                            <span class="mp-badge mp-badge-verified">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                Verified
                            </span>
                        @endif
                        @if($latestRelease)
                            <span class="mp-badge" style="background:#f0f3f2; color:#31453f; border:1px solid #d5ded9;">v{{ $latestRelease->version }}</span>
                        @endif
                    </div>

                    <h1>{{ $item->name }}</h1>

                    <div class="mp-hero-meta-row">
                        <div>
                            By <span class="mp-meta-vendor">{{ $publisherName }}</span>
                        </div>
                        <div class="mp-plugin-id-tag">
                            ID: <code>{{ $item->item_id }}</code>
                            <button type="button" class="mp-btn-copy-mini" onclick="copyText('{{ $item->item_id }}', 'Plugin ID copied to clipboard!')" title="Copy plugin ID">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            </button>
                        </div>
                        <div class="mp-meta-stat">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>{{ number_format($item->downloads_count) }} downloads</span>
                        </div>
                        <div class="mp-meta-stat">
                            <span class="mp-stars">
                                {{ str_repeat('★', (int)round($ratingAvg)) }}{{ str_repeat('☆', 5 - (int)round($ratingAvg)) }}
                            </span>
                            <strong>{{ number_format($ratingAvg, 1) }}</strong>
                            <span style="color:var(--mp-muted);">({{ $ratingCnt }})</span>
                        </div>
                    </div>

                    <p class="mp-hero-summary">{{ $item->summary }}</p>

                    <!-- Hero Action Buttons -->
                    <div class="mp-hero-actions">
                        @if($latestRelease)
                            <a href="{{ route('api.marketplace.download', $latestRelease) }}" class="mp-btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <span>Download JAR</span>
                                <span class="subtext">v{{ $latestRelease->version }} · {{ $latestRelease->file_size ? number_format($latestRelease->file_size / 1024, 1).' KB' : 'JAR' }}</span>
                            </a>
                        @endif

                        <button type="button" class="mp-btn-secondary" onclick="toggleModal(true)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            Install in {{ $item->project->name }}
                        </button>

                        @if($item->website)
                            <a href="{{ $item->website }}" target="_blank" rel="noopener" class="mp-btn-link">
                                Website ↗
                            </a>
                        @endif
                        @if($item->repository_url)
                            <a href="{{ $item->repository_url }}" target="_blank" rel="noopener" class="mp-btn-link">
                                Source Code ↗
                            </a>
                        @endif
                        @if($item->support_url)
                            <a href="{{ $item->support_url }}" target="_blank" rel="noopener" class="mp-btn-link">
                                Support ↗
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Two-Column Grid -->
        <div class="mp-grid-layout">
            <!-- Left Main Column (68%) -->
            <div class="mp-main-col">

                <!-- In-IDE Quick Install Banner -->
                <div class="mp-install-callout">
                    <div class="mp-install-callout-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    </div>
                    <div class="mp-install-callout-body">
                        <h3>Install directly in {{ $item->project->name }} IDE</h3>
                        <p>No manual downloads required. You can search, install, and activate this extension seamlessly right inside the {{ $item->project->name }} user interface.</p>
                        <div class="mp-install-steps">
                            <span class="mp-step-pill"><span class="mp-step-num">1</span> Open Settings (<kbd style="font-size:10px; background:#f4f6f5; padding:1px 4px; border:1px solid #d2ded7; border-radius:3px;">Ctrl+,</kbd>)</span>
                            <span class="mp-step-pill"><span class="mp-step-num">2</span> Go to Plugins → Marketplace</span>
                            <span class="mp-step-pill"><span class="mp-step-num">3</span> Search &ldquo;{{ $item->name }}&rdquo;</span>
                            <span class="mp-step-pill"><span class="mp-step-num">4</span> Click Install & Restart IDE</span>
                        </div>
                    </div>
                </div>

                <!-- Ecosystem Profile Box -->
                @if($item->project->ecosystemProfile)
                    <div class="mp-card" style="background:#f9fbf9; border-left:4px solid #2b8a73;">
                        <h3 style="margin:0 0 8px; font-size:16px; color:#124135;">{{ $item->project->ecosystemProfile->title }}</h3>
                        <p style="margin:0; font-size:13px; line-height:1.6; color:#4a5e57;">{{ $item->project->ecosystemProfile->description }}</p>
                    </div>
                @endif

                <!-- Overview Section -->
                <section class="mp-card">
                    <div class="mp-section-title">Overview</div>
                    <div class="mp-description-body">
                        {!! nl2br(e($item->description)) !!}
                    </div>
                </section>

                <!-- Capabilities & Permissions -->
                @if($item->capabilities || $item->permissions)
                    <section class="mp-card">
                        <div class="mp-section-title">
                            Capabilities & Permissions
                            <span class="count-badge">{{ count($item->capabilities ?? []) + count($item->permissions ?? []) }} declared</span>
                        </div>
                        <div class="mp-cap-grid">
                            @foreach($item->capabilities ?? [] as $cap)
                                <div class="mp-cap-item">
                                    <div class="mp-cap-icon">
                                        @if(str_contains($cap, 'theme') || str_contains($cap, 'color'))
                                            🎨
                                        @elseif(str_contains($cap, 'syntax') || str_contains($cap, 'editor'))
                                            📝
                                        @elseif(str_contains($cap, 'lang') || str_contains($cap, 'lsp'))
                                            ⚡
                                        @else
                                            🧩
                                        @endif
                                    </div>
                                    <div class="mp-cap-text">
                                        <strong>
                                            @if($cap === 'theme.light')
                                                Light Theme Palette
                                            @elseif($cap === 'editor.syntax')
                                                Syntax Highlighting
                                            @elseif($cap === 'ui.theme')
                                                UI Theme Engine
                                            @else
                                                {{ ucfirst(str_replace(['.', '-'], ' ', $cap)) }}
                                            @endif
                                        </strong>
                                        <code>{{ $cap }}</code>
                                        <p>
                                            @if($cap === 'theme.light')
                                                Provides a clean, bright color palette optimized for day-lit environments.
                                            @elseif($cap === 'editor.syntax')
                                                Customizes editor syntax tokens for keywords, strings, comments, and variables.
                                            @elseif($cap === 'ui.theme')
                                                Modifies window canvas, toolbars, sidebars, tabs, and border styling.
                                            @else
                                                Standard extension capability registered with RozeHub.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            @foreach($item->permissions ?? [] as $perm)
                                <div class="mp-cap-item" style="border-color:#e4e2f5; background:#faf9fe;">
                                    <div class="mp-cap-icon" style="background:#edeafc; color:#6b21a8;">
                                        🔒
                                    </div>
                                    <div class="mp-cap-text">
                                        <strong>UI & Theme Permission</strong>
                                        <code style="color:#6b21a8; background:#edeafc;">{{ $perm }}</code>
                                        <p>Requires permission to adjust application UI themes and workspace styling.</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Integrations & Compatibility -->
                @if(!empty($compatTargets))
                    <section class="mp-card">
                        <div class="mp-section-title">Integrations & Compatibility</div>
                        <p style="margin:0 0 14px; font-size:13px; color:var(--mp-muted);">This extension has been verified for compatibility with the following {{ $item->project->name }} target versions:</p>
                        <div class="mp-compat-pills">
                            @foreach($compatTargets as $target)
                                <span class="mp-compat-pill">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    {{ $item->project->name }} v{{ $target }}+
                                </span>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Versions & Release History -->
                <section class="mp-card">
                    <div class="mp-section-title">
                        Versions & Release History
                        <span class="count-badge">{{ $item->releases->count() }} {{ Str::plural('version', $item->releases->count()) }}</span>
                    </div>

                    @forelse($item->releases as $release)
                        <div class="mp-version-card">
                            <div class="mp-version-header">
                                <div class="mp-version-title">
                                    <strong>v{{ $release->version }}</strong>
                                    <span class="mp-channel-badge">{{ $release->channel }}</span>
                                    <span class="mp-version-date">
                                        Published {{ $release->published_at ? $release->published_at->format('M j, Y') : $release->created_at->format('M j, Y') }}
                                    </span>
                                </div>
                                <a href="{{ route('api.marketplace.download', $release) }}" class="mp-btn-secondary" style="padding: 7px 14px; font-size: 13px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    Download ({{ $release->file_size ? number_format($release->file_size / 1024, 1).' KB' : 'JAR' }})
                                </a>
                            </div>

                            <div class="mp-version-meta-tags">
                                <span class="mp-meta-chip">Platform: {{ $release->platform }}</span>
                                <span class="mp-meta-chip">Architecture: {{ $release->architecture }}</span>
                                <span class="mp-meta-chip">Format: {{ strtoupper($release->package_type) }}</span>
                                @if($release->minimum_app_version || $release->maximum_app_version)
                                    <span class="mp-meta-chip">
                                        Compatible: {{ $release->minimum_app_version ?: 'Any' }} → {{ $release->maximum_app_version ?: 'Latest' }}
                                    </span>
                                @endif
                            </div>

                            @if($release->release_notes)
                                <div class="mp-version-notes">
                                    {{ $release->release_notes }}
                                </div>
                            @endif

                            @if($release->sha256)
                                <div class="mp-checksum-row">
                                    <span style="font-weight:700; color:var(--mp-muted);">SHA-256 Checksum:</span>
                                    <span class="mp-checksum-code">{{ $release->sha256 }}</span>
                                    <button type="button" class="mp-btn-copy-hash" onclick="copyText('{{ $release->sha256 }}', 'SHA-256 hash copied to clipboard!')">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        Copy
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="color:var(--mp-muted); font-size:14px; margin:0;">No published versions yet.</p>
                    @endforelse
                </section>

                <!-- Ratings & Reviews Section -->
                <section class="mp-card">
                    <div class="mp-section-title">
                        Ratings & Reviews
                        <span class="count-badge">{{ $ratingCnt }} {{ Str::plural('review', $ratingCnt) }}</span>
                    </div>

                    <div class="mp-ratings-summary">
                        <div class="mp-score-box">
                            <div class="mp-score-number">{{ number_format($ratingAvg, 1) }}</div>
                            <div class="mp-score-stars">
                                {{ str_repeat('★', (int)round($ratingAvg)) }}{{ str_repeat('☆', 5 - (int)round($ratingAvg)) }}
                            </div>
                            <div class="mp-score-count">{{ $ratingCnt }} community {{ Str::plural('rating', $ratingCnt) }}</div>
                        </div>

                        <div class="mp-score-bars">
                            @for($s = 5; $s >= 1; $s--)
                                @php
                                    $starCount = $dist[$s] ?? 0;
                                    $pct = $ratingCnt > 0 ? round(($starCount / $ratingCnt) * 100) : 0;
                                @endphp
                                <div class="mp-bar-row">
                                    <span class="mp-bar-label">{{ $s }} ★</span>
                                    <div class="mp-bar-track">
                                        <div class="mp-bar-fill" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <span class="mp-bar-count">{{ $starCount }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Reviews List -->
                    @forelse($item->marketplaceReviews as $review)
                        <div class="mp-review-card">
                            <div class="mp-review-header">
                                <div class="mp-reviewer-info">
                                    <div class="mp-avatar">
                                        {{ strtoupper(substr($review->user->name ?? 'User', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="mp-reviewer-name">{{ $review->user->name ?? 'Community Member' }}</span>
                                        <div style="font-size:11px; color:var(--mp-muted);">
                                            {{ $review->created_at ? $review->created_at->format('M j, Y') : 'Verified User' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="mp-stars" style="font-size:13px;">
                                    {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                </span>
                            </div>
                            @if($review->title)
                                <h4 class="mp-review-title">{{ $review->title }}</h4>
                            @endif
                            <p class="mp-review-body">{{ $review->body }}</p>
                        </div>
                    @empty
                        <p style="color:var(--mp-muted); font-size:13px; margin:0 0 16px;">
                            No reviews yet for this extension. Be the first to share your experience!
                        </p>
                    @endforelse

                    <!-- Review Form -->
                    @auth
                        <form method="POST" action="{{ route('marketplace.reviews.store', $item) }}" class="mp-review-form-card">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="reviewRatingInput" name="rating" value="5">

                            <h3>Write a Review</h3>
                            <p>Share your feedback about this extension to help other developers in the community.</p>

                            <div class="mp-form-group">
                                <label>Your Rating</label>
                                <div class="mp-star-picker" id="starPicker">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="mp-star-btn active" data-rating="{{ $i }}" onclick="selectRating({{ $i }})">★</button>
                                    @endfor
                                    <span class="mp-star-rating-label" id="starRatingText">5 out of 5 stars</span>
                                </div>
                            </div>

                            <div class="mp-form-group">
                                <label for="reviewTitle">Review Title</label>
                                <input type="text" id="reviewTitle" name="title" class="mp-form-input" placeholder="e.g. Crisp, clear, and high-contrast IntelliJ theme" maxlength="180">
                            </div>

                            <div class="mp-form-group">
                                <label for="reviewBody">Detailed Review</label>
                                <textarea id="reviewBody" name="body" class="mp-form-textarea" rows="3" placeholder="Tell us what you like, syntax coloring experience, or suggestions for the developer..."></textarea>
                            </div>

                            <button type="submit" class="mp-btn-submit-review">Publish Review →</button>
                        </form>
                    @else
                        <div class="mp-guest-review-box">
                            <h4 style="margin:0 0 6px; font-size:15px; color:var(--mp-text);">Have you tested this extension?</h4>
                            <p>Sign in to your RozeHub account to leave a star rating and review.</p>
                            <a href="{{ route('admin.login') }}" class="mp-btn-secondary" style="font-size:13px;">Sign In to Review →</a>
                        </div>
                    @endauth
                </section>

            </div>

            <!-- Right Sidebar (32%) -->
            <aside class="mp-sidebar">

                <!-- Metadata Card -->
                <div class="mp-side-card">
                    <div class="mp-side-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        Extension Information
                    </div>
                    <div class="mp-meta-table">
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Unique Identifier</span>
                            <span class="mp-meta-val"><code>{{ $item->item_id }}</code></span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Target IDE</span>
                            <span class="mp-meta-val">{{ $item->project->name }}</span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Type</span>
                            <span class="mp-meta-val">{{ ucfirst($item->item_type) }}</span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Category</span>
                            <span class="mp-meta-val">{{ $item->category ?: 'General' }}</span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Package Format</span>
                            <span class="mp-meta-val">{{ $latestRelease ? strtoupper($latestRelease->package_type) : 'JAR' }}</span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Total Downloads</span>
                            <span class="mp-meta-val">{{ number_format($item->downloads_count) }}</span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Latest Version</span>
                            <span class="mp-meta-val">{{ $latestRelease ? 'v'.$latestRelease->version : 'v1.0.0' }}</span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">First Released</span>
                            <span class="mp-meta-val">{{ $item->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="mp-meta-row">
                            <span class="mp-meta-label">Last Updated</span>
                            <span class="mp-meta-val">{{ $item->updated_at->format('M j, Y') }}</span>
                        </div>
                        @if($item->license)
                            <div class="mp-meta-row">
                                <span class="mp-meta-label">License</span>
                                <span class="mp-meta-val">{{ $item->license }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Publisher Card -->
                <div class="mp-side-card">
                    <div class="mp-side-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Publisher
                    </div>
                    <div class="mp-publisher-box">
                        <div class="mp-pub-avatar">
                            {{ strtoupper(substr($publisherName, 0, 1)) }}
                        </div>
                        <div class="mp-pub-info">
                            <strong>{{ $publisherName }}</strong>
                            @if($item->is_verified || ($item->owner?->publisherProfile?->is_verified ?? false))
                                <span style="font-size:11px; color:#1d4ed8; font-weight:600;">Verified Publisher</span>
                            @else
                                <span style="font-size:11px; color:var(--mp-muted);">Community Contributor</span>
                            @endif
                        </div>
                    </div>
                    @if($item->owner?->publisherProfile?->bio)
                        <p class="mp-pub-bio">{{ $item->owner->publisherProfile->bio }}</p>
                    @else
                        <p class="mp-pub-bio">Developer and open-source contributor developing tools and extensions for the RozeHub ecosystem.</p>
                    @endif
                    <div class="mp-pub-links">
                        @if($item->owner?->publisherProfile?->github_url)
                            <a href="{{ $item->owner->publisherProfile->github_url }}" target="_blank" rel="noopener" class="mp-pub-link">
                                GitHub Profile ↗
                            </a>
                        @endif
                        @if($item->owner?->publisherProfile?->website)
                            <a href="{{ $item->owner->publisherProfile->website }}" target="_blank" rel="noopener" class="mp-pub-link">
                                Publisher Website ↗
                            </a>
                        @endif
                        <a href="{{ route('marketplace.index', ['project' => $item->project->slug]) }}" class="mp-pub-link">
                            More from this ecosystem →
                        </a>
                    </div>
                </div>

                <!-- Ecosystem Card -->
                <div class="mp-eco-card">
                    <strong>{{ $item->project->name }} Ecosystem</strong>
                    <p>RozeHub manages native extensions, themes, and toolchains for {{ $item->project->name }}. Browse other approved community packages.</p>
                    <a href="{{ route('marketplace.index', ['project' => $item->project->slug]) }}" class="mp-eco-link">
                        Explore {{ $item->project->name }} extensions →
                    </a>
                </div>

            </aside>
        </div>
    </main>

    <!-- Site Footer -->
    <footer class="site-footer mp-shell">
        <a class="brand" href="{{ route('hub') }}">
            <span class="brand-logo">
                <img src="{{ asset('images/rozehub-ecosystem.png').'?v=20260828-professional-logo-2' }}" alt="RozeHub">
            </span>
            <span>RozeHub</span>
        </a>
        <p>Software made with intent. Built by Firoze Hossain.</p>
        <a href="{{ route('admin.login') }}">Admin studio →</a>
    </footer>

    <!-- Installation Modal -->
    <div class="mp-modal-backdrop" id="installModal" onclick="if(event.target===this) toggleModal(false)">
        <div class="mp-modal">
            <button type="button" class="mp-modal-close" onclick="toggleModal(false)" aria-label="Close">✕</button>

            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; border-radius:10px; background:#0c2923; color:#66ddae; display:flex; align-items:center; justify-content:center; font-size:18px;">
                    ⚡
                </div>
                <div>
                    <h3 style="margin:0; font-size:18px; font-weight:800; color:var(--mp-text);">Install {{ $item->name }}</h3>
                    <span style="font-size:12px; color:var(--mp-muted);">For {{ $item->project->name }} IDE</span>
                </div>
            </div>

            <div class="mp-modal-tabs">
                <button type="button" class="mp-modal-tab-btn active" id="tabBtn1" onclick="switchModalTab('gui')">In-IDE Marketplace</button>
                <button type="button" class="mp-modal-tab-btn" id="tabBtn2" onclick="switchModalTab('disk')">Install from Disk (JAR)</button>
            </div>

            <!-- Tab 1: GUI -->
            <div class="mp-modal-pane active" id="paneGui">
                <ol style="padding-left:18px; margin:0 0 16px; line-height:1.8;">
                    <li>Open <strong>{{ $item->project->name }} IDE</strong>.</li>
                    <li>Open <strong>Settings</strong> (<kbd>Ctrl + ,</kbd> or <kbd>Cmd + ,</kbd>).</li>
                    <li>Select <strong>Plugins</strong> from the left sidebar and switch to the <strong>Marketplace</strong> tab.</li>
                    <li>Search for <strong>{{ $item->name }}</strong> or ID <code>{{ $item->item_id }}</code>.</li>
                    <li>Click <strong>Install</strong>, then click <strong>Restart IDE</strong> to activate.</li>
                </ol>
                <div class="mp-code-box">
                    <code>Plugin ID: {{ $item->item_id }}</code>
                    <button type="button" class="mp-btn-copy-code" onclick="copyText('{{ $item->item_id }}', 'Plugin ID copied!')">Copy ID</button>
                </div>
            </div>

            <!-- Tab 2: Disk -->
            <div class="mp-modal-pane" id="paneDisk">
                <ol style="padding-left:18px; margin:0 0 16px; line-height:1.8;">
                    @if($latestRelease)
                        <li>Download <a href="{{ route('api.marketplace.download', $latestRelease) }}" style="color:var(--mp-brand); font-weight:700;">{{ $latestRelease->file_name ?: $item->slug.'.jar' }}</a>.</li>
                    @endif
                    <li>In {{ $item->project->name }}, go to <strong>Settings → Plugins</strong>.</li>
                    <li>Click the gear icon ⚙ or <strong>Install Plugin from Disk…</strong>.</li>
                    <li>Select the downloaded <code>.jar</code> file and restart the IDE.</li>
                </ol>
                @if($latestRelease && $latestRelease->sha256)
                    <div class="mp-code-box">
                        <code style="font-size:10px;">SHA-256: {{ substr($latestRelease->sha256, 0, 32) }}...</code>
                        <button type="button" class="mp-btn-copy-code" onclick="copyText('{{ $latestRelease->sha256 }}', 'SHA-256 copied!')">Copy Checksum</button>
                    </div>
                @endif
            </div>

            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="mp-btn-primary" onclick="toggleModal(false)" style="padding:8px 16px; font-size:13px;">Done</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="mp-toast" id="mpToast">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span id="mpToastMsg">Copied to clipboard!</span>
    </div>

    <!-- Interactive Scripts -->
    <script>
        function toggleModal(show) {
            const m = document.getElementById('installModal');
            if (show) {
                m.classList.add('is-active');
            } else {
                m.classList.remove('is-active');
            }
        }

        function switchModalTab(tab) {
            const btn1 = document.getElementById('tabBtn1');
            const btn2 = document.getElementById('tabBtn2');
            const paneGui = document.getElementById('paneGui');
            const paneDisk = document.getElementById('paneDisk');

            if (tab === 'gui') {
                btn1.classList.add('active');
                btn2.classList.remove('active');
                paneGui.classList.add('active');
                paneDisk.classList.remove('active');
            } else {
                btn2.classList.add('active');
                btn1.classList.remove('active');
                paneDisk.classList.add('active');
                paneGui.classList.remove('active');
            }
        }

        function copyText(text, msg) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => showToast(msg));
            } else {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    showToast(msg);
                } catch (e) {
                    console.error('Copy failed', e);
                }
                document.body.removeChild(ta);
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('mpToast');
            const msgEl = document.getElementById('mpToastMsg');
            msgEl.textContent = msg || 'Copied to clipboard!';
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }

        function selectRating(val) {
            document.getElementById('reviewRatingInput').value = val;
            const buttons = document.querySelectorAll('#starPicker .mp-star-btn');
            buttons.forEach(btn => {
                const r = parseInt(btn.getAttribute('data-rating'), 10);
                if (r <= val) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            const textEl = document.getElementById('starRatingText');
            if (textEl) {
                textEl.textContent = val + ' out of 5 stars';
            }
        }

        // Close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                toggleModal(false);
            }
        });
    </script>
</body>
</html>
