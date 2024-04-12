@extends('default')

@section('content')


    @include('prob-notice')


    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                </div>
                <h1>Prizes</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Awarded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $prize->id }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>{{ number_format($prize->awarded,0) }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id]]) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Simulate</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['simulate']]) !!}
                        <div class="form-group">
                            {!! Form::label('number_of_prizes', 'Number of Prizes') !!}
                            {!! Form::number('number_of_prizes', 50, ['class' => 'form-control']) !!}
                        </div>
                        {!! Form::submit('Simulate', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                    <br>

                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['reset']]) !!}
                        {!! Form::submit('Reset', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="container  mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Probability Settings</h2>
                <canvas id="probabilityChart"></canvas>
            </div>
            <div class="col-md-6">
                <h2>Actual Rewards</h2>
                <canvas id="awardedChart"></canvas>
            </div>
        </div>
    </div>


@stop


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
    var ctx = document.getElementById('probabilityChart').getContext('2d');
    var chartData = {!! json_encode($prizes) !!};

    var labels = [];
    var data = [];
    var backgroundColor = [];

    chartData.forEach(function(item) {
        // labels.push(item.title);
        var formattedLabel = item.title + ' (' + item.probability + '%)';
    labels.push(formattedLabel);
        data.push(item.probability);
    });

    var myChart = new Chart(ctx, {
        type: 'doughnut',
        
        data: {
            labels: labels,
            datasets: [{
                data: data,
                label: 'probability',
                backgroundColor: ['#F3AC16', '#9F9F9F', '#FF3355', '#55EE22', '#354D73', '#666633', '#553FCF'],
      
            }]
        },
        options: {
    layout: {
      padding: {
        bottom: 25
      }
    },
    plugins: {
      
      /** Imported from a question linked above. 
          Apparently Works for ChartJS V2 **/
      datalabels: {
        formatter: (value, dnct1) => {
          let sum = 0;
          let dataArr = dnct1.chart.data.datasets[0].data;
;          dataArr.map(data => {
            sum += Number(data);
          });

          let percentage = (value * 100 / sum).toFixed(2) + '%';
          return percentage;
        },
        color: '#ff3',
      }
    }
  },
  
  plugins: [ChartDataLabels]
            
    });
</script>
<script>
    // JavaScript code to render the doughnut chart
    var ctx = document.getElementById('awardedChart').getContext('2d');
    var chartData = {!! json_encode($prizes) !!};

    var labels = [];
    var data = [];
    var backgroundColor = [];

    chartData.forEach(function(item) {
    var formattedLabel = item.title + ' (' + item.awarded + '%)';
    labels.push(formattedLabel);
        data.push(item.awarded);
    });

    var myChart = new Chart(ctx, {
        type: 'doughnut',
        
        data: {
            labels: labels,
            datasets: [{
                data: data,
                label: 'awarded',
                backgroundColor: ['#0d6efd', '#6610f2', '#dc3545', '#198754', '#20c997', '#6c757d', '#0dcaf0'],
    
            }]
        },
        options: {
    layout: {
    padding: {
        bottom: 25
    }
    },
    plugins: {
    
    /** Imported from a question linked above. 
         Apparently Works for ChartJS V2 **/
    datalabels: {
        formatter: (value, dnct1) => {
        let sum = 0;
        let dataArr = dnct1.chart.data.datasets[0].data;
            dataArr.map(data => {
            sum += Number(data);
        });

        if (value > 0) {
            let percentage = value + '%';
            return percentage;
        }
        
        },
        color: '#ff3',
    }
    }
    },
    plugins: [ChartDataLabels]
            
    });
</script>

@endpush
