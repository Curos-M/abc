<?php

namespace App\Http\Controllers;

use App\Book;
use App\Author;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        // @TODO implement
        $book = Book::leftJoin('book_author as ba', 'books.id', '=', 'ba.book_id')
        ->leftJoin('authors as a', 'ba.author_id', '=', "a.id")
        ->leftJoin('book_reviews as br', 'books.id', '=', 'br.book_id')
        ->when($request->authors, function ($query, $filter){
          $explode = explode (",", $filter);
          $query->whereIn('a.id', $explode);
        })
        ->when($request->title, function ($query, $filter){
          $query->where('title', 'like', '%'.$filter.'%');
        })
        ->select([
          'books.id as id',
          'isbn',
          'title',
          'description',
          'published_year',
          DB::raw('avg(review) as avg_review, count(br.id) as count'),
        ])
        ->groupBy('books.id')
        ->orderBy($request->sortColumn??"books.id", $request->sortDirection??'asc');
        
        $data = $book->paginate();

        $data->map(function ($item){
          $authors = Author::leftJoin('book_author as ba', 'authors.id', '=', 'ba.author_id')
          ->where('ba.book_id', $item->id)->select('id','name','surname')->get();
          if($authors)
            $item['authors'] = $authors;
          return $item;
        });

        return BookResource::collection($data);
    }

    public function store(PostBookRequest $request)
    {
        // @TODO implement
      $book = new Book();

      $id = $book->insertGetId([
        'isbn' => $request->isbn,
        'title' => $request->title,
        'description' => $request->description,
        'published_year' => $request->published_year
      ]);
      $book = $book->find($id);
      $book->authors()->attach($request->authors);
      $book = $book->find($id);
      $br = new BookResource($book);
      return $br->response()->setStatusCode(201);
    }
}
