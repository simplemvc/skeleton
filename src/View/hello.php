<?php $this->layout('layout', ['title' => 'Hello example']) ?>

<main class="home">
    <h1 align="center">Hi <?= $this->e($name) ?>!</h1>
    <p align="center">Change the name in the URL, eg. <b>/hello/foo</b></p>
</main>