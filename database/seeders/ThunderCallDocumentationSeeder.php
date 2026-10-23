<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThunderCallDocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $project = DB::table('software_projects')->where('slug', 'thundercall')->first();
        if (!$project) {
            $this->command->error("ThunderCall project not found in database.");
            return;
        }

        $projectId = $project->id;

        $sections = [
            [
                'title' => 'Getting Started',
                'slug' => 'getting-started',
                'description' => 'Everything you need to install, launch, and master ThunderCall.',
                'icon' => '🚀',
                'sort_order' => 10,
                'pages' => [
                    [
                        'title' => 'Welcome to ThunderCall',
                        'slug' => 'welcome-to-thundercall',
                        'kind' => 'overview',
                        'summary' => 'A calm, modern, and high-performance API client built with JavaFX and Spring Boot.',
                        'content' => <<<'MD'
# Welcome to ThunderCall

ThunderCall is a modern, high-performance API development and testing platform designed for developers who value speed, precision, and a calm, responsive workflow.

## Why ThunderCall?

Traditional API clients have grown heavy, slow to launch, and resource-intensive. ThunderCall combines a native **JavaFX 21** desktop experience with a reactive **Spring Boot** backend engine to deliver:

- **Instant Startup**: Zero Electron overhead, sub-second cold starts, and minimal memory footprint.
- **Postman Compatibility**: Native import and export for Postman Collection v2.1 format and environment files.
- **Multi-Protocol Support**: First-class handling for REST HTTP/1.1 & HTTP/2, WebSockets, Socket.IO, and GraphQL with live schema introspection.
- **Local-First & Team-Enabled**: Work completely offline with local SQLite/H2 persistence, or collaborate in real time with shared team workspaces.

## System Architecture

ThunderCall operates as a paired desktop architecture:

```text
+-----------------------------------------------------------+
|               ThunderCall JavaFX Desktop UI               |
|  - Tabbed request editor (RichTextFX syntax highlighting) |
|  - Real-time response viewer, folding & visualization     |
|  - Dynamic {{variable}} autocomplete & environment sync   |
+-----------------------------+-----------------------------+
                              | High-speed IPC / HTTP
+-----------------------------v-----------------------------+
|              ThunderCall Spring Boot Engine               |
|  - Reactive HTTP client & WebSocket multiplexer           |
|  - Mock server engine & dynamic route matcher             |
|  - Automated cron monitor execution service               |
|  - Postman v2.1 script runner & assertion sandbox         |
+-----------------------------------------------------------+
```
MD
                    ],
                    [
                        'title' => 'Quick Start Guide',
                        'slug' => 'quick-start-guide',
                        'kind' => 'guide',
                        'summary' => 'Build, send, and inspect your first API request in under 60 seconds.',
                        'content' => <<<'MD'
# Quick Start Guide

Get started with ThunderCall by sending your first API request.

## Step 1: Open a Request Tab

Press `Ctrl+T` (or `Cmd+T` on macOS), or click the **+** button in the tab bar. A new tab named `Untitled Request` will open with the default HTTP method set to `GET`.

## Step 2: Enter the Endpoint URL

Type or paste an API endpoint into the URL field. For testing, you can use:

```text
https://jsonplaceholder.typicode.com/posts/1
```

> **Tip**: You can use variables inside the URL like `{{baseUrl}}/posts/1`. Press `{{` in the URL bar to trigger the variable autocomplete dropdown.

## Step 3: Send and Inspect the Response

Click the **Send** button (or press `Ctrl+Enter`).

ThunderCall immediately sends the request and displays:
- **Status Code**: e.g., `200 OK` with color-coded status badges.
- **Response Time**: Total round-trip latency in milliseconds.
- **Payload Size**: Exact byte count of the response body.
- **Body Viewer**: Fully formatted JSON with syntax highlighting, line numbers, search (`Ctrl+F`), and code folding.

```json
{
  "userId": 1,
  "id": 1,
  "title": "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
  "body": "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum..."
}
```

## Step 4: Save to a Collection

Press `Ctrl+S` to save your request. Name the request (e.g., `Get First Post`) and select a collection or create a new one to keep your workspace organized.
MD
                    ],
                    [
                        'title' => 'Installation & Setup',
                        'slug' => 'installation-and-setup',
                        'kind' => 'guide',
                        'summary' => 'System requirements, platform packages, and development build instructions.',
                        'content' => <<<'MD'
# Installation & Setup

ThunderCall is distributed as a cross-platform desktop application supporting Linux, Windows, and macOS.

## System Requirements

- **Operating System**: Linux (x86_64 or aarch64), Windows 10/11 (64-bit), or macOS 12+ (Intel & Apple Silicon).
- **Java Runtime**: OpenJDK 21 LTS or later.
- **Memory**: Minimum 512 MB RAM (1 GB recommended).
- **Disk Space**: ~150 MB for the application binaries.

## Running from Source

You can clone and launch ThunderCall directly using Maven:

```bash
git clone https://github.com/firoze-hossain/thundercall.git
cd thundercall

# Compile both backend and frontend modules
mvn clean compile -DskipTests

# Run the desktop client
mvn javafx:run -f thundercall-frontend/pom.xml
```

## Configuration & User Data

ThunderCall stores local session state, cached responses, and offline collections safely in your user home directory:

- Linux/macOS: `~/.thundercall/`
- Windows: `%USERPROFILE%\.thundercall\`
MD
                    ],
                    [
                        'title' => 'Navigating the Workspace',
                        'slug' => 'navigating-the-workspace',
                        'kind' => 'reference',
                        'summary' => 'Explore the menu bar, sidebar rail, request tabs, and dual-pane views.',
                        'content' => <<<'MD'
# Navigating the Workspace

ThunderCall's user interface is optimized for high-density information display without visual clutter.

## 1. Application Menu Bar
- **File**: Create tabs (`Ctrl+T`), close tabs (`Ctrl+W`), manage collections, import/export, and open settings.
- **Edit**: Undo, Redo, Cut, Copy, Paste, and selection helpers.
- **View**: Toggle full screen (`F11`), zoom in/out, toggle dual-pane layout (`Alt+Ctrl+V`), and toggle sidebar (`Ctrl+\`).
- **Help**: Clear Cache and Reload, Documentation, Support Center, Check for Updates, and About ThunderCall.

## 2. The Sidebar Rail
The left icon rail gives one-click access to core subsystems:
- **Collections**: Hierarchical tree of requests and folders.
- **Environments**: Variable scopes and active environment selector.
- **History**: Chronological log of sent requests with status codes and instant re-send.
- **Teams**: Team workspaces and member role management.
- **Mock Servers**: Configurable simulated endpoints.
- **Monitors**: Scheduled uptime and health check runners.

## 3. Two-Pane Layout
Press `Alt+Ctrl+V` to toggle between horizontal split (editor on top, response on bottom) and vertical side-by-side view, ideal for wide ultra-wide monitors.
MD
                    ]
                ]
            ],
            [
                'title' => 'Building Requests',
                'slug' => 'building-requests',
                'description' => 'Configuring HTTP methods, URL parameters, headers, body formats, and protocols.',
                'icon' => '⚡',
                'sort_order' => 20,
                'pages' => [
                    [
                        'title' => 'HTTP Methods and Dynamic URLs',
                        'slug' => 'http-methods-and-urls',
                        'kind' => 'guide',
                        'summary' => 'Standard HTTP verbs, bidirectional query param synchronization, and URL decoding.',
                        'content' => <<<'MD'
# HTTP Methods and Dynamic URLs

ThunderCall supports all standard HTTP verbs: `GET`, `POST`, `PUT`, `DELETE`, `PATCH`, `HEAD`, and `OPTIONS`.

## Method Dropdown & Color Coding
Each method is color-coded to match industry standards:
- `GET`: Green
- `POST`: Orange
- `PUT`: Blue
- `DELETE`: Red
- `PATCH`: Purple

## Two-Way URL & Query Parameter Sync
Editing query parameters in the URL automatically populates the **Params** key-value table. Conversely, modifying keys, values, or toggling checkboxes in the Params table immediately updates the URL query string in real time.

Special characters are automatically URL-encoded using UTF-8 without mangling double-curly variable references like `{{userId}}`.
MD
                    ],
                    [
                        'title' => 'Query Parameters & Headers',
                        'slug' => 'parameters-and-headers',
                        'kind' => 'guide',
                        'summary' => 'Manage query strings, request headers, presets, and active toggle states.',
                        'content' => <<<'MD'
# Query Parameters & Headers

Headers and parameters in ThunderCall feature bulk editing, descriptions, and active row toggling.

## Key-Value Table Features
- **Checkbox Toggle**: Disable a parameter or header temporarily without deleting it.
- **Description Field**: Document the purpose of each header or parameter; descriptions are saved with the collection.
- **Auto-append Placeholder**: Typing in the bottom blank row automatically creates a new row.
- **Context Menu**: Right-click any row to duplicate, insert above/below, or delete.

## Common Headers
ThunderCall automatically manages headers such as:
- `Content-Type`: Automatically set based on body format (e.g. `application/json`).
- `Accept`: Defaults to `*/*` or `application/json`.
- `User-Agent`: Identifies as `ThunderCall/1.0.0`.
MD
                    ],
                    [
                        'title' => 'Request Body Formats',
                        'slug' => 'request-body-formats',
                        'kind' => 'guide',
                        'summary' => 'Send Raw JSON, Multipart Form-Data, URL-Encoded, and GraphQL requests.',
                        'content' => <<<'MD'
# Request Body Formats

ThunderCall offers specialized editors for every standard HTTP payload format.

## 1. Raw (JSON, Text, XML, HTML)
- **Syntax Highlighting**: Real-time coloring for JSON keys, strings, numbers, booleans, and nulls.
- **JSON Formatting**: Auto-format and prettify messy JSON with proper indentation.
- **Code Folding**: Collapse nested JSON objects and arrays using the gutter arrows.

## 2. Form-Data (`multipart/form-data`)
Ideal for file uploads and form submissions:
- Key-Value pairs with a Type dropdown: choose between **Text** or **File**.
- File rows include a native file picker button showing the selected file path and size.

## 3. x-www-form-urlencoded
Key-value pairs encoded as URL query format, commonly used in OAuth token exchange endpoints.

## 4. GraphQL
Dedicated GraphQL editor with dual panes:
- **Query Editor**: Syntax highlighted GraphQL query/mutation.
- **Variables Editor**: Separate JSON editor for GraphQL variables.
- **Auto Schema Introspection**: Fetches and caches the server's GraphQL schema to provide autocomplete.
MD
                    ],
                    [
                        'title' => 'Authentication & Security',
                        'slug' => 'authentication-and-security',
                        'kind' => 'guide',
                        'summary' => 'Configure Bearer tokens, Basic Auth, API Keys, and collection-level auth inheritance.',
                        'content' => <<<'MD'
# Authentication & Security

ThunderCall makes configuring API authentication straightforward and secure.

## Supported Auth Types
- **No Auth**: Default for public endpoints.
- **Bearer Token**: Automatically injects the `Authorization: Bearer <token>` header. Supports variable substitution such as `{{jwtToken}}`.
- **Basic Auth**: Enter Username and Password. ThunderCall encodes them into standard `Authorization: Basic <base64>` header upon dispatch.
- **API Key**: Configure a custom header (e.g. `X-API-Key`) or query parameter.

## Collection-Level Inheritance
Requests inside a folder or collection can inherit their authentication configuration from their parent collection, eliminating the need to re-type tokens across hundreds of endpoints.
MD
                    ],
                    [
                        'title' => 'WebSockets & Socket.IO Testing',
                        'slug' => 'websockets-and-socketio',
                        'kind' => 'guide',
                        'summary' => 'Test real-time WebSocket connections and Socket.IO event emission.',
                        'content' => <<<'MD'
# WebSockets & Socket.IO Testing

In addition to traditional REST, ThunderCall includes full-duplex real-time protocol support.

## Native WebSockets
Connect to any `ws://` or `wss://` endpoint:
- Real-time connection status badge (Connected, Connecting, Disconnected).
- Frame stream log: Inspect incoming and outgoing text/binary frames with exact timestamps.
- Send message compose box with JSON syntax formatting.

## Socket.IO
- Connect to Socket.IO servers (v2, v3, v4).
- Custom namespace support (`/chat`, `/notifications`).
- Event Emission: specify event name and JSON payload.
- Event Listener: subscribe to arbitrary server-sent events and view history.
MD
                    ]
                ]
            ],
            [
                'title' => 'Collections & Environments',
                'slug' => 'collections-and-environments',
                'description' => 'Organizing requests into collections and managing dynamic environment variables.',
                'icon' => '🗂',
                'sort_order' => 30,
                'pages' => [
                    [
                        'title' => 'Managing Collections & Folders',
                        'slug' => 'managing-collections',
                        'kind' => 'guide',
                        'summary' => 'Create collections, group endpoints in nested folders, and run test suites.',
                        'content' => <<<'MD'
# Managing Collections & Folders

Collections are the fundamental unit of organization in ThunderCall.

## Creating Collections & Folders
- Click **New Collection** from the top bar or sidebar.
- Right-click any collection to add sub-folders (e.g., `Auth`, `Users`, `Payments`).
- Drag and drop or context-menu to reorder and nest folders to arbitrary depths.

## Collection Runner
Run all requests in a collection sequentially:
- Set iteration count and delay between requests.
- Track real-time execution progress, pass/fail test assertion counts, and total execution time.
MD
                    ],
                    [
                        'title' => 'Environments & Dynamic Variables',
                        'slug' => 'environments-and-variables',
                        'kind' => 'guide',
                        'summary' => 'Manage Global, Environment, and Collection variable scopes with {{variable}} syntax.',
                        'content' => <<<'MD'
# Environments & Dynamic Variables

Environments let you switch between different servers (e.g., `Local`, `Staging`, `Production`) without modifying request URLs.

## Variable Scopes
ThunderCall resolves variables in the following order of precedence:
1. **Local Variables**: Variables set during a single request run.
2. **Environment Variables**: Variables belonging to the currently selected environment (e.g. `{{baseUrl}}`).
3. **Collection Variables**: Variables scoped to a specific collection.
4. **Global Variables**: System-wide fallback variables accessible across all workspaces.

## Using Variables
Wrap the variable name in double curly braces: `{{variable_name}}`.

Variables can be used in:
- URL: `{{baseUrl}}/v1/users/{{userId}}`
- Query Parameters: `page={{defaultPage}}`
- Headers: `Authorization: Bearer {{token}}`
- Request Body: `{"email": "{{userEmail}}"}`
MD
                    ],
                    [
                        'title' => 'Postman v2.1 Import & Export',
                        'slug' => 'postman-import-and-export',
                        'kind' => 'guide',
                        'summary' => 'Seamlessly import collections and environments from Postman or export to JSON.',
                        'content' => <<<'MD'
# Postman v2.1 Import & Export

Migrating to ThunderCall is instantaneous with full Postman schema compatibility.

## Importing
1. In ThunderCall, click **File > Import…** (or the **Import** button in the top bar).
2. Choose your Postman Collection JSON file (v2.0 or v2.1) or Environment JSON file.
3. ThunderCall automatically parses and imports:
   - Request URLs, methods, headers, and descriptions.
   - Body configurations (raw JSON, form-data, urlencoded).
   - Pre-request and test assertion scripts.
   - Folder hierarchies.

## Exporting
1. Right-click any collection in the sidebar and choose **Export as JSON (Postman)**.
2. The generated JSON file conforms strictly to the Postman v2.1 schema, allowing immediate sharing with teammates using other tools.
MD
                    ]
                ]
            ],
            [
                'title' => 'Scripts & Automated Testing',
                'slug' => 'scripts-and-testing',
                'description' => 'Write pre-request scripts and response assertions using JavaScript.',
                'icon' => '🧪',
                'sort_order' => 40,
                'pages' => [
                    [
                        'title' => 'Pre-request Scripts & Setup',
                        'slug' => 'pre-request-scripts',
                        'kind' => 'guide',
                        'summary' => 'Execute JavaScript before request dispatch to compute timestamps, tokens, and hashes.',
                        'content' => <<<'MD'
# Pre-request Scripts & Setup

Pre-request scripts execute before the HTTP request is dispatched to the network.

## Common Use Cases
- Generating dynamic timestamps or nonces.
- Calculating HMAC or MD5 signatures for secured APIs.
- Setting dynamic environment variables based on previous values.

## Example Script
```javascript
// Set a fresh timestamp in milliseconds
pm.environment.set("requestTimestamp", Date.now());

// Generate a random client request ID
const uuid = crypto.randomUUID ? crypto.randomUUID() : "req-" + Math.random().toString(36).substr(2, 9);
pm.environment.set("clientRequestId", uuid);
```
MD
                    ],
                    [
                        'title' => 'Writing Test Scripts & Assertions',
                        'slug' => 'test-scripts-and-assertions',
                        'kind' => 'guide',
                        'summary' => 'Verify status codes, JSON schema, response times, and update environment variables.',
                        'content' => <<<'MD'
# Writing Test Scripts & Assertions

Test scripts run automatically as soon as a response is received from the server.

## Postman-Compatible Assertion Syntax

```javascript
// Test status code
pm.test("Status code is 200 OK", function () {
    pm.response.to.have.status(200);
});

// Test response time
pm.test("Response time is under 500ms", function () {
    pm.expect(pm.response.responseTime).to.be.below(500);
});

// Inspect and validate JSON payload
pm.test("Response body contains valid user profile", function () {
    var data = pm.response.json();
    pm.expect(data).to.be.an("object");
    pm.expect(data.id).to.be.a("number");
    pm.expect(data.email).to.include("@");
    
    // Automatically persist token for subsequent requests
    if (data.token) {
        pm.environment.set("authToken", data.token);
    }
});
```

## Viewing Test Results
The **Tests** tab in the response viewer displays a clear summary:
- Green badge for passed tests (`✓ Pass`).
- Red badge for failed tests (`✗ Fail`) with exact stack trace details.
MD
                    ]
                ]
            ],
            [
                'title' => 'Mocks & API Monitors',
                'slug' => 'mocks-and-monitors',
                'description' => 'Simulate endpoints with mock servers and run periodic health checks with monitors.',
                'icon' => '🎯',
                'sort_order' => 50,
                'pages' => [
                    [
                        'title' => 'Mock Servers & Route Simulation',
                        'slug' => 'mock-servers',
                        'kind' => 'guide',
                        'summary' => 'Build and test before backend APIs exist by simulating mock responses.',
                        'content' => <<<'MD'
# Mock Servers & Route Simulation

ThunderCall includes a built-in mock server engine allowing frontend and mobile developers to work unblocked.

## Creating a Mock Server
1. Click **Mock Servers** in the sidebar rail.
2. Click **+ New Mock Server**.
3. Specify server name, port, and optional collection binding.

## Configuring Simulated Routes
For each mock endpoint, you can configure:
- **HTTP Method & Path**: e.g. `GET /api/v1/orders`.
- **Response Status Code**: `200`, `201`, `400`, `404`, `500`.
- **Simulated Latency**: Test slow network conditions (e.g. 500ms delay).
- **Headers & Response Body**: Return custom JSON payloads or static assets.
MD
                    ],
                    [
                        'title' => 'API Monitors & Scheduled Health Checks',
                        'slug' => 'api-monitors',
                        'kind' => 'guide',
                        'summary' => 'Set up automated background health checks, cron schedules, and alerts.',
                        'content' => <<<'MD'
# API Monitors & Scheduled Health Checks

Monitors run your API collections on a scheduled basis to ensure endpoints remain healthy and fast.

## Setting Up a Monitor
1. Select **Monitors** from the sidebar rail.
2. Choose **Create Monitor** and pick a collection and target environment.
3. Configure recurrence:
   - Pre-sets: Every 5 minutes, hourly, daily, or custom cron expressions.

## Monitoring Metrics
- **Uptime Percentage**: Tracks success rates over 24 hours, 7 days, and 30 days.
- **Latency Trend Charts**: Visual graphs of response time fluctuations.
- **Execution Run History**: Inspect detailed logs of every scheduled run.
MD
                    ]
                ]
            ],
            [
                'title' => 'Troubleshooting & Support',
                'slug' => 'troubleshooting-and-support',
                'description' => 'Clear cache, resolve SSL and connection issues, and access community support.',
                'icon' => '🛠',
                'sort_order' => 60,
                'pages' => [
                    [
                        'title' => 'Clear Cache and Reload Mechanism',
                        'slug' => 'clear-cache-and-reload',
                        'kind' => 'guide',
                        'summary' => 'How Postman and ThunderCall safely clear client caches and resync application state.',
                        'content' => <<<'MD'
# Clear Cache and Reload Mechanism

When testing APIs, client-side caches can occasionally retain stale environment resolutions, schema types, or temporary buffers.

## How It Works
Selecting **Help > Clear Cache and Reload**:
1. **Unsaved Edits Protection**: ThunderCall inspects all active tabs. If any tab has unsaved changes, you are prompted to confirm before discarding.
2. **In-Memory Cache Purge**: Clears variable resolution caches, comment threads, mock server caches, and GraphQL introspection schemas.
3. **Storage & Temporary File Cleanup**: Flushes temporary response buffers while preserving your authentication tokens and saved collections.
4. **Server Re-sync**: Automatically refetches workspaces, collections, environments, monitors, and history from the backend server.
5. **UI Re-render**: Rebuilds the tab graphics, refreshes the code editors, and resets status indicators.
MD
                    ],
                    [
                        'title' => 'Common Troubleshooting & FAQs',
                        'slug' => 'common-troubleshooting-faqs',
                        'kind' => 'reference',
                        'summary' => 'Resolving connection refused, SSL/TLS validation, and CORS issues.',
                        'content' => <<<'MD'
# Common Troubleshooting & FAQs

### 1. Connection Refused / 127.0.0.1
If ThunderCall fails to connect to a local service:
- Verify your local backend service is running and listening on the expected port.
- Check firewall settings to ensure localhost traffic is not blocked.

### 2. Self-Signed SSL/TLS Certificates
When testing internal APIs with self-signed certificates:
- In ThunderCall Settings, toggle **SSL Certificate Verification** to disabled for development environments.

### 3. Missing Environment Variables
- Ensure an environment is selected in the top-right environment dropdown.
- Check that the variable name matches exactly (variable names are case-sensitive).
MD
                    ]
                ]
            ]
        ];

        $now = now();

        foreach ($sections as $secData) {
            $sectionId = DB::table('documentation_sections')->updateOrInsert(
                [
                    'software_project_id' => $projectId,
                    'slug' => $secData['slug'],
                ],
                [
                    'title' => $secData['title'],
                    'description' => $secData['description'],
                    'icon' => $secData['icon'],
                    'sort_order' => $secData['sort_order'],
                    'is_published' => true,
                    'updated_at' => $now,
                ]
            );

            // Fetch actual ID (since updateOrInsert returns boolean)
            $section = DB::table('documentation_sections')
                ->where('software_project_id', $projectId)
                ->where('slug', $secData['slug'])
                ->first();

            $pageOrder = 1;
            foreach ($secData['pages'] as $pageData) {
                DB::table('documentation_pages')->updateOrInsert(
                    [
                        'software_project_id' => $projectId,
                        'release_id' => null,
                        'slug' => $pageData['slug'],
                    ],
                    [
                        'documentation_section_id' => $section->id,
                        'title' => $pageData['title'],
                        'kind' => $pageData['kind'],
                        'version' => '1.0.0',
                        'summary' => $pageData['summary'],
                        'content' => $pageData['content'],
                        'sort_order' => $pageOrder++,
                        'is_published' => true,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        $this->command->info("ThunderCall documentation successfully seeded in RozeHub!");
    }
}
