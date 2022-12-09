<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index()
    {
        //$books = Book::all();
        $books = Book::with('bookDownload', 'category', 'editorial', 'authors')->get();
        return $this->getResponse200($books);
    }

    public function response()
    {
        return [
            "error" => true,
            "message" => "",
            "data" => []
        ];
    }

    public function store(Request $request)
    {
        //$response = $this->response();
        $isbn = trim($request->isbn);
        $existIsbn = Book::where('isbn', $isbn)->exists();
        if (!$existIsbn) {
            $book = new Book();
            $book->isbn = $isbn;
            $book->title = $request->title;
            $book->description = $request->description;
            $book->published_date = Carbon::now();
            $book->category_id = $request->category['id'];
            $book->editorial_id = $request->editorial['id'];
            $book->save();
            foreach ($request->authors as $item) {
                $book->authors()->attach($item);
            }


            //$response["error"] = false;
            //$response["message"] = "Your book has been created!";
            //$response["data"] = $book;
            $response = $this->getResponse201("book", "created", $book);


        } else {
            $response =  $this->getResponse400("ISBN duplicated"); ;
        }
        return $response;
    }

    public function update(Request $request, $id)
    {
        //$response = $this->response();
        $book = Book::find($id);

        DB::beginTransaction();
        try {

            if ($book) {
                $isbn = trim($request->isbn);
                $isbnOwner = Book::where('isbn', $isbn)->first();
                if (!$isbnOwner || $isbnOwner->id == $book->id) {
                    $book->isbn = $isbn;
                    $book->title = $request->title;
                    $book->description = $request->description;
                    $book->published_date = Carbon::now();
                    $book->category_id = $request->category['id'];
                    $book->editorial_id = $request->editorial['id'];
                    $book->update();
                    //Delete
                    foreach ($book->authors as $item) {
                        $book->authors()->detach($item);
                    }
                    //Add new authors
                    foreach ($request->authors as $item) {
                        $book->authors()->attach($item);
                    }



                    $book = Book::with('category', 'editorial', 'authors')->where('id', $id)->get();
                    $response = $this->getResponse201("book", "updated", $book);
                    //$response["error"] = false;
                    //$response["message"] = "Your book has been updated!";
                    //$response["data"] = $book;
                } else {

                    //$response["message"] = "ISBN duplicated!";
                    $response = $this->getResponse400("ISBN duplicated");
                }
            } else {
                $response = $this->getResponse404();
                //$response["message"] = "Not found";
            }

            DB::commit();
        } catch (Exception $e) {
            //$response["message"] = "Rollback transaction";
            $response = $this->getResponse500([$e->getMessage()]);

            DB::rollBack();
        }
        return $response;
    }

    public function show($id){
        $book = Book::find($id);
        return $this->getResponse200($book);
    }

    public function destroy($id){
        $book = Book::find($id);
        if($book){
            $book->delete();
            return $this->getResponseDelete200("book");
        }
        return $this->getResponse404();
    }

}
