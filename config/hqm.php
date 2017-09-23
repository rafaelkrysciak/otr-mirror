<?php

return [
    'database_cleanup_older_then' => '6 WEEK',
    'keep_files_on_all_nodes_days' => 1,
    'download_files_not_older_then_days' => 10,
    'retry_download_after_minutes' => 30,
    'recaptcha_secret' => env('RECAPTCHA_SECRET'),
];