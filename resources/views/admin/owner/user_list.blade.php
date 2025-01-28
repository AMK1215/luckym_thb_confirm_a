@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Player List Dashboards</h5>

          </div>
         
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="users-search">
          <thead class="thead-light">
            <th>#</th>
            <th>PlayerName</th>
            <th>PlayerId</th>
            <th>AgentName</th>
            <th>Phone</th>
            <th>Balance</th>
            <th>Status</th>
            <th>Created_at</th>
          </thead>
          @foreach ($agents[0]->createdAgents as $createdAgent)
          @foreach ($createdAgent->players as $player)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $player->name }}</td>
            <td>{{ $player->user_name }}</td>
            <td>{{$player->parent->name}}</td>
            <td>{{ $player->phone }}</td>
            <td>
              @if ($player->status == 1)
              <span class="text-success font-weight-bold">Active</span>
              @else
              <span class="text-danger font-weight-bold">Inactive</span>
              @endif
            </td>
            <td>{{ number_format($player->balanceFloat) }}</td>
            <td>{{ $player->created_at->setTimezone('Asia/Yangon')->format('d-m-Y H:i:s') }}
            </td>
          </tr>
          @endforeach
          @endforeach
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  if (document.getElementById('users-search')) {
    const dataTableSearch = new simpleDatatables.DataTable("#users-search", {
      searchable: true,
      fixedHeight: false,
      perPage: 7
    });

    document.querySelectorAll(".export").forEach(function(el) {
      el.addEventListener("click", function(e) {
        var type = el.dataset.type;

        var data = {
          type: type,
          filename: "material-" + type,
        };

        if (type === "csv") {
          data.columnDelimiter = "|";
        }

        dataTableSearch.export(data);
      });
    });
  };
</script>
<script>
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
</script>
@endsection