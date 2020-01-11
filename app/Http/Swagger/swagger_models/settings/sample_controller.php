<?php

class SampleController //This is a sample laravel Controller

{

    ////////////////////////////////////////////
    ///******** AUTH **********////
    ////////////////////////////////////////////

    /////////////// LOGIN
    /**
     * @OA\POST(
     *     path="/icr/public/api/login",
     *     tags={"Auth"},
     *     summary="Create list of users with given input array",
     *     operationId="createUsersWithListInput",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     * )
     */

    ////////////// AUTH USER
    /**
     * @OA\GET(
     *     path="/icr/public/api/auth/user",
     *     tags={"Auth"}, 
     *     summary="Auth User Login",
     *     description="Auth User Login",
     *     operationId="auth",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    ////////////////////////////////////////////
    /* USER */
    ////////////////////////////////////////////
    
    /////////////// GET USER BY ID
    /**
     * @OA\GET(
     *     path="/icr/public/api/user/getUserById",
     *     tags={"User"},
     *     summary="Auth User Login",
     *     description="ambil data user dari id",
     *     operationId="user",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         @OA\Schema(
     *             type="int",
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */


    ////////////////////////////////////////////
    /* MENU */
    ////////////////////////////////////////////

    // get user menu
    /**
     * @OA\GET(
     *     path="/icr/public/api/menu/getMenuTree",
     *     tags={"Menu"}, 
     *     summary="Get User Menu",
     *     description="Menutree untuk navbar",
     *     operationId="menu",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    /////////// SAVE MENU
    /**
     * @OA\POST(
     *     path="/icr/public/api/menu/saveMenu",
     *     tags={"Auth"},
     *     summary="Create list of users with given input array",
     *     operationId="createUsersWithListInput",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     * )
     */
    public function sampleFunctionWithDoc()

    {

    }
}

