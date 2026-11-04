<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Kesepakatan</title>

    <style>
        @page {
            margin: 2cm 2.2cm;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9.5pt;
            line-height: 1.5;
            color: #000;
        }

        h2 {
            font-size: 12pt;
            text-align: center;
            margin: 6px 0;
        }

        h3 {
            font-size: 10.5pt;
            margin: 14px 0 6px;
            page-break-after: avoid;
        }

        p {
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px;
        }

        td, th {
            padding: 3px 5px;
            vertical-align: top;
        }

        ol, ul {
            margin: 4px 0;
            padding-left: 22px;
        }

        li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    {!! $konten !!}
</body>
</html>
