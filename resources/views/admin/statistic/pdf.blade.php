<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <style>
        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('Maisonneue/MaisonNeue-Book.ttf')}}") format("truetype");
            font-weight: 400;
            font-style: normal
        }

        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('Maisonneue/MaisonNeue-Bold.ttf')}}") format("truetype");
            font-weight: 700;
            font-style: normal
        }

        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('Maisonneue/MaisonNeue-Medium.ttf')}}") format("truetype");
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

        .new-page {
            page-break-after: always;
        }

        body {
            font-family: 'Maisonneue', sans-serif;
            font-size: 11px;
            padding: 50px 15px;
        }

        h1, .h1 {
            line-height: 1.6rem;
        }
        h4, .h4 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 10px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        td.r, th.r {
            text-align: right;
        }

        td.l, th.l {
            text-align: left;
        }

        td.c, th.c {
            text-align: center;
        }

        td.t, th.t {
            vertical-align: top;
        }

        td.b, th.b {
            vertical-align: bottom;
        }

        td.m, th.m {
            vertical-align: middle;
        }

        table {
            width: 100%;
            margin-bottom: 40px;
            border-collapse: collapse;
        }

        table td, table th {
            padding: 5px;
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    @include('admin.statistic.partials.tables')
</body>
</html>
