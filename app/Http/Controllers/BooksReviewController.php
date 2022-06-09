<?php

namespace App\Http\Controllers;

use App\BookReview;
use App\Book;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class BooksReviewController extends Controller
{
    public function __construct()
    {

    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        // @TODO implement
      $bookReview = new BookReview();
      $book = Book::find($bookId);

      if(!$book){
        throw new HttpResponseException(response()->json([], 404));
      }

      $bookReview->review = $request->review;
      $bookReview->comment = $request->comment;
      $bookReview->book_id = $bookId;
      $bookReview->user_id = Auth::id();
      $bookReview->save();
      $data = $bookReview->leftJoin('users as u', 'book_reviews.user_id', '=', 'u.id')
      ->select([
        'book_reviews.id as id',
        'review',
        'comment',
        'u.id as u_id',
        'u.name as u_name'
      ])
      ->find($bookReview->id);

      $brr = new BookReviewResource($data);
      return $brr->response()->setStatusCode(201);
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        // @TODO implement
        $bookReview = new BookReview();
        $review = $bookReview->find($reviewId);
        if($review){
          $book = $review->book()->find($bookId);
          if($book){
            $book->delete();
            $review->delete();
          }
          throw new HttpResponseException(response()->json('', 204));
        }else{
          throw new HttpResponseException(response()->json([], 404));
        }
        
    }
}
