<?php

return [
    'secret_key' => $_ENV['JWT_SECRET_KEY'],
    'issuer' => $_ENV['JWT_ISSUER'],
    'audience' => $_ENV['JWT_AUDIENCE'],
    'expiration_time' => $_ENV['JWT_EXPIRATION_TIME'],
    'algorithm' => 'HS256',
];
