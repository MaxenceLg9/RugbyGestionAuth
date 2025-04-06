<?php
$title = "Authentication Endpoint - /auth.php";
$description = "Handles user authentication for the API. Supports login, registration, token validation, and token refresh.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
</head>
<body>
<h1><?= $title ?></h1>
<p><?= $description ?></p>

<h2>🔐 Authentication</h2>
<p><strong>Required:</strong> No (Token only needed for validation or refresh)</p>

<h2>🧭 Methods Available</h2>

<h3>POST</h3>
<p>Handles login, registration, and token validation depending on provided body parameters.</p>

<h3>PUT</h3>
<p>Refreshes the JWT token if valid.</p>

<h3>OPTIONS</h3>
<p>Handles preflight request. Responds with basic CORS headers.</p>

<h2>📤 Request Parameters</h2>

<h3>POST - Login</h3>
<ul>
    <li><code>email</code> (string) - User's email address.</li>
    <li><code>password</code> (string) - User's password.</li>
</ul>
<pre><code>curl -X POST https://yourdomain.com/auth.php \
-H "Content-Type: application/json" \
-d '{"email":"test@example.com","password":"123456"}'</code></pre>

<h3>POST - Registration</h3>
<ul>
    <li><code>nom</code> (string) - Last name.</li>
    <li><code>prenom</code> (string) - First name.</li>
    <li><code>equipe</code> (string) - Team name.</li>
    <li><code>email</code> (string) - Email address.</li>
    <li><code>password</code> (string) - Password.</li>
    <li><code>confirmpassword</code> (string) - Password confirmation (must match <code>password</code>).</li>
</ul>
<pre><code>curl -X POST https://yourdomain.com/auth.php \
-H "Content-Type: application/json" \
-d '{"nom":"Doe","prenom":"John","equipe":"Team A","email":"john@example.com","password":"123456","confirmpassword":"123456"}'</code></pre>

<h3>POST - Token Validation</h3>
<ul>
    <li><code>token</code> (string) - The JWT token to validate.</li>
</ul>
<pre><code>curl -X POST https://yourdomain.com/auth.php \
-H "Content-Type: application/json" \
-d '{"token":"your.jwt.token"}'</code></pre>

<h3>PUT - Refresh Token</h3>
<ul>
    <li><code>token</code> (string) - The JWT token to refresh.</li>
</ul>
<pre><code>curl -X PUT https://yourdomain.com/auth.php \
-H "Content-Type: application/json" \
-d '{"token":"your.jwt.token"}'</code></pre>

<h2>✅ Success Responses</h2>

<h3>POST - Login Success</h3>
<pre><code>{
    "response": "OK",
    "status": 200,
    "token": "jwt.token.value"
}</code></pre>

<h3>POST - Registration Success</h3>
<pre><code>{
    "response": "OK",
    "status": 200
}</code></pre>

<h3>POST - Token Valid</h3>
<pre><code>{
    "response": "OK",
    "status": 200,
    "valid": true
}</code></pre>

<h3>PUT - Token Refreshed</h3>
<pre><code>{
    "response": "OK",
    "status": 200,
    "token": "new.jwt.token"
}</code></pre>

<h2>❌ Failure Responses</h2>

<h3>POST - Missing or Invalid Data</h3>
<pre><code>{
    "response": "Please provide a proper data",
    "status": 400
}</code></pre>

<h3>POST - Invalid Credentials</h3>
<pre><code>{
    "response": "Invalid login or password",
    "status": 400
}</code></pre>

<h3>POST - Passwords Do Not Match</h3>
<pre><code>{
    "response": "Passwords do not match",
    "status": 400
}</code></pre>

<h3>POST - User Already Exists</h3>
<pre><code>{
    "response": "Un utilisateur existe déjà",
    "status": 400
}</code></pre>

<h3>PUT - Token Invalid</h3>
<pre><code>{
    "response": "Token is invalid",
    "status": 405,
    "token": ""
}</code></pre>

<h2>📎 Notes</h2>
<ul>
    <li>This endpoint supports CORS.</li>
    <li>All requests and responses are JSON encoded.</li>
    <li>Ensure valid and matching fields are sent when registering users.</li>
</ul>
</body>
</html>
