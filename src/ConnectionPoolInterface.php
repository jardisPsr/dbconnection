<?php

declare(strict_types=1);

namespace JardisPsr\DbConnection;

use RuntimeException;

/**
 * Interface for read/write splitting connection pool implementations.
 *
 * Provides connection pooling for database replication setups,
 * routing writes to a primary server and distributing reads across replica servers.
 */
interface ConnectionPoolInterface
{
    /**
     * Get a connection for write operations (primary database).
     *
     * The connection is created lazily on first use.
     *
     * @return DbConnectionInterface A connection to the primary database
     * @throws RuntimeException If writer connection fails health check or creation fails
     */
    public function getWriter(): DbConnectionInterface;

    /**
     * Get a connection for read operations (replica database).
     *
     * Automatically load-balances across available readers using the configured
     * strategy (round-robin, random, weighted) and performs failover if a reader
     * is unhealthy. Connections are created lazily on first use.
     *
     * If no readers are configured, returns the writer connection.
     *
     * @return DbConnectionInterface A connection to a replica (or primary if no replicas)
     * @throws RuntimeException If all readers fail health checks or creation fails
     */
    public function getReader(): DbConnectionInterface;

    /**
     * Get pool statistics.
     *
     * Returns current statistics about pool usage including read/write counts
     * and failover events.
     *
     * @return array{reads: int, writes: int, failovers: int, readers: int} Pool statistics
     */
    public function getStats(): array;

    /**
     * Reset statistics counters.
     *
     * Resets read, write, and failover counters to zero.
     * Useful for monitoring and metrics collection.
     *
     * @return void
     */
    public function resetStats(): void;
}
