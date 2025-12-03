<?php

declare(strict_types=1);

namespace JardisPsr\DbConnection;

use PDO;
use PDOException;

interface DbConnectionInterface
{
    /**
     * Default PDO options for all connections.
     */
    public const DEFAULT_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Returns the underlying PDO instance.
     *
     * Use this for direct database operations.
     */
    public function pdo(): PDO;

    /**
     * Checks if the connection is still valid.
     *
     * Warning: This may perform a test query and could be slow.
     * Useful in long-running application servers to detect stale connections.
     *
     * @return bool True if connection is alive, false otherwise
     */
    public function isConnected(): bool;

    /**
     * Explicitly closes the database connection.
     *
     * Important for long-running application servers (RoadRunner, Swoole, etc.)
     * to prevent stale connections and free resources.
     *
     * After calling this, the connection cannot be used.
     * Calling disconnect() on an already disconnected connection is safe (no-op).
     */
    public function disconnect(): void;

    /**
     * Reconnects to the database.
     *
     * Useful when a stale connection is detected.
     *
     * @throws PDOException if reconnection fails
     */
    public function reconnect(): void;

    /**
     * Begins a transaction.
     *
     * @throws PDOException if transaction cannot be started
     */
    public function beginTransaction(): void;

    /**
     * Commits the current transaction.
     *
     * @throws PDOException if no transaction is active
     */
    public function commit(): void;

    /**
     * Rolls back the current transaction.
     *
     * @throws PDOException if no transaction is active
     */
    public function rollback(): void;

    /**
     * Checks if currently in a transaction.
     *
     * @return bool True if a transaction is active, false otherwise
     */
    public function inTransaction(): bool;

    /**
     * Get the database driver name (e.g., 'mysql', 'pgsql').
     *
     * This is determined from the DSN during connection.
     */
    public function getDriverName(): string;

    /**
     * Get the database name.
     *
     * This is determined from the DSN during connection.
     */
    public function getDatabaseName(): string;
}
