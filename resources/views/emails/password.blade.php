@extends('emails.main')

@section('content')
    @include('emails.content_block_start')
        Klicke an den folgenden Link, um dein Passwort zurück zu setzen:
    @include('emails.content_block_end')

    <tr style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
            <a href="{{ url('password/reset/'.$token) }}" class="btn-primary" itemprop="url" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">Passwort zurücksetzen</a>
        </td>
    </tr>
@stop
