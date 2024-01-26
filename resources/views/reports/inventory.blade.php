<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif !important;
            text-align: justify;
            text-justify: inter-word;
        }

        .container {
            width: 90%;
            margin: 15px auto;
        }

        tr {
            font-family: Arial, Helvetica, sans-serif !important;
        }

        .table {
            border-collapse: collapse;
        }

        .table th {
            font-size: 9px;
            border: 1px solid black;
        }

        .table td {
            font-size: 9px;
            border-collapse: collapse;
            border: 1px solid black;
        }

        @page {
            margin: 100px 25px;
        }

        header {
            position: fixed;
            top: -100px;
            left: -25px;
            right: -25px;
            height: 100px;
            /** Extra personal styles **/
            /* background-color: #29323b;
            color:#f2f6fa ; */
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -100px;
            left: -25px;
            right: -25px;
            height: 80px;
            font-size: 9px;
            /** Extra personal styles **/
            /* background-color: #29323b;
            color: #f2f6fa; */
        }

        .box {
            display: block;
            max-width: 210px;
            position: relative;
            border: 1px solid black;
            padding: 12px;
            text-align: center
        }

        .box2 {
            display: block;
            position: relative;
            border: 1px solid black;
            padding: 12px;
        }

        footer .pagenum:before {
            content: counter(page);
        }
    </style>
</head>

<body>
    <header>
        <table style="width:100%;border:none">
            <tr>
                <td width="150" style="text-align:center">
                    <img src="./img/logo.png" width="90" alt="" style="margin-top:10px">

                </td>
                <td style="text-align:left;padding:0px 50px 0px 0px;line-height:14px"> <br>
                    <h3>Inventario Fisico de Articulos</h3>
                    <p style="font-size:11px">PROYECTO EL VALLE</p>
                    <p style="font-size:9px">Del {{ $init }} Al {{ $end }}</p>
                </td>
            </tr>
        </table>
    </header>
    <footer>
        <table style="width:90%;margin:10px auto">
            <tr>
                <td>Reporte generado por el sistema AMTIC.</td>
            </tr>
            <td>
            <td>
                <div class="pagenum-container">Pagina: <span class="pagenum"></span></div>
            </td>
            </td>
        </table>
    </footer>
    <table style="width:100%;margin:40px 0px; font-size: 8px !important;">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Und.</th>
                <th>Saldo Actual</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td width="60">{{ $row->id }}</td>
                <td>{{ $row->material }}</td>
                <td width="70">{{ $row->marca_nombre }}</td>
                <td>{{ $row->unidad_nombre }}</td>
                <td>{{ $row->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>