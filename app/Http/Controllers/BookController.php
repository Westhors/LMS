<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class BookController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(BookRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Books = BookResource::collection($this->crudRepository->all(['teacher', 'stage', 'subject'], [], ['*']));
            return $Books->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(BookRequest $request)
    {
        try {
           $Books = $this->crudRepository->create($request->validated());
           if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $Books);
           }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(Book $book): ?\Illuminate\Http\JsonResponse
    {
        try {
            $book->load(['teacher']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new BookResource($book));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(BookRequest $request, Book $book): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $book->id);
            if ($request->filled('image')) {
                $book = Book::find($book->id);
                $this->crudRepository->AddMediaCollection('image', $book);
            }
            activity()->performedOn($book)->withProperties(['attributes' => $book])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('books', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Book::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Book::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Book::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

