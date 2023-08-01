<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str; //String Random
use Illuminate\Support\Facades\Storage; //Panggil Class Untuk Menghapus Image
use App\Models\Movie; //Panggil Model Movie
use Illuminate\Database\Eloquent\SoftDeletes; //Untuk melakukan softdeletes

class MovieController extends Controller
{
    public function index(){
        $movies = Movie::all();
        return view('admin.movies', ['movies' => $movies]);
    }

    public function create(){
        return view('admin.movie-create');
    }

    //Create ke Database
    public function store(Request $request){
        // $data = $request->all();
        $data = $request->except('_token');

        //validasi data
        $request->validate([
            'title' => 'required|string',
            'small_thumbnail' => 'required|image|mimes:jpeg,png,jpg',
            'large_thumbnail' => 'required|image|mimes:jpeg,png,jpg',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about' => 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required|string',
        ]);

        //upload gambar
        $smallThumbnail = $request->small_thumbnail;
        $largeThumbnail = $request->large_thumbnail;
        $modifiedSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName();
        $modifiedLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();

        $smallThumbnail->storeAs('public/thumbnail', $modifiedSmallThumbnailName);
        $largeThumbnail->storeAs('public/thumbnail', $modifiedLargeThumbnailName);

        //modified + upload data
        $data['small_thumbnail'] = $modifiedSmallThumbnailName;
        $data['large_thumbnail'] = $modifiedLargeThumbnailName;

        Movie::create($data);

        return redirect()->route('admin.movie')->with('success', 'Movie berhasil dibuat');
    }

    public function edit($id){
        $movie = Movie::find($id);

        return view('admin.movie-edit', ['movie' => $movie]);
    }


    //Edit data dari Database
    public function update(Request $request, $id){
        $data = $request->except('_token');
        //validasi data
        $request->validate([
            'title' => 'required|string',
            'small_thumbnail' => 'image|mimes:jpeg,png,jpg',
            'large_thumbnail' => 'image|mimes:jpeg,png,jpg',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about' => 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required|string',
        ]);

        $movie = Movie::find($id);

        //Jika small thumbnail diisi di edit
        if($request->small_thumbnail){
            //Upload gambar baru
            $smallThumbnail = $request->small_thumbnail;
            $modifiedSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName();
            $smallThumbnail->storeAs('public/thumbnail', $modifiedSmallThumbnailName);
            $data['small_thumbnail'] = $modifiedSmallThumbnailName;

            //Delete gambar lama
            Storage::delete('public/thumbnail/' . $movie->small_thumbnail);
        }

        //Jika large thumbnail diisi di edit
        if($request->large_thumbnail){
            //Upload gambar baru
            $largeThumbnail = $request->large_thumbnail;
            $modifiedLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();
            $largeThumbnail->storeAs('public/thumbnail', $modifiedLargeThumbnailName);
            $data['large_thumbnail'] = $modifiedLargeThumbnailName;

            //Delete gambar lama
            Storage::delete('public/thumbnail/' . $movie->large_thumbnail);
        }

        $movie->update($data);

        return redirect()->route('admin.movie')->with('success', 'Movie berhasil diupdate');
    }

    public function delete(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return redirect()->route('admin.movie')->with('error', 'Movie berhasil dihapus');
    }
}
