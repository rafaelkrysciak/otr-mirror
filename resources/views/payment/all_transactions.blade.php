@extends('app')

@section('title', 'Alle Transaktionen')

@section('content')

    <h2>Alle Transaktionen</h2>

    @if($transactions->count() > 0)
        <table class="table">
            <tr>
                <th>Datum</th>
                <th>User</th>
                <th class="text-right">Betrag</th>
                <th>Product</th>
                <th>Status</th>
                <th></th>
            </tr>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{$transaction->created_at->format('Y-m-d H:i')}}</td>
                    <td>{{$transaction->user->name}} ({{$transaction->user->premium_valid_until->format('Y-m-d')}})</td>
                    <td class="text-right">{{$transaction->amt}} {{$transaction->currencycode}}</td>
                    <td>{{$transaction->product->name}}</td>
                    <td>
                        @if($transaction->paymentstatus == 'Completed')
                            <i class="glyphicon glyphicon-ok-circle"></i>
                        @elseif($transaction->paymentstatus == 'Pending')
                            <i class="glyphicon glyphicon-hourglass"></i>
                        @elseif($transaction->paymentstatus == 'Refunded')
                            <i class="glyphicon glyphicon-erase"></i>
                        @else
                            <i class="glyphicon glyphicon-flash"></i>
                        @endif
                        {{$transaction->paymentstatus}}
                    </td>
                    <td>
                        <a href="https://{{$paypalDomain}}/cgi-bin/webscr?cmd=_view-a-trans&id={{$transaction->transactionid}}" target="_blank">
                            <i class="fa fa-university"></i>
                        </a>
                        @if($transaction->paymentstatus == 'Completed')
                            <a href="{{url('payment/refund', ['transaction_id'=>$transaction->id])}}">
                                <i class="glyphicon glyphicon-repeat"></i> Refund
                            </a>
                            <a href="{{url('payment/refund', ['transaction_id'=>$transaction->id, 'free'=>'free'])}}">
                                <i class="glyphicon glyphicon-exclamation-sign"></i> Giveaway
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        {!! $transactions->render() !!}
    @endif

@stop