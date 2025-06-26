<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use ReflectionClass;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    protected static array $defaultRoles = [
        [
            'name' => 'admin',
            'guard_name' => 'web'
        ]
    ];

    protected static array $defaultPermissions = [
        ['name' => 'read-report', 'group' => 'report'],
        ['name' => 'read-groupreport', 'group' => 'report'],
        ['name' => 'read-studentreport', 'group' => 'report'],
        ['name' => 'read-attendancereport', 'group' => 'report'],
        ['name' => 'read-paymentreport', 'group' => 'report'],
    ];

    protected static array $defaultAccess = ['create', 'read', 'update', 'delete'];

    public static function create(array $roles = []): array
    {
        self::createRoles($roles);

        self::createDefaultPermissions();

        $models = [];

        $modelClasses = self::getPermissionEnabledModels();

        foreach ($modelClasses as $class) {
            $model = self::getModelName($class);
            $ref = new ReflectionClass($class);

            // Default permissions
            if (
                !$ref->hasProperty('defaultAccess') ||
                ($ref->hasProperty('defaultAccess') && $ref->getProperty('defaultAccess')->getValue())
            ) {
                self::createModelPermissions($model);
            }

            // Custom permissions
            if ($ref->hasProperty('customPermissions')) {
                $permissions = $ref->getProperty('customPermissions')->getValue();
                if (is_array($permissions)) {
                    self::createCustomPermissions($permissions, $model);
                }
            }

            $models[] = $model;
        }

        return $models;
    }

    protected static function createRoles(array $roles = []): void
    {
        foreach (array_merge(self::$defaultRoles, $roles) as $role) {
            Role::firstOrCreate($role);
        }
    }

    protected static function createDefaultPermissions(): void
    {
        foreach (self::$defaultPermissions as $permission) {
            $permission = (object) $permission;
            self::createPermission($permission->name, $permission->group);
        }
    }

    protected static function getPermissionEnabledModels(): array
    {
        return collect(File::allFiles(app_path('Models')))
            ->filter(fn($file) => $file->getExtension() === 'php')
            ->map(fn($file) => 'App\\Models\\' . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname()))
            ->filter(fn($class) => class_exists($class) && self::hasInPermission($class))
            ->values()
            ->toArray();
    }

    protected static function hasInPermission(string $class): bool
    {
        $ref = new ReflectionClass($class);
        if ($ref->hasProperty('inPermission')) {
            $property = $ref->getProperty('inPermission');
            return $property->isStatic() && $property->isPublic() && $property->getValue();
        }
        return false;
    }

    protected static function createModelPermissions(string $model): void
    {
        foreach (self::$defaultAccess as $action) {
            self::createPermission("{$action}-{$model}", $model);
        }
    }

    protected static function createCustomPermissions(array $permissions, string $model): void
    {
        foreach ($permissions as $permission) {
            self::createPermission($permission, $model);
        }
    }

    protected static function createPermission(string $name, string $group): void
    {
        $permission = Permission::firstOrCreate(['name' => $name], ['group' => $group]);
        Role::findByName('admin')->givePermissionTo($permission);
    }

    protected static function getModelName(string $class): string
    {
        return strtolower(class_basename($class));
    }
}
