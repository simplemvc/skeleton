<?php $this->layout('layout', ['title' => 'Home SimpleMVC']) ?>

<h1>Secret page</h1>

<p>This page can be accessed using the credential:</p>
<p><strong>username</strong>: <?= $this->e($username)?></p>
<p><strong>password</strong>: <?= $this->e($password)?></p>