<?php $this->layout('layout', ['title' => 'Home SimpleMVC']) ?>

<main class="home">
    <p><img src="/img/logo.png" alt="Logo SimpleMVC"></p>
    <p>Welcome to <a href="https://github.com/simplemvc">SimpleMVC</a> skeleton application for PHP.</p>
    <p>This application contains 4 example controllers corrisponding to the following URLs (stored in <a href="https://github.com/simplemvc/skeleton/blob/main/config/route.php">config/route.php</a>):</p>
    <ul>
        <li><a href="/">/</a>, the home page (this page);</li>
        <li><a href="/hello">/hello[/:name]</a>, a hello page with an optional name (e.g. <a href="/hello/alberto">/hello/alberto</a>)</li>
        <li><a href="/basic-auth">/basic-auth</a>, a page protected by <a href="https://en.wikipedia.org/wiki/Basic_access_authentication">Basic access authentication</a> (<strong>test</strong>/<strong>1234567890</strong>, in config/config.php)</li>
        <li><a href="/login">/login</a>, a login FORM (<strong>admin</strong>/<strong>supersecret</strong>, stored in a SQLite database, <a href="https://github.com/simplemvc/skeleton/blob/data/db.sql">data/db.sql</a>)</li>
        <li><a href="/admin/users">/admin/users</a>, an admin page for users management (reserved, requires login)</li>
    </ul>
    <p>For more information you can read the <a href="https://github.com/simplemvc/skeleton/blob/main/README.md">official documentation</a></p>
</main>