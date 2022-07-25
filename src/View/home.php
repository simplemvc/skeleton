<?php $this->layout('layout', ['title' => 'Home SimpleMVC']) ?>

<div>
    <p><img src="/img/logo.png" alt="Logo SimpleMVC"></p>
    <p>Welcome to <a href="https://github.com/simplemvc">SimpleMVC</a> skeleton application for PHP.</p>
    <p>This application contains 3 example controllers corrisponding to the following URLs (stored in <a href="https://github.com/simplemvc/skeleton/blob/main/config/route.php">config/route.php</a>):</p>
    <ul>
        <li><a href="/">/</a>, the home page (this page);</li>
        <li><a href="/hello">/hello[/:name]</a>, a hello page with an optional name (e.g. <a href="/hello/alberto">/hello/alberto</a>)</li>
        <li><a href="/secret">/secret</a>, a protected page with Basic HTTP authentication (with username <strong>test</strong> and password <strong>password</strong>, stored in <a href="https://github.com/simplemvc/skeleton/blob/config/container.php">config/container.php</a>)</li>
    </ul>
    <p>For more information you can read the <a href="https://github.com/simplemvc/skeleton/blob/main/README.md">official documentation</a></p>
</div>
