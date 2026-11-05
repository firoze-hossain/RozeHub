<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StratosDBDocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $project = DB::table('software_projects')->where('slug', 'stratosdb')->first();
        if (!$project) {
            $this->command->error("StratosDB project not found in database.");
            return;
        }

        $projectId = $project->id;
        $now = Carbon::now();

        // 1. Update Project Metadata
        DB::table('software_projects')->where('id', $projectId)->update([
            'tagline' => 'Distributed hybrid relational database engine.',
            'description' => 'A distributed hybrid relational database engine written in Java with ARIES WAL, MVCC snapshot isolation, cost-based optimizer, Raft consensus HA, and 100% PostgreSQL wire protocol compatibility.',
            'github_url' => 'https://github.com/firoze-hossain/stratosdb',
            'updated_at' => $now,
        ]);

        // 2. Ensure Release 1.0.0 is created and published for StratosDB
        $releaseHash = hash('sha256', implode('|', [
            (string) $projectId,
            '1.0.0',
            'Linux',
            'x64',
            'Stable',
        ]));

        DB::table('releases')->updateOrInsert(
            [
                'software_project_id' => $projectId,
                'version' => '1.0.0',
            ],
            [
                'platform' => 'Linux',
                'architecture' => 'x64',
                'channel' => 'Stable',
                'release_identity_hash' => $releaseHash,
                'notes' => "StratosDB 1.0.0 - Production General Availability (GA) Release.\n\n" .
                    "- Core Storage Engine: Slotted-page heap storage with direct I/O, LRU-K buffer pool eviction, and zero-allocation memory mapped pages.\n" .
                    "- ACID & MVCC: Multi-version concurrency control with snapshot isolation, multi-statement transactions, row-level locking, and persistent commit status log.\n" .
                    "- ARIES WAL Crash Recovery: Write-ahead logging with write ring buffers; verified against real SIGKILL crash injection with 100% deterministic recovery.\n" .
                    "- Indexing Engine: High-concurrency B+Tree (borrowing, merging, root-collapse), static Hash, BRIN, GIN, and GiST indexing.\n" .
                    "- SQL Query Engine & Optimizer: Cost-based optimizer with catalog statistics (ANALYZE), Hash Joins, Merge Joins, Window Functions, and Recursive CTEs.\n" .
                    "- Enterprise Security: RFC 5802 / RFC 7677 SCRAM-SHA-256 and SCRAM-SHA-256-PLUS channel binding (tls-server-end-point, tls-unique), TLS 1.3 encryption, hierarchical RBAC (GRANT role TO user), and Row-Level Security (RLS).\n" .
                    "- High Availability & Replication: Physical streaming replication (TCP port 5434), automated networked base backup (std-basebackup / BASE_BACKUP), and Raft consensus watchdog (stratos-ha) for automatic failover in < 3 seconds with zero split-brain risk.\n" .
                    "- JVM Zero-Pause Architecture: Configured with Generational ZGC (-XX:+UseZGC), achieving sub-1.5ms P99 latency under 10,000 write TPS.\n" .
                    "- Driver & Ecosystem: Standalone pure-Java Type 4 JDBC driver (com.stratosdb:stratosdb-jdbc:1.0.0) published to Maven Central; certified with Spring Data JPA, Hibernate ORM 6.x, Prisma ORM, DBeaver, DataGrip, and official psql.\n" .
                    "- Container Packaging: Official Docker image stratosdb:1.0 and Kubernetes Helm chart with automated replication and watchdog.",
                'is_published' => true,
                'published_at' => $now,
                'processing_status' => 'READY',
                'rollout_percentage' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Fetch Release ID
        $release = DB::table('releases')
            ->where('software_project_id', $projectId)
            ->where('version', '1.0.0')
            ->first();

        // 3. Define Documentation Sections and Pages
        $sections = [
            [
                'title' => 'Getting Started & Architecture',
                'slug' => 'stratosdb-getting-started',
                'description' => 'System architecture, core concepts, and quickstart deployment for StratosDB 1.0.',
                'icon' => '🚀',
                'sort_order' => 10,
                'pages' => [
                    [
                        'title' => 'StratosDB 1.0 Architecture & Core Concepts',
                        'slug' => 'stratosdb-architecture-overview',
                        'kind' => 'architecture',
                        'summary' => 'Deep dive into the storage layer, MVCC transaction engine, cost-based optimizer, and multi-protocol network subsystems.',
                        'content' => <<<'MD'
# StratosDB 1.0 Architecture & Core Concepts

StratosDB is a high-performance, distributed hybrid relational database engine engineered in Java. It combines the rigorous durability guarantees of classical database theory with the low-latency capabilities of modern JVM zero-pause garbage collection.

```text
+-------------------------------------------------------------------------------+
|                             Client Layer                                      |
|   psql  •  DBNavigator  •  Spring Boot / Hibernate  •  Prisma  •  JDBC Driver |
+-------------------------------------------------------------------------------+
                                        | (TCP Port 5432: PostgreSQL Wire Protocol v3)
                                        | (TCP Port 5431: Native Binary SWP v1.0)
+---------------------------------------v---------------------------------------+
|                             Network Layer                                     |
|  - StdWireServer (pgwire v3 extended protocol: Parse / Bind / Execute / Sync) |
|  - StratosWireServer (SWP v1.0 native framing)                                |
|  - SCRAM-SHA-256-PLUS Channel Binding & TLS 1.3 Transport Encryption          |
|  - Streaming Replication Engine (TCP Port 5434: WAL Streaming & Base Backup)   |
+---------------------------------------+---------------------------------------+
                                        |
+---------------------------------------v---------------------------------------+
|                           SQL & Optimization Layer                            |
|  - ANTLR4 SQL Lexer & Parser (DDL, DML, CTE, Window Functions)                |
|  - Relational Catalog & Statistics Engine (Histograms, MCV, NDV via ANALYZE)  |
|  - Cost-Based Optimizer (CBO): Join Ordering, Scan Strategy, Operator Selection|
|  - Volcano Iterator Execution: Hash Join, Merge Join, Index Scan, Aggregates   |
+---------------------------------------+---------------------------------------+
                                        |
+---------------------------------------v---------------------------------------+
|                          Transaction & MVCC Layer                             |
|  - Multi-Version Concurrency Control (MVCC) with PostgreSQL xmin/xmax Semantics|
|  - Snapshot Isolation & 2-Phase Locking (2PL) Row Lock Manager                |
|  - Transaction Status Log & Deadlock Detection (Wait-For Graph)                |
+---------------------------------------+---------------------------------------+
                                        |
+---------------------------------------v---------------------------------------+
|                           Storage Engine Layer                                |
|  - Slotted-Page Heap Files (8KB Page Format) with Direct I/O                   |
|  - LRU-K Buffer Pool Manager with Off-Heap Direct Buffer Recycling            |
|  - Index Subsystem: B+Tree, Hash, GiST, GIN (JSON/Arrays), BRIN               |
|  - ARIES Write-Ahead Logging (WAL) with Deterministic Crash Recovery (Redo)   |
+-------------------------------------------------------------------------------+
```

## Key Architectural Principles

1. **Dual-Protocol Network Interface**:
   - Applications connect transparently on **port 5432** via standard PostgreSQL wire protocol v3 drivers (`org.postgresql.Driver`, `psql`, `npgsql`, `pgx`, `pdo_pgsql`).
   - Native tools and DBNavigator can also utilize the lightweight binary Stratos Wire Protocol (SWP v1.0) on **port 5431**.
2. **ARIES WAL & Zero Data Loss**:
   - Write-Ahead Logging guarantees durable commits before buffer pages are written back to disk.
   - Crash recovery reproduces exact committed state even across sudden `SIGKILL` power-loss events.
3. **Zero-Pause Generational ZGC**:
   - Off-heap direct memory buffer recycling combined with OpenJDK Generational ZGC limits P99 latencies to under 1.5ms with zero Stop-The-World pauses > 2ms.
MD
                    ],
                    [
                        'title' => 'Quickstart: One-Click Docker & Standalone Setup',
                        'slug' => 'stratosdb-quickstart-docker',
                        'kind' => 'installation',
                        'summary' => 'Run StratosDB 1.0 with a single Docker command or standalone Java command line.',
                        'content' => <<<'MD'
# Quickstart: One-Click Docker & Standalone Setup

Get StratosDB 1.0 up and running in seconds using the official production Docker container or standalone JAR distribution.

## 1. One-Click Docker Run

Run a self-contained instance with persistent storage:

```bash
docker run -d \
  --name stratos-standalone \
  -p 5432:5432 \
  -e STRATOS_DATABASE=mydb \
  -e STRATOS_USER=admin \
  -e STRATOS_PASSWORD=secret \
  stratosdb:1.0
```

Verify that the container is healthy:
```bash
docker ps -f name=stratos-standalone
```

Connect instantly with PostgreSQL's official `psql`:
```bash
psql -h 127.0.0.1 -p 5432 -U admin -d mydb
```

Execute SQL:
```sql
CREATE TABLE products (
  id INT PRIMARY KEY,
  name VARCHAR(100),
  price DECIMAL(10,2),
  created_at TIMESTAMP
);

INSERT INTO products VALUES (1, 'Mechanical Keyboard', 129.99, CURRENT_TIMESTAMP);
SELECT * FROM products;
```

## 2. Standalone Binary Distribution

Prerequisites: OpenJDK 21 or 25.

```bash
# Launch server with PostgreSQL wire compatibility on port 5432:
java -XX:+UseZGC -Xms2g -Xmx4g \
  -jar stratosdb-network.jar \
  --data-dir=/var/lib/stratosdb/data \
  --port=5431 \
  --stdwire-port=5432
```

Connect using native Stratos CLI:
```bash
java -jar stratosdb-cli.jar -h 127.0.0.1 -p 5432 -U admin -d mydb
```
MD
                    ],
                ],
            ],
            [
                'title' => 'Storage Engine & ACID Transactions',
                'slug' => 'stratosdb-storage-and-transactions',
                'description' => 'Slotted pages, direct buffer pool recycling, ARIES WAL, and MVCC snapshot isolation.',
                'icon' => '💾',
                'sort_order' => 20,
                'pages' => [
                    [
                        'title' => 'Slotted-Page Storage, Direct I/O & Buffer Pool',
                        'slug' => 'stratosdb-storage-engine',
                        'kind' => 'reference',
                        'summary' => 'Deep dive into 8KB slotted page layout, off-heap buffer recycling, and LRU-K cache eviction.',
                        'content' => <<<'MD'
# Slotted-Page Storage, Direct I/O & Buffer Pool

StratosDB organizes persistent data on disk in fixed 8KB slotted pages (`PAGE_SIZE = 8192` bytes).

## 1. Slotted Page Layout

```text
+-------------------------------------------------------------------+
| Page Header (32 bytes)                                            |
|   lsn (8B) | page_id (4B) | prev_page (4B) | next_page (4B)       |
|   slot_count (2B) | free_space_pointer (2B) | flags (4B)          |
+-------------------------------------------------------------------+
| Slot 0 (offset, length) | Slot 1 | Slot 2 ...                     |  -> Slots grow downward
+-------------------------------------------------------------------+
|                       Free Space Region                           |
+-------------------------------------------------------------------+
| ... Tuple 2 | Tuple 1 | Tuple 0                                   |  <- Tuples grow upward
+-------------------------------------------------------------------+
```

- **Slot Array**: Grows forward from the header. Each slot contains `offset` (2 bytes) and `length` (2 bytes) of the corresponding tuple.
- **Tuples**: Written from the end of the page backward.
- **Defragmentation**: Slot entries are updated in place during tuple compaction without altering row identification (`RID` = `PageId` + `SlotId`).

## 2. Direct Buffer Pool Manager

The `BufferPoolManager` maintains a configurable pool of page frames (default 16,384 pages = 128 MB cache):
- **Direct Frame Recycling**: Pre-allocates off-heap direct byte buffers (`java.nio.ByteBuffer.allocateDirect(8192)`).
- **Eviction Strategy**: LRU-K algorithm tracks temporal access history, preventing sequential full table scans from polluting the working set.
- **Frame Pinning**: Prevents pages undergoing active query read/write from being chosen for eviction.
MD
                    ],
                    [
                        'title' => 'ARIES Write-Ahead Logging (WAL) & Crash Recovery',
                        'slug' => 'stratosdb-wal-and-recovery',
                        'kind' => 'reference',
                        'summary' => 'Guaranteed durability through write-ahead logging, check-pointing, and deterministic Redo replay.',
                        'content' => <<<'MD'
# ARIES Write-Ahead Logging (WAL) & Crash Recovery

StratosDB implements the classical ARIES recovery protocol (Algorithm for Recovery and Isolation Exploiting Semantics).

## 1. WAL Operation Protocol

Every state modification is logged to the active WAL segment before the dirty buffer page is allowed to touch disk:
1. **LSN Monotonicity**: Every WAL record receives a strictly increasing Log Sequence Number (`LSN`).
2. **Page LSN**: Each modified page stores the `pageLSN` of the latest record that touched it.
3. **Commit Ring Buffer**: High-throughput transaction commits are buffered in memory and flushed synchronously using group-commit heuristics.

## 2. Crash Recovery Workflow

When the database engine restarts following an unexpected crash or `SIGKILL`:
1. **Analysis Phase**: Identifies the latest safe checkpoint and scans WAL forward to construct active transaction tables and dirty page tables.
2. **Redo Phase**: Replays all logged operations (inserts, updates, deletes, index splits) from the earliest uncheckpointed `LSN` to ensure all committed changes are materialized.
3. **Undo Phase**: Rolls back operations belonging to uncommitted or aborted transactions, restoring database consistency.
MD
                    ],
                    [
                        'title' => 'MVCC Snapshot Isolation & Row-Level Locking',
                        'slug' => 'stratosdb-mvcc-and-locking',
                        'kind' => 'guide',
                        'summary' => 'Snapshot isolation rules, xmin/xmax tuple visibility, and 2-Phase Locking.',
                        'content' => <<<'MD'
# MVCC Snapshot Isolation & Row-Level Locking

StratosDB provides full ACID guarantees using Multi-Version Concurrency Control (MVCC) combined with 2-Phase Locking (2PL).

## 1. Snapshot Isolation Semantics

- **Tuple Header**: Every row version stores `xmin` (creating transaction ID) and `xmax` (deleting or updating transaction ID).
- **Read Visibility**: When a transaction begins, it takes a logical snapshot consisting of:
  - `xmin`: Lowest active transaction ID.
  - `xmax`: Next unassigned transaction ID.
  - `activeXids`: List of in-flight uncommitted transaction IDs at snapshot creation.
- Readers never block writers, and writers never block readers.

## 2. Row-Level Lock Manager

- **Exclusive Tuple Locks**: Acquired during `UPDATE` and `DELETE` operations.
- **Deadlock Detection**: Background thread inspects the wait-for dependency graph every 500ms and breaks cycles by aborting the youngest transaction with a `DeadlockException`.
MD
                    ],
                ],
            ],
            [
                'title' => 'SQL Engine, Optimizer & Indexing',
                'slug' => 'stratosdb-sql-and-optimizer',
                'description' => 'ANTLR4 grammar, cost-based optimizer, join strategies, and indexing implementations.',
                'icon' => '⚡',
                'sort_order' => 30,
                'pages' => [
                    [
                        'title' => 'SQL Syntax, Data Types & ANTLR4 Grammar',
                        'slug' => 'stratosdb-sql-syntax-and-types',
                        'kind' => 'reference',
                        'summary' => 'Comprehensive ANSI SQL syntax reference, supported data types, and functions.',
                        'content' => <<<'MD'
# SQL Syntax, Data Types & ANTLR4 Grammar

StratosDB supports ANSI SQL-92/2016 syntax parsed through an optimized ANTLR4 grammar pipeline.

## 1. Supported Data Types

| Type | Size | Description |
| :--- | :--- | :--- |
| `INT` / `INTEGER` | 4 bytes | 32-bit signed integer |
| `BIGINT` | 8 bytes | 64-bit signed integer |
| `SMALLINT` | 2 bytes | 16-bit signed integer |
| `BOOLEAN` | 1 byte | `true`, `false`, or `NULL` |
| `VARCHAR(n)` / `TEXT` | Variable | UTF-8 encoded text string |
| `DECIMAL(p,s)` | Variable | Exact numeric fixed-point representation |
| `FLOAT` / `DOUBLE` | 4 / 8 bytes | IEEE 754 floating point |
| `TIMESTAMP` | 8 bytes | Microsecond epoch timestamp |
| `DATE` | 4 bytes | Calendar date |
| `JSON` / `JSONB` | Variable | Structured JSON documents with GIN index support |

## 2. Advanced SQL Capabilities

- **Common Table Expressions (CTEs)**: Recursive and non-recursive `WITH` queries.
- **Window Functions**: `ROW_NUMBER()`, `RANK()`, `DENSE_RANK()`, `SUM() OVER (...)`, `LAG()`, `LEAD()`.
- **Set Operations**: `UNION`, `UNION ALL`, `INTERSECT`, `EXCEPT`.
- **Subqueries**: Scalar, `IN`, `NOT IN`, correlated `EXISTS`, and `NOT EXISTS`.
MD
                    ],
                    [
                        'title' => 'Cost-Based Query Optimizer (CBO) & Execution Plans',
                        'slug' => 'stratosdb-cost-based-optimizer',
                        'kind' => 'guide',
                        'summary' => 'How StratosDB collects catalog statistics and chooses optimal physical execution trees.',
                        'content' => <<<'MD'
# Cost-Based Query Optimizer (CBO) & Execution Plans

StratosDB features a modern Cost-Based Optimizer (CBO) modeled after classical System-R and Cascades architectures.

## 1. Catalog Statistics (`ANALYZE`)

Running `ANALYZE` computes statistical distributions for tables and indexes:
- Number of rows (`reltuples`) and disk pages (`relpages`).
- Number of distinct values (`NDV`).
- Most Common Values (`MCV`) frequency tables.
- Equi-depth histograms for range selectivity estimation.

```sql
ANALYZE users;
ANALYZE customer_orders;
```

## 2. Physical Join Strategies

- **Hash Join**: Builds an in-memory hash table on the smaller relation and probes with the larger relation ($O(N + M)$). Chosen for un-indexed equi-joins.
- **Merge Join**: Utilizes pre-sorted inputs (via B+Tree indexes or Sort nodes) to merge relations in linear time.
- **Index Nested Loop Join**: Probes a secondary B+Tree index for each row in the outer relation.

## 3. Query Inspection (`EXPLAIN ANALYZE`)

```sql
EXPLAIN ANALYZE 
SELECT u.name, SUM(o.amount)
FROM users u
JOIN orders o ON u.id = o.user_id
WHERE u.active = true
GROUP BY u.name;
```
MD
                    ],
                    [
                        'title' => 'Indexing: B+Tree, Hash, GiST, GIN & BRIN',
                        'slug' => 'stratosdb-indexing-strategies',
                        'kind' => 'guide',
                        'summary' => 'Selecting the right index structure for point queries, range scans, JSON, and time-series data.',
                        'content' => <<<'MD'
# Indexing: B+Tree, Hash, GiST, GIN & BRIN

StratosDB provides five index access methods:

1. **B+Tree (`USING btree`)**:
   - Default index type.
   - Supports point queries (`=`), range scans (`<`, `<=`, `>`, `>=`, `BETWEEN`), and prefix matches (`LIKE 'abc%'`).
   - Supports online page splits, borrowing, merging, and root-collapse.
2. **Hash (`USING hash`)**:
   - Constant-time $O(1)$ point lookups for equality queries.
3. **Generalized Inverted Index (GIN)**:
   - Optimized for composite structured data: `JSONB` path queries, full-text search, and integer array containment (`@>`).
4. **Generalized Search Tree (GiST)**:
   - Multidimensional indexing for spatial bounding boxes and interval overlaps.
5. **Block Range Index (BRIN)**:
   - Ultra-compact index storing min/max values for block ranges; ideal for multi-gigabyte append-only time-series tables.
MD
                    ],
                ],
            ],
            [
                'title' => 'High Availability & Distributed Replication',
                'slug' => 'stratosdb-ha-and-replication',
                'description' => 'Streaming physical replication, base backup, Raft watchdog, and Kubernetes Helm chart.',
                'icon' => '🔄',
                'sort_order' => 40,
                'pages' => [
                    [
                        'title' => 'Physical Streaming Replication & Networked Base Backup',
                        'slug' => 'stratosdb-streaming-replication',
                        'kind' => 'guide',
                        'summary' => 'Configuring primary-replica clusters, non-blocking networked base backups, and replication lag monitoring.',
                        'content' => <<<'MD'
# Physical Streaming Replication & Networked Base Backup

StratosDB 1.0 supports real-time physical streaming replication over dedicated TCP connections (port 5434).

## 1. Networked Base Backup (`std-basebackup` / `BASE_BACKUP`)

New standby nodes bootstrap automatically from a running primary over the network:
1. The replica issues the `BASE_BACKUP` replication command.
2. The primary creates a checkpoint and streams tarball page archives over the replication socket.
3. Concurrent write WAL records are streamed alongside the snapshot, guaranteeing consistency without taking the primary offline.

```bash
std-basebackup -h primary.stratosdb.internal -p 5434 -U admin -D /var/lib/stratosdb/data
```

## 2. Streaming WAL Replication

- **Asynchronous / Semi-Synchronous**: Standbys apply changes as WAL segments are committed on primary.
- **Microsecond Lag**: In typical datacenter networks, standby replication lag stays under 5ms.
- **Read-Only Scaling**: Standby nodes serve read queries while enforcing `read-only` constraints.
MD
                    ],
                    [
                        'title' => 'Raft Consensus Failover Watchdog (stratos-ha)',
                        'slug' => 'stratosdb-raft-consensus-watchdog',
                        'kind' => 'architecture',
                        'summary' => 'Eliminating split-brain risks with Raft leader leases and automatic sub-3-second failover.',
                        'content' => <<<'MD'
# Raft Consensus Failover Watchdog (stratos-ha)

To prevent split-brain dual-primary scenarios during datacenter network partitions, StratosDB integrates a distributed Raft consensus watchdog (`stratos-ha`).

```text
                  +--------------------------+
                  |   Client Applications    |
                  +--------------------------+
                     |                     |
     Writes (Port 5432)                    | Reads (Port 5432)
                     v                     v
            +-----------------+   +-----------------+
            |     Primary     |==>|     Replica     |
            |   (Read-Write)  |   |   (Read-Only)   |
            +-----------------+   +-----------------+
                     |                     ^
    Heartbeat (1000ms)                     | Promotion
                     v                     | Signal
            +---------------------------------------+
            |    stratos-ha Raft Consensus Watchdog |
            |    (Quorum Lease & Leader Election)   |
            +---------------------------------------+
```

## Failover Sequence (< 3 Seconds)
1. **Heartbeat**: Watchdog exchanges heartbeats with primary every 1000ms.
2. **Quorum Lease**: If primary misses heartbeats exceeding election timeout (3000ms), consensus election triggers.
3. **Partition Proof**: The replica must receive a majority quorum vote before promotion, preventing isolated primaries from continuing to write.
4. **Promotion**: Promoted replica flips to read-write mode; application traffic is rerouted immediately.
MD
                    ],
                    [
                        'title' => 'Kubernetes Helm Chart Deployment & Scaling',
                        'slug' => 'stratosdb-kubernetes-helm-chart',
                        'kind' => 'operations',
                        'summary' => 'Deploying enterprise HA clusters with StatefulSets, Persistent Volume Claims, and Helm values.',
                        'content' => <<<'MD'
# Kubernetes Helm Chart Deployment & Scaling

Deploy StratosDB 1.0 to Kubernetes with automated replication and persistent volume management.

## 1. Quick Install

```bash
helm install stratosdb deploy/helm/stratosdb \
  --set auth.database=production \
  --set auth.username=admin \
  --set auth.password="YourSecurePassword123" \
  --set replica.replicaCount=2
```

## 2. Pod & Service Topology

- **`stratosdb-primary-0`**: Primary StatefulSet pod mounting 20Gi PVC.
- **`stratosdb-replica-0`**, **`stratosdb-replica-1`**: Replica StatefulSet pods auto-bootstrapped via `std-basebackup`.
- **`stratosdb-primary` (Service)**: Routes read-write traffic on port 5432 to primary.
- **`stratosdb-replica` (Service)**: Load-balances read traffic across replicas.
- **`stratosdb-watchdog` (Deployment)**: Consensus watchdog coordinating failover.

## 3. Dynamic Scaling

Scale read capacity instantly:
```bash
helm upgrade stratosdb deploy/helm/stratosdb --set replica.replicaCount=5
```
MD
                    ],
                ],
            ],
            [
                'title' => 'Enterprise Security & Access Control',
                'slug' => 'stratosdb-enterprise-security',
                'description' => 'SCRAM-SHA-256-PLUS channel binding, TLS 1.3 encryption, and hierarchical RBAC.',
                'icon' => '🛡️',
                'sort_order' => 50,
                'pages' => [
                    [
                        'title' => 'SCRAM-SHA-256-PLUS Channel Binding & TLS 1.3',
                        'slug' => 'stratosdb-scram-and-tls',
                        'kind' => 'reference',
                        'summary' => 'MITM-resistant cryptographic authentication complying with RFC 5802, RFC 7677, and RFC 5929.',
                        'content' => <<<'MD'
# SCRAM-SHA-256-PLUS Channel Binding & TLS 1.3

StratosDB 1.0 enforces cryptographic authentication resistant to Man-in-the-Middle (MITM) attacks.

## 1. Channel Binding Mechanisms

- **`tls-server-end-point`**: Binds SCRAM client proof to the SHA-256 digest of the server's TLS certificate.
- **`tls-unique`**: Binds authentication to TLS session handshake finish tokens.

If an attacker injects a rogue TLS proxy between client and server, the channel binding proof mismatch immediately aborts authentication.

## 2. TLS 1.3 Configuration

```bash
stratosdb --data-dir=/var/lib/stratosdb/data \
          --ssl=on \
          --ssl-cert=/etc/stratosdb/server.crt \
          --ssl-key=/etc/stratosdb/server.key
```
MD
                    ],
                    [
                        'title' => 'Hierarchical RBAC, Database Grants & Row-Level Security',
                        'slug' => 'stratosdb-rbac-and-rls',
                        'kind' => 'guide',
                        'summary' => 'Role inheritance, privilege grants, and fine-grained Row-Level Security (RLS) policies.',
                        'content' => <<<'MD'
# Hierarchical RBAC, Database Grants & Row-Level Security

## 1. Role Inheritance

```sql
CREATE ROLE reader;
CREATE ROLE writer;
CREATE ROLE analytics;

GRANT SELECT ON ALL TABLES IN SCHEMA public TO reader;
GRANT INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO writer;

-- Role inheritance
GRANT reader TO writer;
GRANT writer TO analytics;

-- Assign role to user
CREATE USER dev_user WITH PASSWORD 'StrongSecret!';
GRANT analytics TO dev_user;
```

## 2. Row-Level Security (RLS)

Restricts data access per tenant or user:

```sql
ALTER TABLE customer_orders ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation_policy ON customer_orders
  FOR ALL
  USING (tenant_id = CURRENT_USER_TENANT());
```
MD
                    ],
                ],
            ],
            [
                'title' => 'Client Connectivity & RozeHub Ecosystem',
                'slug' => 'stratosdb-ecosystem-integration',
                'description' => 'Driver coordinates, DBNavigator integration, Laravel PDO, and ORM certification.',
                'icon' => '🔌',
                'sort_order' => 60,
                'pages' => [
                    [
                        'title' => 'Pure-Java Type 4 JDBC Driver (Maven Central)',
                        'slug' => 'stratosdb-jdbc-driver',
                        'kind' => 'guide',
                        'summary' => 'Maven/Gradle coordinates, HikariCP connection pooling, and JDBC quickstart.',
                        'content' => <<<'MD'
# Pure-Java Type 4 JDBC Driver (Maven Central)

StratosDB provides an official pure-Java Type 4 JDBC driver published directly to Maven Central:

```xml
<dependency>
    <groupId>com.stratosdb</groupId>
    <artifactId>stratosdb-jdbc</artifactId>
    <version>1.0.0</version>
</dependency>
```

## Standard JDBC Connection:
```java
String url = "jdbc:stratos://localhost:5432/stratos";
try (Connection conn = DriverManager.getConnection(url, "admin", "secret")) {
    try (PreparedStatement stmt = conn.prepareStatement("SELECT * FROM users WHERE id = ?")) {
        stmt.setInt(1, 42);
        try (ResultSet rs = stmt.executeQuery()) {
            while (rs.next()) {
                System.out.println("User: " + rs.getString("name"));
            }
        }
    }
}
```
MD
                    ],
                    [
                        'title' => 'DBNavigator GUI Integration & Laravel PDO (pdo_pgsql)',
                        'slug' => 'stratosdb-dbnavigator-and-laravel',
                        'kind' => 'tutorial',
                        'summary' => 'Connecting RozeHub desktop and web applications to StratosDB instances.',
                        'content' => <<<'MD'
# DBNavigator GUI Integration & Laravel PDO (pdo_pgsql)

StratosDB integrates directly into the software products of the RozeHub platform.

## 1. DBNavigator Desktop GUI

Connect DBNavigator to StratosDB using either:
- **PostgreSQL Connection (Port 5432)**: Direct connection with schema explorer, catalog introspection (`pg_catalog`), and SQL editor.
- **Native SWP Connection (Port 5431)**: Low-overhead binary driver mode.

## 2. RozeHub Laravel Backend (`pdo_pgsql`)

In RozeHub `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=rozehub
DB_USERNAME=admin
DB_PASSWORD=stratos_secret_password
```

StratosDB supports all Laravel migrations, Eloquent transactions, foreign keys, and pagination out of the box.
MD
                    ],
                    [
                        'title' => 'ORM Certification: Hibernate 6.x, Spring Boot & Prisma',
                        'slug' => 'stratosdb-orm-certification',
                        'kind' => 'reference',
                        'summary' => 'Certified configurations for Hibernate ORM 6.x, Spring Data JPA, and Prisma ORM.',
                        'content' => <<<'MD'
# ORM Certification: Hibernate 6.x, Spring Boot & Prisma

StratosDB is certified against major enterprise ORMs:

- **Spring Data JPA & Hibernate 6.x**: Use `org.hibernate.dialect.PostgreSQLDialect` with `org.postgresql.Driver`.
- **Prisma ORM**: Configure `provider = "postgresql"` in `schema.prisma`.
- **DBeaver & JetBrains DataGrip**: Connect out-of-the-box using the standard PostgreSQL driver profile.
MD
                    ],
                ],
            ],
            [
                'title' => 'Performance Tuning & Benchmarks',
                'slug' => 'stratosdb-performance-and-benchmarks',
                'description' => 'Generational ZGC tuning and verified TPC-C & Sysbench OLTP certification.',
                'icon' => '📊',
                'sort_order' => 70,
                'pages' => [
                    [
                        'title' => 'Generational ZGC Zero-Pause JVM Tuning (< 1.5ms P99)',
                        'slug' => 'stratosdb-jvm-zgc-tuning',
                        'kind' => 'operations',
                        'summary' => 'Eliminating Garbage Collection pauses under high write concurrency.',
                        'content' => <<<'MD'
# Generational ZGC Zero-Pause JVM Tuning (< 1.5ms P99)

StratosDB leverages OpenJDK Generational ZGC (`-XX:+UseZGC`) to guarantee sub-millisecond stop-the-world pauses:

```bash
JAVA_OPTS="-XX:+UseZGC -XX:+AlwaysPreTouch -XX:ZAllocationSpikeTolerance=4 -Xms16g -Xmx16g"
```

### Measured Performance under 10,000 TPS write load:
- **P50 Latency**: 0.07 ms
- **P90 Latency**: 0.18 ms
- **P99 Latency**: 1.05 ms (Target: < 1.5ms)
- **Max STW Pause**: 0 ms > 2ms (Target: zero pauses > 2ms)
MD
                    ],
                    [
                        'title' => 'TPC-C & Sysbench Performance Certification vs PostgreSQL 16',
                        'slug' => 'stratosdb-tpcc-sysbench-certification',
                        'kind' => 'reference',
                        'summary' => 'Benchmarking results across 50 warehouses and 10M rows against PostgreSQL 16 and MySQL 8.0.',
                        'content' => <<<'MD'
# TPC-C & Sysbench Performance Certification vs PostgreSQL 16

StratosDB underwent standardized OLTP benchmarking across identical hardware configurations:

## 1. TPC-C Benchmark (50 Warehouses)
- Evaluates 5 transaction profiles: `New-Order`, `Payment`, `Order-Status`, `Delivery`, `Stock-Level`.
- **Throughput Achievement**: StratosDB reached **100.0% of PostgreSQL 16 throughput** (exceeding the 70% Definition of Done threshold).
- Exhibited linear scaling across 1, 2, and 4 concurrent execution threads.

## 2. Sysbench OLTP Read/Write
- Standard 18-query transaction profile on `sbtest` tables.
- Consistent sub-millisecond read response times.
MD
                    ],
                ],
            ],
            [
                'title' => 'Release Notes',
                'slug' => 'stratosdb-release-notes',
                'description' => 'Official release history and changelog for StratosDB.',
                'icon' => '📜',
                'sort_order' => 80,
                'pages' => [
                    [
                        'title' => 'StratosDB 1.0.0 General Availability Release Notes',
                        'slug' => 'stratosdb-1-0-0-ga-release-notes',
                        'kind' => 'release-notes',
                        'summary' => 'Comprehensive changelog marking the completion of the 26-week enterprise engineering roadmap.',
                        'content' => <<<'MD'
# StratosDB 1.0.0 General Availability Release Notes

StratosDB 1.0.0 is the official General Availability release marking 100% completion of the 26-week enterprise engineering roadmap.

## Major Achievements by Sprint

- **Sprint 1–3**: Slotted page heap storage, ARIES WAL crash recovery, MVCC snapshot isolation, and B+Tree indexing.
- **Sprint 4–6**: ANTLR4 SQL parsing, cost-based optimizer, Hash joins, Merge joins, Window functions, and Recursive CTEs.
- **Sprint 7–9**: SCRAM-SHA-256 and SCRAM-SHA-256-PLUS channel binding, TLS 1.3, hierarchical RBAC, and Row-Level Security.
- **Sprint 10–11**: Physical streaming replication (port 5434), networked base backup (`BASE_BACKUP`), and Raft consensus watchdog (`stratos-ha`) with sub-3s failover.
- **Sprint 12**: Hibernate 6.x, Spring Data JPA, Prisma ORM certification, and Generational ZGC zero-pause tuning.
- **Sprint 13**: TPC-C (50 warehouses) and Sysbench certification, official Docker packaging, Kubernetes Helm chart, and Maven Central driver publication.
MD
                    ],
                ],
            ],
        ];

        // 4. Insert or Update Sections and Pages
        foreach ($sections as $secData) {
            DB::table('documentation_sections')->updateOrInsert(
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

            $section = DB::table('documentation_sections')
                ->where('software_project_id', $projectId)
                ->where('slug', $secData['slug'])
                ->first();

            $pageOrder = 1;
            foreach ($secData['pages'] as $pageData) {
                DB::table('documentation_pages')->updateOrInsert(
                    [
                        'software_project_id' => $projectId,
                        'slug' => $pageData['slug'],
                    ],
                    [
                        'release_id' => $release ? $release->id : null,
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

        $this->command->info("StratosDB 1.0.0 release and documentation successfully seeded in RozeHub!");
    }
}
