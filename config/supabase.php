<?php

return [
    'url' => rtrim((string) env('SUPABASE_URL', ''), '/'),
    'anon_key' => env('SUPABASE_ANON_KEY', ''),
    // Used only to append dashboard authentication events, never for domain reads/writes.
    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY', ''),
    'timeout' => 10,
    'connect_timeout' => 3,
];
