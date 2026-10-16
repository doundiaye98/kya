<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
logout_user();
flash('Vous êtes déconnecté.');
redirect('/kya/login.php');
