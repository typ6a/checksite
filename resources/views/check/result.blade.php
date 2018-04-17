@extends('master')
@section('title', 'Проверка сайта')
@section('content')
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php //pre($data['checkResult']['targetUrl'],1) ?>
            <h3> Результат проверки {{ $data['checkResult']['targetUrl'] }} </h3>
            
        </div>
        <table id="result" class="table table-bordered table-sm" name="table">
            <tbody>
                <tr>
                    <th >№</th>
                    <th>Название проверки</th>
                    <th>Статус</th>
                    <th>&nbsp;</th>
                    <th >Текущее состояние</th>
                </tr>
                <?php $show8 = true; ?>
                @foreach ($data['metrics'] as $metric_key => $metric)
                    <?php if($metric_key === \App\Enum\CheckMetrics::HOST_EXISTS) { ?>
                        <?php if(!$data['checkResult'][$metric_key]['status']): ?>
                            <?php $show8 = false; ?>
                        <?php endif; ?>
                    <?php } ?>
                    <?php if($metric_key === \App\Enum\CheckMetrics::HOST_COUNT && !$show8) { ?>
                        @continue
                    <?php } ?>
                    <tr>
                        <td rowspan=2 >
                            {{ $metric['number'] }}
                        </td>
                        <td rowspan=2 >{{ $metric['title'] }}</td>
                        @if ($data['checkResult'][$metric_key]['status'])
                            <td class="table-success" rowspan=2 >{{ $metric['status']['ok']['title'] }}</td>
                        @else
                            <td class="table-danger" rowspan=2 >{{ $metric['status']['error']['title'] }}</td>
                        @endif
                        <td>Состояние</td>
                        @if ($data['checkResult'][$metric_key]['status'])
                            <td>{{ sprintf($metric['status']['ok']['state'], $data['checkResult'][$metric_key]['value']) }}</td>
                        @else
                            <td>{{ sprintf($metric['status']['error']['state'], $data['checkResult'][$metric_key]['value']) }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td>Рекомендация</td>
                        @if ($data['checkResult'][$metric_key]['status'])
                            <td>{{ sprintf($metric['status']['ok']['recomendation'], $data['checkResult'][$metric_key]['value']) }}</td>
                        @else
                            <td>{{ sprintf($metric['status']['error']['recomendation'], $data['checkResult'][$metric_key]['value']) }}</td>
                        @endif
                    </tr>
                    @if ($data['checkResult']['robotstxtPresents']['status'] === false)
                        @break;
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="row">
        <div class="form-group" style="margin: 15px">
            <a href="export" type="submit" class="btn btn-primary">Сохранить</a>
        </div>
        <div style="margin: 15px">
            <a href="/" type="submit" class="btn btn-Success"">Назад</a>
        </div>
        </div>    
    </div>
</div>

@endsection