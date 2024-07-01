<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Book;

use Validator;

class BookController extends Controller
{
    public function index(Request $request) {
        $data = Book::get();
        return response()->json($data, 200);        
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'author' => 'required',
        ]);

        $error = 0;
        $a = 0;
        $data = array();
        $data['errors'] = [];

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            foreach ($errors as $value) {
                $data['errors'][$a] = $value[0];
                $a++;
            }
            $error = 1;
        }

        if ($error == 1) {
            $data['status'] = 'error';
            return response()->json($data, 400);
        } else {
            return response()->json(DB::transaction(function () use ($request) {
                $Book =  Book::create([
                    'title' => $request->title,
                    'author' => $request->author,
                ]);
                $data['status'] = 'success';
                $data['message'] = 'Success add Book';

                return $data;
            }), 200);
        }
    }

    public function update(Request $request, $id) {
        $detail = Book::findOrFail($id);
        return response()->json(DB::transaction(function () use ($request,$detail) {
            $detail->update([
                'title' => $request->title,
                'author' => $request->author,
            ]);
            $data['status'] = 'success';
            $data['message'] = 'Success update data Book';

            return $data;
        }), 200);
    }

    public function destroy($id) {
        $detail = Book::findOrFail($id);

        return response()->json(DB::transaction(function () use ($detail) {
            $detail->delete();

            $data['status'] = 'success';
            $data['message'] = 'Success delete data Book';

            return $data;
        }), 200);
    }
}
