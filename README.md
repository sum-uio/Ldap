Ldap
====

PHP library for handling LDAP connections and queries.

Usage
-----

    // Define host.
    $host = 'ldap://your.host.com/';

    // Connect and bind to host.
    $ldap = new Ldap\Manager($host);

    // Query directory.
    $result = $ldap->search($dn, $filter, $filterArguments);

    // Loop through each entry in the result.
    foreach ($result as $entry)
    {
        // ...
    }

    // Close connection.
    $ldap->close();