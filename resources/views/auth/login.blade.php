@include('user.layouts.head')
<body>
  <div class=" container-fluid" id="main">
    <div class="pt-5">
      <div class="row">
        <div class="col-md-4 offset-md-4">
          <div class="p-3 shadow border border-1 rounded-4">
            <div class="text-center mt-3">
              <img src="{{ asset('/assets/img/main_logo.png') }}" alt="" style="width: 120px; height: auto" />
            </div>
            <h4 class="text-center mt-3 text-white">Admin Login</h4>
            <form action="{{ route('login') }}" method="post" class="px-3 pb-4 pt-3">
              @csrf
              <div class="mb-3">
                <label for="login" class="form-label text-white">Username</label>
                <input type="text" name="user_name" id="login" class="form-control" placeholder="Enter username ">
                @error('user_name')
                <div class="text-light">*{{ $message }}</div>
                @enderror
              </div>
      
              <div class="mb-3">
                <label for="password" class="form-label text-white">Password</label>
                <div class="input-group border border-0 rounded-4">
                  <span class="input-group-text bg-white border border-0"><i class="fas fa-key text-purple"></i></span>
                  <input type="password" name="password" id="password" class="form-control border border-0" placeholder="Enter password">
                  <span class="input-group-text bg-white border border-0"><i class="fas fa-eye text-purple" id="eye" onclick="PwdView()" style="cursor: pointer;"></i></span>
                </div>
                @error('password')
                <div class="text-light">*{{ $message }}</div>
                @enderror
              </div>
      
              <div class="mb-3 mt-2">
                <button type="submit" class="btn btn-primary w-100">login</button>
              </div>
    
            </form>
          </div>
        </div>
      </div>
    </div>


  </div>



</body>
@include('user.layouts.js')
@yield('script')
<script>
  function PwdView() {
    var x = document.getElementById("password");
    var y = document.getElementById("eye");

    if (x.type === "password") {
      x.type = "text";
      y.classList.remove('fa-eye');
      y.classList.add('fa-eye-slash');
    } else {
      x.type = "password";
      y.classList.remove('fa-eye-slash');
      y.classList.add('fa-eye');
    }
  }
</script>