<?php
/**
 * call_bdd.php
 * Connexion PostgreSQL via PDO + helpers de requêtes.
 *
 * Variables d'environnement attendues :
 * - DB_HOST
 * - DB_PORT (optionnel, défaut 5432)
 * - DB_NAME
 * - DB_USER
 * - DB_PASS
 */

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'postgresql-osmose-3il.alwaysdata.net';
    $port = getenv('DB_PORT') ?: '5432';
    $name = getenv('DB_NAME') ?: 'osmose-3il_seriousgame';
    $user = getenv('DB_USER') ?: 'osmose-3il';
    $pass = getenv('DB_PASS') ?: 'osmose-3il_bdd';

    if ($name === '' || $pass === '') {
        throw new RuntimeException("DB_NAME et DB_PASS doivent être définis (variables d'environnement).");
    }

    $dsn = "pgsql:host={$host};port={$port};dbname={$name};options='--client_encoding=UTF8'";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}

/**
 * Exécute une requête SELECT et retourne toutes les lignes.
 */
function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Exécute une requête SELECT et retourne une seule ligne (ou null).
 */
function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/**
 * Exécute INSERT/UPDATE/DELETE, retourne le nombre de lignes affectées.
 */
function db_exec(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}
