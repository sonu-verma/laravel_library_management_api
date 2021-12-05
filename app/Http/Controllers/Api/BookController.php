<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Book;
use App\Models\User;
use App\Models\UserBook;
class BookController extends Controller
{
    private $user;
    public function __construct(){
        //  var_dump('here'); die;  
         $this->middleware('auth:api');
         $this->middleware('isAdmin',['except' => ['index','rentBook','returnBook','rentedUsers']]);
         $this->user =  $this->guard()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::all();
        return response()->json(['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'book_name' => 'required|unique:books',
            'author' => 'required',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),401);
        }

        $imageName = time().'.'.$request->cover_image->extension(); 
        $request->cover_image->move(public_path('images'), $imageName);
        
        
        try{
            \DB::beginTransaction();
        
            $book = Book::create(array_merge(
                $validator->validated(),
                ["cover_image" => $imageName]
            ));
        
            \DB::commit();
            return response()->json(['message' => 'Succussfully added book','book' => $book]);

        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['message' => 'Something went wrong.','book' => '']);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        if(isset($book)){
            $result = ['status' => true , 'book' => $book];
        }else{
            $result = ['message' => false ,'book' => ''];
        }

        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(),[
            'book_name' => 'required|unique:books,book_name,'.$id.',b_id',
            'author' => 'required',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),401);
        }
        $imageName = time().'.'.$request->cover_image->extension(); 

        $request->cover_image->move(public_path('images'), $imageName);
        
        try{
            \DB::beginTransaction();
        
            $book = Book::find($id);
            if(!empty($book)){
                $book->book_name = $request->book_name;
                $book->author = $request->author;
                $book->cover_image = $imageName;
                $book->updated_at = date('Y-m-d h:m:s');
                $book->update();
            }
            \DB::commit();
            return response()->json(['message' => 'Succussfully updated book','book' => $book]);
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['message' => 'Something went wrong.','book' => '']);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        if(!empty($book)){
            $book->delete();
            return response()->json(['message' => 'successfully deleted.'],200);
        }else{
            return response()->json(['message' => 'No Data found.']);
        }
    }
    
    public function rentBook($id){
        $loggedInUserId = $this->user->u_id;
        $book = Book::find($id);
       if(!empty($book)){
            $isAlreadyRented = UserBook::where(['b_id' => $book->b_id,'action_type' => 1])->count();
            if($isAlreadyRented == 1){
                return  response()->json(['message' => 'The name of book '.$book->book_name.' is not available, already rented.']);
            }else{
                $addAsRented = UserBook::insert(['u_id' => $loggedInUserId,'b_id' => $book->b_id,'action_type' => 1,'created_at' => date("Y-m-d H:i:s")]);
                return  response()->json(['message' => 'You have successfully rented '.$book->book_name.' book.']);          
            }
        }else{
            return  response()->json(['message' => 'Requested book is not available in library.']);
        }
    }
    public function returnBook($id){
        $loggedInUserId = $this->user->u_id;
        $book = Book::find($id);
        if(!empty($book)){
            $checkIsRentedByCurrentUser = UserBook::where(['u_id' => $loggedInUserId,'b_id' => $book->b_id,'action_type' => 1])->count();
            if($checkIsRentedByCurrentUser == 0){
                return  response()->json(['message' => 'The name of book '.$book->book_name.' is rented by other user.']);
            }else{
                $addAsRented = UserBook::where(['u_id' => $loggedInUserId,'b_id' => $book->b_id,'action_type' => 1])->update(['action_type' => 2]);
                return  response()->json(['message' => 'You have successfully return '.$book->book_name.' book.']);          
            }
        }else{
            return  response()->json(['message' => 'Requested book is not available in library.']);
        }
    }

    public function rentedUsers(){
        $users = User::with('book')->get();

        return response()->json($users);
    }

    protected function guard(){
        return Auth::guard();
    }

}
