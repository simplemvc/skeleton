<?php $this->layout('layout', ['title' => 'Home SimpleMVC']) ?>

<main class="home">
    <h1>Secret page</h1>

    <p>This page can be accessed using the credential:</p>
    <ul>
        <li><strong>username</strong>: <?= $this->e($username)?></li>
        <li><strong>password</strong>: <?= $this->e($password)?></li>
    </ul>
</main>