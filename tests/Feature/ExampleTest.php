<?php

it('mengalihkan halaman utama ke login', function () {
    $this->get('/')->assertRedirect(route('login'));
});