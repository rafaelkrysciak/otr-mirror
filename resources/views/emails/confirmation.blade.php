@extends('emails.main')

@section('title', 'User Contact')

@section('content')


    @include('emails.content_block_start')
    <h1>Hallo {{$user->name}},</h1>
    @include('emails.content_block_end')

    @include('emails.content_block_start')
    Willkommen bei HQ-Mirror! Dein unter der E-Mail-Adresse "{{$user->email}}" registriertes Benutzerkonto ist jetzt eingerichtet.
    Um es zu aktivieren, bestätige bitte deine E-Mail-Adresse:
    @include('emails.content_block_end')

    <tr style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
            <a href="{{url('auth/confirmation', ['confirmation_code' => $user->confirmation_code])}}" class="btn-primary" itemprop="url" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">Registrierung bestätigung</a>
        </td>
    </tr>

    @include('emails.content_block_start')
    Viel Spaß beim modernen Fernsehn schauen!<br><br>
    Viele Grüße
    @include('emails.content_block_end')
@stop