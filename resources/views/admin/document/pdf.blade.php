@php
    /** @var \App\Models\Cart $cart */
@endphp
    <!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <style>
        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('theme/fonts/Maisonneue/MaisonNeue-Book.ttf')}}") format("truetype");
            font-weight: 400;
            font-style: normal
        }

        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('theme/fonts/Maisonneue/MaisonNeue-Bold.ttf')}}") format("truetype");
            font-weight: 700;
            font-style: normal
        }

        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('theme/fonts/Maisonneue/MaisonNeue-Medium.ttf')}}") format("truetype");
            font-weight: 500;
            font-style: normal
        }

        @page {
            margin: 0px 0px 0px 0px !important;
            padding: 0px 0px 0px 0px !important;
        }
        * {
            margin: 0;
            padding: 0
        }
        body {
            font-family: 'Maisonneue', sans-serif;
            font-size: 11px;
            padding: 50px 30px;
        }
        .new-page {
            page-break-after: always;
        }
        p {
            margin-bottom: 10px;
        }
        img {
            max-width: 100%;
        }
    </style>
</head>
<body>
@foreach($data->builders as $builder)
    @if($builder->type == \App\Models\Builder::BUILDER_TEXT)
        {!! $builder->text !!}
    @elseif($builder->type == \App\Models\Builder::BUILDER_IMAGE)
        <img src="{{$builder->getImage('image_0')}}" alt="" />
    @elseif($builder->type == \App\Models\Builder::BUILDER_HR)
        <div class="new-page"></div>
    @endif
@endforeach
</body>
</html>
