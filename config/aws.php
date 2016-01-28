<?php

return [
    'key'          => env('AWS_KEY'),
    'secret'       => env('AWS_SECRET'),
    'region'       => 'eu-west-1',
    'config_file'  => null,
    'bucket'       => 'hqmirror',

    'pa_key'       => env('AWS_PA_KEY'),
    'pa_secret'    => env('AWS_PA_SECRET'),
    'country'      => 'DE',
    'associateTag' => 'hqmi-21',
];