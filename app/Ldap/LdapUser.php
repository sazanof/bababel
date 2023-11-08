<?php

namespace App\Ldap;

use LdapRecord\Models\ActiveDirectory\User;

class LdapUser extends User
{
    /**
     * The object classes of the LDAP model.
     */
    public static array $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'user',
    ];
}
