<?php

namespace Token {

    use function Entraineur\getEntraineur;


    function generate_jwt($headers, $payload, $secret): string {
        $headers_encoded = base64url_encode(json_encode($headers));

        $payload_encoded = base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = base64url_encode($signature);

        return $headers_encoded . '.' . $payload_encoded . '.' . $signature_encoded;
    }

    function is_jwt_valid($jwt, $secret): bool {
        // split the jwt
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) != 3) {
            return FALSE;
        }
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;

        // build a signature based on the header and payload using the secret
        $base64_url_header = base64url_encode($header);
        $base64_url_payload = base64url_encode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = base64url_encode($signature);

        // verify it matches the signature provided in the jwt
        $is_signature_valid = ($base64_url_signature === $signature_provided);

        if ($is_token_expired || !$is_signature_valid) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function base64url_encode($data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function get_authorization_header(): ?string {
        $headers = null;
        if (isset($_COOKIE["token"])) {
            return $_COOKIE["token"];
        }
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } else if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        return $headers;
    }

    function get_bearer_token(): ?string {
        $headers = get_authorization_header();

        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                if ($matches[1] == 'null') //$matches[1] est de type string et peut contenir 'null'
                    return null;
                else
                    return $matches[1];
            }
        }
        return null;
    }

    function encode($login, $id): string
    {
        $headers = array("alg" => "HS256", "typ" => "JWT");
        $payload = array("login" => $login, "id" => $id, "exp" => time() + 1800);
        return "Bearer " . generate_jwt($headers, $payload, "bar-mitzvah");
    }

    function is_valid_token($jwt): bool {
        if(is_jwt_valid($jwt, "bar-mitzvah"))
            if(getEntraineur(getPayload($jwt)["id"]) != [])
                return true;
        return false;
    }

    function refreshJwt($jwt): string {
        $payload = getPayload($jwt);
        return encode($payload["login"], $payload["id"]);
    }

    function getPayload($token = ""): array {
        if ($token == "")
            $token = get_bearer_token();
        $tokenParts = explode('.', $token);
        return json_decode(base64_decode($tokenParts[1]), true);
    }

}