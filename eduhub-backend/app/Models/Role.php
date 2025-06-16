<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public static bool $inPermission = true;
    public static array $customPermissions = ['read-dashboard'];
    // Add customizations here if needed
}
