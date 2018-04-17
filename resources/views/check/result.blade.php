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
                @endforeach
            </tbody>
        </table>
        <div>
            <a href="/" class="btn btn-primary" style="padding: 25px, 5px,25px, 5px; margin: 15px;">Назад</a>
        </div>
        <div class="form-group">
            <a href="export" type="submit" class="btn btn-primary">Сохранить</a>
        </div>

    </div>
</div>

@endsection