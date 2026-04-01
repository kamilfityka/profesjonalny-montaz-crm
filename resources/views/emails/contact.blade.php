@extends('praust::emails.layout')

@section('content')
    <h3>Witaj,</h3>
    <p class="lead">Ze strony internetowej została wysłana wiadomość.</p>
    <p style="font-style: italic">{{$userMessage}}</p>

    <!-- social & contact -->
    <table class="social" width="100%">
        <tr>
            <td>

                <!-- column 2 -->
                <table align="left" class="column">
                    <tr>
                        <td>
                            <h5 class="">Kontakt z klientem:</h5>
                            <p>Imię i Nazwisko: <strong>{{$name}}</strong></p>
                            <p>E-mail: <strong><a href="mailto:{{$email}}">{{$email}}</a></strong></p>
                            <p>Nazwa Firmy: <strong>{{$company}}</strong></p>
                            <p>Telefon: <strong>{{$phone}}</strong></p>
                        </td>
                    </tr>
                </table>
                <!-- /column 2 -->

                <span class="clear"></span>

            </td>
        </tr>
    </table><!-- /social & contact -->
@endsection
