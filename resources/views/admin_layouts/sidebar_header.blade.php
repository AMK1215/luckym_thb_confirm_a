 <div class="sidenav-header">
     <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
         aria-hidden="true" id="iconSidenav"></i>
     <a class="navbar-brand m-0" href="{{ url('/') }}">
     @if (!Auth::user()->hasRole('Agent') && !Auth::user()->hasRole('Sub Agent'))
         <img src="{{ $adminLogo }}" class="navbar-brand-img h-100" alt="main_logo">
    @endif
         <span class="ms-1 font-weight-bold text-white">
         {{ $siteName }}
             <span class="badge badge-success ms-2">
                 {{ Auth::user()->roles->pluck('title')->implode(', ') }}
             </span>
         </span>
     </a>
 </div>
