<?php

namespace App\Services;

use App\Models\DatabaseInstance;
use App\Models\Domain;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class DatabaseProvisioningService
{
    protected static function mysqlAuthArgs(): array
    {
        $rootUser = env('DB_PROVISION_ROOT_USER', env('DB_USERNAME', 'root'));
        $rootPassword = env('DB_PROVISION_ROOT_PASSWORD', env('DB_PASSWORD', ''));
        $host = env('DB_PROVISION_HOST', env('DB_HOST', '127.0.0.1'));
        $port = env('DB_PROVISION_PORT', env('DB_PORT', '3306'));

        $args = ['mysql', '-h', $host, '-P', $port, '-u', $rootUser];

        if ($rootPassword !== '') {
            $args[] = "--password={$rootPassword}";
        }

        return $args;
    }

    protected static function runMysqlCommand(string $sql): void
    {
        $command = array_merge(self::mysqlAuthArgs(), ['-e', $sql]);

        $proc = new Process($command);
        $proc->setTimeout(30);

        if ($proc->run() !== 0) {
            throw new \RuntimeException($proc->getErrorOutput() ?: $proc->getOutput());
        }
    }

    protected static function sanitizeName(string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9_]/', '_', $value));
    }

    public static function createDatabase(Domain $domain): string
    {
        $dbName = 'app_' . self::sanitizeName($domain->domain) . '_' . Str::random(6);

        self::runMysqlCommand("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        return $dbName;
    }

    public static function createUser(Domain $domain, string $password): string
    {
        $dbUser = 'user_' . self::sanitizeName($domain->domain) . '_' . Str::random(6);

        self::runMysqlCommand("CREATE USER IF NOT EXISTS `{$dbUser}`@'%' IDENTIFIED BY '{$password}';");

        return $dbUser;
    }

    public static function grantPrivileges(string $dbName, string $dbUser): void
    {
        self::runMysqlCommand("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO `{$dbUser}`@'%';");
        self::runMysqlCommand('FLUSH PRIVILEGES;');
    }

    public static function storeCredentials(Domain $domain, string $dbName, string $dbUser, string $dbPassword): DatabaseInstance
    {
        $encryptedPassword = Crypt::encryptString($dbPassword);

        return DatabaseInstance::create([
            'domain_id' => $domain->id,
            'db_name' => $dbName,
            'db_user' => $dbUser,
            'db_password' => $encryptedPassword,
            'status' => 'provisioned',
        ]);
    }

    public static function provision(Domain $domain): ?DatabaseInstance
    {
        $dbPassword = Str::random(24);

        try {
            $dbName = self::createDatabase($domain);
            $dbUser = self::createUser($domain, $dbPassword);
            self::grantPrivileges($dbName, $dbUser);

            return self::storeCredentials($domain, $dbName, $dbUser, $dbPassword);
        } catch (\Throwable $e) {
            Log::error('DatabaseProvisioningService failed', [
                'domain' => $domain->domain,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
