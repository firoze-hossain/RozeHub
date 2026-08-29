<?php
return [
    'signing_public_key' => env('ROZEHUB_RELEASE_SIGNING_PUBLIC_KEY'),
    'require_signature' => filter_var(env('ROZEHUB_RELEASE_REQUIRE_SIGNATURE', false), FILTER_VALIDATE_BOOL),
    'default_rollout_percentage' => (int) env('ROZEHUB_RELEASE_ROLLOUT_PERCENTAGE', 100),
];
