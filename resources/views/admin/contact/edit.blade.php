@extends('admin_layouts.app')
@section('styles')
<style>
  .transparent-btn {
    background: none;
    border: none;
    padding: 0;
    outline: none;
    cursor: pointer;
    box-shadow: none;
    appearance: none;
    /* For some browsers */
  }


  .custom-form-group {
    margin-bottom: 20px;
  }

  .custom-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
  }

  .custom-form-group input,
  .custom-form-group select {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e1e1e1;
    border-radius: 5px;
    font-size: 16px;
    color: #333;
  }

  .custom-form-group input:focus,
  .custom-form-group select:focus {
    border-color: #d33a9e;
    box-shadow: 0 0 5px rgba(211, 58, 158, 0.5);
  }

  .submit-btn {
    background-color: #d33a9e;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
  }

  .submit-btn:hover {
    background-color: #b8328b;
  }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-icons@1.13.12/iconfont/material-icons.min.css">
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.contact.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Edit Bank</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.contact.update', $contact->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="custom-form-group">
                  <label for="title">Contact Type <span class="text-danger">*</span></label>
                  <div class="custom-select-wrapper">
                    <select name="contact_type_id" class="form-control custom-select">
                    @foreach ($contact_types as $type)
                      <option value="{{ $type->id}}"
                        {{ $contact->contact_type_id == $type->id ? 'selected' : ''}}>{{$type->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                  <div class="custom-form-group">
                    <label for="title">Link</label>
                    <input type="text" class="form-control" id="" name="link" value="{{$contact->link}}">
                    @error('link')
                    <span class="text-danger d-block">*{{ $message }}</span>
                    @enderror
                  </div>
                  
                  @if(Auth::user()->hasRole('Owner'))
                  <div class="mb-3">
                    <div class="d-flex">
                      <div class="me-2 single" id="single">
                        <label for="single" class="form-label">
                          <input type="radio"
                            name="type"
                            value="single"
                            class=" me-2"
                            id="single" {{$contact->contactAgents->count() == 1 ? 'checked' : '' }}>
                          Single
                        </label>
                      </div>
                      <div class="me-2">
                        <label for="all" class="form-label">
                          <input type="radio"
                            name="type"
                            value="all"
                            class=" me-2"
                            id="all" {{$contact->contactAgents->count() > 1 ? 'checked' : '' }}>
                          All
                        </label>
                      </div>
                    </div>
                    @error('type')
                    <span class="text-danger">*{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="custom-form-group {{$contact->contactAgents->count() > 1 ? 'is-hide' : '' }} " id="singleAgent">
                    <label for="title">Select Agent</label>
                    <select name="agent_id" class="form-control form-select" id="">
                      @foreach (Auth::user()->agents as $agent)
                      <option
                        value="{{ $agent->id }}"
                        {{ $contact->contactAgents->contains('agent_id', $agent->id) ? 'selected' : '' }}>
                        {{ $agent->name }}
                      </option> @endforeach
                    </select>
                    @error('agent_id')
                    <span class="text-danger">*{{ $message }}</span>
                    @enderror
                  </div>
                  @endif
                  <div class="custom-form-group">
                    <button class="btn btn-primary" type="submit">Edit</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>

<script src="{{ asset('admin_app/assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('admin_app/assets/js/plugins/quill.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
  $(document).ready(function() {
    $(".is-hide").hide();
    $("#single").on("change", function() {
      console.log('here');
      $("#singleAgent").show();
    });
    $("#all").on("change", function() {
      $("#singleAgent").hide();
    });
  });
</script>
@endsection