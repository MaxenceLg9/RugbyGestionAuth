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