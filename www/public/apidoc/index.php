<?php

/**
 * @api {post} /auth.php 1 Register a User
 * @apiName RegisterAuth
 * @apiGroup Auth
 * @apiDescription Handles login, registration, or token validation based on the provided request body.
 *
 * @apiBody (Register) {String} nom Last name.
 * @apiBody (Register) {String} prenom First name.
 * @apiBody (Register) {String} equipe Team name.
 * @apiBody (Register) {String} email User's email.
 * @apiBody (Register) {String} password Password.
 * @apiBody (Register) {String} confirmpassword Must match password.
 *
 * @apiExample {json} Request-Example:
 *  {
 *      "nom":"nom",
 *      "prenom":"prenom",
 *      "email":"nom.p@mail.fr",
 *      "equipe":"Real Madrid",
 *      "password":"aa",
 *      "confirmpassword": "aa"
 * }
 *
 * @apiSuccess (200 OK) {String} response OK
 * @apiSuccess (200 OK) {Integer} Status 200
 *
 * @apiSuccessExample {json} Success-Response:
 *        {
 *          "status": 200,
 *          "response": "OK",
 *        }
 *
 * @apiError (400 Bad Request) {String} response Error message
 * @apiError (400 Bad Request) {String} response Passwords do not match
 * @apiError (400 Bad Request) {String} response Erreur lors de la création de l'utilisateur
 * @apiError (405 Method Not Allowed) {String} response Un utilisateur existe déjà
 */

/**
 * @api {post} /auth.php 2 Login a User
 * @apiName LoginAuth
 * @apiGroup Auth
 * @apiDescription Handles login
 *
 * @apiBody (Login) {String} email User's email.
 * @apiBody (Login) {String} password User's password.
 *
 * @apiExample {json} Request-Example:
 *   {
 *       "email":"nom.p@mail.fr",
 *       "password":"aa",
 *  }
 *
 * @apiSuccess (200 OK) {String} response OK
 * @apiSuccess (200 OK) {String} token JWT token
 *
 * @apiSuccessExample {json} Success-Response:
 *       {
 *         "status": 200,
 *         "response": "OK",
 *         "token": "token"
 *       }
 *
 * @apiError (400 Bad Request) {String} response Invalid login or password
 */

/**
 * @api {post} /auth.php 3 Check the validity of a Token
 * @apiName CheckTokenAuth
 * @apiGroup Auth
 * @apiDescription Check if the token is valid
 *
 * @apiBody (Token Validation) {String} token JWT token to validate.
 *
 * @apiExample {json} Request-Example:
 *    {
 *        "token":"header.payload.signature",
 *   }
 *
 * @apiSuccess (200 OK) {String} response OK
 * @apiSuccess (200 OK) {Boolean} valid true if token is valid
 *
 * @apiSuccessExample {json} Success-Response:
 *       {
 *         "status": 200,
 *         "response": "OK",
 *         "valid": "true/false"
 *       }
 *
 * @apiError (400 Bad Request) {String} response Please provide a proper data
 */

/**
 * @api {put} /auth.php 4 Refresh JWT Token
 * @apiName RefreshToken
 * @apiGroup Auth
 * @apiDescription Refreshes the JWT token expiration time if the token is valid.
 *
 * @apiBody {String} token JWT token to refresh.
 *
 * @apiExample {json} Request-Example:
 *     {
 *         "token":"header.payload.signature",
 *    }
 *
 *
 * @apiSuccess (200 OK) {String} response OK
 * @apiSuccess (200 OK) {String} token Refreshed JWT token
 *
 * @apiSuccessExample {json} Success-Response:
 *      {
 *        "status": 200,
 *        "response": "OK",
 *        "token": "token"
 *      }
 *
 * @apiError (400 Bad Request) {String} response Please provide a proper data
 * @apiError (405 Method Not Allowed) {String} response Token is invalid
 */

/**
 * @api {options} /auth.php 5 Preflight (CORS)
 * @apiName AuthOptions
 * @apiGroup Auth
 * @apiDescription Handles CORS preflight request.
 *
 * @apiSuccess (200 OK) {String} response Options ok
 * @apiSuccess (200 OK) {Array} data Empty array
 */
