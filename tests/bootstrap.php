<?php
if (!($loader = @include __DIR__ . '/../vendor/autoload.php')) {
    exit(<<<EOT
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
EOT
    );
}

function validate_email($formValue)
{
    return filter_var($formValue, FILTER_VALIDATE_EMAIL);
}
