@extends('layouts.admin.main')
@section('content')
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            
            <!-- Default panel contents -->
            <div class="panel-heading">Filter</div>
            <div class="panel-body">
              <form id="filterForm">
              <div class="panel panel-primary">
                <div class="panel-heading">Course</div>
                <div class="panel-body">
                  <select name="course" class="form-control" onchange="setFilterNull(0)">
                    <option value=0 disabled selected>----</option>
                    @foreach($courses as $course)
                      <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <!-- List group -->
              <div class="panel panel-primary">
                <div class="panel-heading">Division/Department</div>
                <div class="panel-body">
                      <label>Division</label>
                      <select onchange="setFilterNull(1)" id="division" name="division" class="form-control" disabled>
                        <option value="all" selected>All</option>
                        @foreach($divisions as $division)
                          <option value="{{ $division->division_id }}">{{ $division->division }}</option>
                        @endforeach
                      <option value=0 disabled>----</option>
                      </select>
                      <div class="d-flex align-items-center">
                        <p class="text-center">--OR--</p>
                      </div>
                      <label>Department</label>
                      <select onchange="setFilterNull(2)" name="department" id="department" class="form-control" disabled>
                        @foreach($departments as $department)
                          <option value="{{ $department->department_id }}">{{ $department->department }}</option>
                        @endforeach
                        <option value=0 disabled selected>----</option>
                      </select>
                </div>
              </div>
              <div class="panel panel-primary">
                <div class="panel-heading">Year</div>
                <div class="panel-body">
                      <select name="year" id="year" class="form-control" disabled>
                        @foreach($years as $year)
                          <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                      </select>
                </div>
              </div>
              </form>
            </div>
            <div class="panel-footer">
              <button id="buttonFilter" disabled type="button" class="btn btn-info" onclick="filter()">Filter</button>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="panel panel-default">
            <div class="panel-heading">Summary</div>
            <div class="panel-body">
              <h4 id="panelHeading"></h4>
              <h5>Total Enrolled: <span id="totalEnrolled"></span> </h5>
              <h5>Total Finished: <span id="totalFinished"></span> </h5>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">Total Number of Employees</div>
            <div class="panel-body">
              <canvas id="myChart"></canvas>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">Percentage of Finished Employees</div>
            <div class="panel-body">
              <canvas id="myChart2"></canvas>
            </div>
          </div>

          <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Enrolled Employee List</div>
            <div class="panel-body">
              <!-- Table -->
              <table id="myTable" class="table table-striped">
                <thead>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Date Started</th>
                    <th>Date Finished</th>
                    <th>Status</th>
                </thead>
                <tbody>                
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>    
    </div>
@endsection
@section('additional_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const empListTable = $('#myTable').DataTable();
  const ctx = document.getElementById('myChart');
  const ctx2 = document.getElementById('myChart2');
  const barChartObject = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [],
      datasets: [{
        label: '# of Employees',
        data: [],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  const pieChartObject = new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: [],
      datasets: [{
        label: '# of Employees',
        data: [],
        backgroundColor: [
          'rgb(0,128,0)',
          'rgb(255, 99, 132)'
        ],
      }]
    }
  });

  function setFilterNull(flag) {
    if(flag==0) {
      $('#division').attr('disabled', false);
      $('#department').attr('disabled', false);
      $('#year').attr('disabled', false);
      $('#buttonFilter').attr('disabled', false);
    } else if(flag==1) {
      $('#panelHeading').html($('#division').find(":selected").text())
      $('#department').val(0)
    }
    else {
      $('#panelHeading').html($('#department').find(":selected").text())
      $('#division').val(0)
    }
  }

  function filter() {
    
    const filterForm = $('#filterForm').serialize()
    $.ajax({
        method: 'get',
        url: '{{ route("admin.get_dashboard") }}',
        data: filterForm
    }).done(function(response) {
      empListTable.clear().draw();
      $('#totalEnrolled').html(response.total_enrolled+" out of "+response.employee_count+" ("+response.percentage+"%)")
      $('#totalFinished').html(response.total_finished+" out of "+response.total_enrolled+" ("+response.percentage_finished+"%)")
        // $('.filterList').remove()
        var i = 1
        for(var k in response.list) {
          let name = response.list[k].lastname+', '+response.list[k].firstname
          let department = '-' 
          if(response.list[k].department)
            department = response.list[k].department
            
          empListTable.row.add(
            [response.list[k].emp_id,name,department,response.list[k].created_at,response.list[k].finished_date,null]
          ).draw();
          // let xxasd = ''
          // for(var j in response.list[k].quiz) {
          //     xxasd += '<li class="list-group-item">'+response.list[k].quiz[j].score+'</li>'
          // }

          // $('#tbl-hist').append('<tr class="filterList">'+
          //   '<td>'+i+'</td>'+
          //   '<td>'+response.list[k].emp_id+'</td>'+
          //   '<td>'+name+'</td>'+
          //   '<td>'+response.list[k].created_at+'</td>'+
          //   '<td><ul class="list-group list-group-flush">'+xxasd+'</ul></td>'+
          // '</tr>')
          // i++
        }
        barChartObject.data.datasets[0].data = [Number(response.employee_count),Number(response.total_enrolled)]
        barChartObject.data.labels = ['Total Employees','Total Enrolled']
        barChartObject.update()

        pieChartObject.data.datasets[0].data = [response.percentage_finished,100-response.percentage_finished]
        pieChartObject.data.labels = ['% Finished','% Unfinished']
        pieChartObject.update()
    })
  }
</script>
@endsection