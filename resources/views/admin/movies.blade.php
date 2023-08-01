@extends('admin.layouts.base');
@section('title', 'Movies');
@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session()->has('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if(session()->has('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Movies</h3>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <a href="{{ route('admin.movie-create') }}" class="btn btn-warning">Create Movie</a>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-md-12">
              <table id="movie" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Id</th>
                    <th>Title</th>
                    <th>Small Thumbnail</th>
                    <th>Large Thumbnail</th>
                    <th>Categories</th>
                    <th>Casts</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach ($movies as $movie)
                    <tr>
                      <td>{{ $movie->id }}</td>
                      <td>{{ $movie->title }}</td>
                      <td><img src="{{ asset('storage/thumbnail/'.$movie->small_thumbnail) }}" alt="" width="50%"/></td>
                      <td><img src="{{ asset('storage/thumbnail/'.$movie->large_thumbnail) }}" alt="" width="50%"/></td>
                      <td>{{ $movie->categories }}</td>
                      <td>{{ $movie->casts }}</td>
                      <td>
                        <a href="{{ route('admin.movie-edit', ['id' => $movie->id]) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                        <form action="{{ route('admin.movie-delete', ['id' => $movie->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endsection

  @section('js')
  <script>
    $('#movie').DataTable();
  </script>
  @endsection
