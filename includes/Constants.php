<?php 
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'stressos');
    
    define('USER_CREATED', 101);
    define('USER_EXISTS', 102);
    define('USER_FAILURE', 103); 
    define('USER_AUTHENTICATED', 201);
    define('USER_NOT_FOUND', 202); 
    define('USER_PASSWORD_DO_NOT_MATCH', 203);
    define('PASSWORD_CHANGED', 301);
    define('PASSWORD_DO_NOT_MATCH', 302);
    define('PASSWORD_NOT_CHANGED', 303);
    define('PARENT_CREATED', 401);
    define('PARENT_FAILURE', 402);
    define('PARENT_NOT_EXISTS', 403);
    define('SHIFT_CREATED', 501);
    define('SHIFT_FAILURE', 502);
    define('SHIFT_PK_EXISTS', 503);