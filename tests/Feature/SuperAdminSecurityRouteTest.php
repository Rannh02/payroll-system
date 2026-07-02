<?php

it('registers the superadmin security logs route', function () {
    $this->assertTrue(route('superadmin.security_logs') !== null);
    $this->assertTrue(app('router')->has('superadmin.security_logs'));
});
