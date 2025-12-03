# JardisPsr DbConnection - Database Connection Interfaces

![Build Status](https://github.com/jardisCore/dbconnection/actions/workflows/ci.yml/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue.svg)](https://www.php.net/)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-Level%208-success.svg)](phpstan.neon)
[![PSR-4](https://img.shields.io/badge/autoload-PSR--4-blue.svg)](https://www.php-fig.org/psr/psr-4/)
[![PSR-12](https://img.shields.io/badge/code%20style-PSR--12-orange.svg)](phpcs.xml)

This package provides interface contracts for database connection management with read/write splitting support. Implementations can be used in any PHP environment, including long-running processes like RoadRunner, Swoole, or FrankenPHP.

**Important**: This is a contract-only package. It defines interfaces but does not provide implementations.

## Features

- **Read/Write Splitting**: Separate connections for primary (write) and replica (read) databases
- **Connection Management**: Handle connection lifecycle, validation, and reconnection
- **Automatic Load Balancing**: Distribute read operations across multiple replicas
- **Failover Support**: Automatic failover when replicas become unhealthy
- **Statistics Tracking**: Monitor read/write counts and failover events

## Requirements

- PHP >= 8.2
- ext-pdo

## Installation

```bash
composer require jardispsr/dbconnection
```

## Interfaces

### DbConnectionInterface

Core interface for database connections with support for:
- PDO instance access (`pdo()`)
- Connection validation (`isConnected()`)
- Connection lifecycle management (`disconnect()`, `reconnect()`)
- Transaction management (`beginTransaction()`, `commit()`, `rollback()`, `inTransaction()`)
- Metadata access (`getDriverName()`, `getDatabaseName()`)
- Default PDO options constant (`DEFAULT_OPTIONS`)

### ConnectionPoolInterface

Interface for read/write splitting in database replication setups:
- `getWriter()`: Returns a `DbConnectionInterface` for write operations (primary database)
- `getReader()`: Returns a `DbConnectionInterface` for read operations (replica database)
- `getStats()`: Returns array with statistics: `{reads: int, writes: int, failovers: int, readers: int}`
- `resetStats()`: Resets all statistics counters to zero

Implementations are expected to provide:
- Lazy connection creation
- Load balancing across multiple replicas
- Automatic failover when replicas become unhealthy
- Fallback to writer when no readers are configured

## Usage Example

```php
use JardisPsr\DbConnection\ConnectionPoolInterface;
use JardisPsr\DbConnection\DbConnectionInterface;

// Assuming you have an implementation of ConnectionPoolInterface
// (this package only provides the interface contracts)

/** @var ConnectionPoolInterface $pool */

// Write operations - use the primary database
$writer = $pool->getWriter();
$pdo = $writer->pdo();

$stmt = $pdo->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
$stmt->execute(['John Doe', 'john@example.com']);

// Read operations - use a replica database
$reader = $pool->getReader();
$pdo = $reader->pdo();

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([1]);
$user = $stmt->fetch();

// Monitor pool statistics
$stats = $pool->getStats();
echo sprintf(
    "Reads: %d, Writes: %d, Failovers: %d, Readers: %d\n",
    $stats['reads'],
    $stats['writes'],
    $stats['failovers'],
    $stats['readers']
);

// Reset statistics for new monitoring period
$pool->resetStats();
```

## Why Read/Write Splitting?

In database replication setups, the primary database handles writes while replicas handle reads. Read/write splitting:

1. **Scales read operations**: Distribute read load across multiple replicas
2. **Reduces primary load**: Offload read queries from the primary database
3. **Improves performance**: Read queries don't compete with write operations
4. **Provides failover**: Automatically handle replica failures
5. **Enables monitoring**: Track read/write patterns and failover events

This approach is particularly beneficial in long-running PHP processes (RoadRunner, Swoole, FrankenPHP) where database connections persist across multiple requests.

## Development

### Code Quality

```bash
# Run PHPStan (level 8)
make phpstan

# Run PHP CodeSniffer (PSR-12)
make phpcs
```

All commands require Docker Compose and a `.env` file (see Makefile for details).

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Support

- Issues: https://github.com/JardisPsr/dbconnection/issues
- Email: devcore@headgent.dev

## Authors

- Jardis Core Development Team
