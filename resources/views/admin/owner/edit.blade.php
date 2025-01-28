@extends('admin_layouts.app')
@section('content')
<div class="container text-center mt-4">
  <div class="row">
    <div class="col-12 col-md-8 mx-auto">
      <div class="card">
        <!-- Card header -->
        <div class="card-header pb-0">
          <div class="d-lg-flex">
            <div>
              <h5 class="mb-0">Edit Owner</h5>

            </div>
            <div class="ms-auto my-auto mt-lg-0 mt-4">
              <div class="ms-auto my-auto">
                <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.owner.index') }}">
                  <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <form role="form" method="POST" class="text-start" action="{{ route('admin.owner.update',$owner->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="custom-form-group">
              <label for="title">Owner Name <span class="text-danger">*</span></label>
              <input type="text" name="user_name" class="form-control" value="{{$owner->user_name}}" readonly>
              @error('name')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" value="{{$owner->name}}">
              @error('name')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Phone No <span class="text-danger">*</span></label>
              <input type="text" name="phone" class="form-control" value="{{$owner->phone}}">
              @error('phone')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Owner Site Title</label>
              <input type="text" name="site_name" class="form-control" value="{{$owner->site_name}}">
              @error('site_name')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Player Site Title</label>
              <input type="text" name="site_link" class="form-control" value="{{$owner->site_link}}">
              @error('site_link')
              <span class="text-danger d-block">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <label for="title">Owner logo<span class="text-danger">*</span></label>
              <input type="file" class="form-control" id="" name="agent_logo">
              @if($owner->agent_logo)
              <img src="{{asset('assets/img/logo/'. $owner->agent_logo )}}" alt="" width="100px">
              @endif
              @error('agent_logo')
              <span class="text-danger">*{{ $message }}</span>
              @enderror
            </div>
            <div class="custom-form-group">
              <button type="submit" class="btn btn-primary" type="button">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection