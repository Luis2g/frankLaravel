<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Exception;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $author = Author::all();
        return $this->getResponse200($author);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try{
            $author = new Author();
            $author->name = $request->name;
            $author->first_surname = $request->first_surname;
            $author->second_surname = $request->second_surname;
            $author->save();

            $response = $this->getResponse201("author", "created", $author);
        }catch(Exception $ex){
            $response = $this->getResponse400($ex);
        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $author = Author::find($id);
        if($author){
            return $this->getResponse200($author);
        }

        return $this->getResponse404();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $author = Author::find($id);

        if(!$author){
            return $this->getResponse404();
        }


        try{
            $author->name = $request->name;
            $author->first_surname = $request->first_surname;
            $author->second_surname = $request->second_surname;
            $author->update();

            $response = $this->getResponse201("author", "updated", $author);
        }catch(Exception $ex){
            $response = $this->getResponse400($ex);
        }

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $author = Author::find($id);
        if($author){
            $author->delete();
            return $this->getResponseDelete200("author");
        }
        return $this->getResponse404();
    }
}
