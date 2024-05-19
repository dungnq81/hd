<?php return array(
    'root' => array(
        'name' => 'mu-plugins/mu-hd',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => '244fda4a3b15b394b9ac1f165d53622796f835ea',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'mu-plugins/mu-hd' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '244fda4a3b15b394b9ac1f165d53622796f835ea',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roave/security-advisories' => array(
            'pretty_version' => 'dev-latest',
            'version' => 'dev-latest',
            'reference' => '4a1ce99ead02e286f024fc7d032c7233ac01091b',
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
