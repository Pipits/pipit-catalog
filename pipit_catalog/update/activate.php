<?php

    $API = new PerchAPI(1.0, 'pipit_catalog');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('pipit_catalog', 'Access the Catalog app');