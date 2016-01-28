@extends('app')

@section('title', 'Premium Transaktionen')

@section('content')

    <h2>Transaktionen</h2>

    @if($transactions->count() > 0)
        <table class="table">
            <caption>Premium-Status gültig bis: {{$user->premium_valid_until->format('Y-m-d')}}</caption>
            <tr>
                <th>Datum</th>
                <th class="text-right">Betrag</th>
                <th>Product</th>
                <th>Status</th>
            </tr>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{$transaction->created_at->format('Y-m-d H:i')}}</td>
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
                </tr>
            @endforeach
        </table>
    @endif

@stop