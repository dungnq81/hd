<?php return array(
    'root' => array(
        'name' => 'mu-plugins/mu-hd',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => 'd463f5b2b196767d02a69f4214e8edf53880b8c6',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'mu-plugins/mu-hd' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'd463f5b2b196767d02a69f4214e8edf53880b8c6',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roave/security-advisories' => array(
            'pretty_version' => 'dev-latest',
            'version' => 'dev-latest',
            'reference' => '0ec6bad5f4523eda2514e89d5ea48cde6ebe7f99',
            'type' => 'metapackage',
            'install_path' => null,
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
        'roots/wp-password-bcrypt' => array(
            'pretty_version' => '1.1.0',
            'version' => '1.1.0.0',
            'reference' => '15f0d8919fb3731f79a0cf2fb47e1baecb86cb26',
            'type' => 'library',
            'install_path' => __DIR__ . '/../roots/wp-password-bcrypt',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
