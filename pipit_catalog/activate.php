<?php

    $API = new PerchAPI(1.0, 'pipit_catalog');
    $UserPrivileges = $API->get('UserPrivileges');
    $UserPrivileges->create_privilege('pipit_catalog', 'Access the Catalog app');
    $UserPrivileges->create_privilege('pipit_catalog.reorder', 'Reorder products');
    $UserPrivileges->create_privilege('pipit_catalog.republish', 'Republish products');