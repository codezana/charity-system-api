<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\ {Hash,Validator};

    class AuthController extends Controller {

        //index method

        public function index() {
            $user = auth()->user();

            if ( !$user ) {
                return response()->json( [ 'error' =>  'بەکارهێنەر مۆڵەتی ڕێگەپێدانی نییە' ], 401 );
            }

            if ( $user->role != 'admin' ) {
                return response()->json( [ 'error' => 'بێ مۆڵەت : تۆ ئەدمین نیت' ], 403 );
            }

            return response()->json( User::all() );
        }

        //View user

        public function show( $id ) {

            if ( auth()->user()->role != 'admin' ) {
                return response()->json( [ 'error' => 'بێ مۆڵەت : تۆ ئەدمین نیت' ], 401 );
            }
            $user = User::findOrFail( $id );
            return response()->json( $user );
        }

        //reset password

        public function resetPassword( Request $request ) {

            if ( auth()->user()->role != 'admin' ) {
                return response()->json( [ 'error' => 'بێ مۆڵەت : تۆ ئەدمین نیت' ], 403 );
            }

            $request->validate( [
                'password' => 'required',
            ] );

            $user = User::findOrFail( $request->id );

            $user->password = Hash::make( $request->password ?? 12345678 );
            $user->save();

            return response()->json( [
                'message' =>trans( 'passwords.reset' ),
                'user' => $user
            ] );
        }

        //login

        public function login( Request $request ) {

            $request->validate( [
                'name' => 'required',
                'password' => 'required',
            ] );

            $user = User::whereRaw( 'LOWER(name) = ?', [ strtolower( $request->name ) ] )->first();

            if ( !$user || !Hash::check( $request->password, $user->password ) ) {
                return response()->json( [ 'error' => trans( 'auth.failed' ) ], 401 );
            }

            // Issue the token
            $token = $user->createToken( 'CharityApp' )->accessToken;

            return response()->json( [
                'message' => 'چوونەژوورەوە سەرکەوتوو بوو',
                'user' => $user,
                'token' => $token
            ] );
        }

        //logout

        public function logout( Request $request ) {
            // Invalidate the token ( delete it )
            $request->user()->tokens->each ( function ( $token ) {
                $token->delete();
            }
        );

        return response()->json( [
            'message' => 'بەسەرکەوتووی چووەدەرەوە'
        ] );
    }

    //create user

    public function store( Request $request ) {

        if ( !(auth()->user()->role == 'admin' ) ) {
            return response()->json( [ 'error' => 'بێ مۆڵەت : تۆ ئەدمین نیت' ], 401 );
        }

        $validatedData = $request->validate( [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff',
        ] );

        $user = User::create( [
            'name' => $validatedData[ 'name' ],
            'password' => Hash::make( $validatedData[ 'password' ] ),
            'role' => $validatedData[ 'role' ]
        ] );

        return response()->json( [
            'message' => 'بەکارهێنەر بە سەرکەوتوویی دروست کرا',
            'user' => $user
        ] );
    }

    //update user

    public function update( Request $request, $id ) {

        if ( !(auth()->user()->role == 'admin' ) ) {
            return response()->json( [ 'error' => 'بێ مۆڵەت : تۆ ئەدمین نیت' ], 401 );
        }

        $user = User::findOrFail( $id );

        $validator = Validator::make( $request->all(), [
            'name' => 'string',
            'role' => 'in:admin,staff' // Use 'in' instead of 'enum'
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'error' => collect( $validator->errors()->all() )->first()
            ], 422 );
        }

        $user->name = $request->input( 'name' );
        $user->role = $request->input( 'role' );
        $user->save();

        return response()->json( [ 'message' => 'بەکارهێنەر بە سەرکەوتوویی نوێکرایەوە' ] );
    }

    //delete user

    public function destroy( $id ) {

        if ( !(auth()->user()->role == 'admin' ) ) {
            return response()->json( [ 'error' => 'بێ مۆڵەت : تۆ ئەدمین نیت' ], 401 );
        }


        // if that user is user who loged cant delete
        if ( auth()->user()->id == $id ) {
            return response()->json( [ 'error' => 'ناتوانی خۆت بسڕیتەوە' ], 403 );
        }

        $user = User::findOrFail( $id );

        $user->delete();
        return response()->json( [ 'message' => 'بەکارهێنەر بە سەرکەوتوویی سڕایەوە' ] );
    }
}

