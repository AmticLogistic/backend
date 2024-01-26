<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif !important;
        }

        .container {
            width: 90%;
            margin: 15px auto;
        }

        tr {
            font-family: Arial, Helvetica, sans-serif !important;
        }

        th {
            text-align: left;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 18px;
        }

        .table th {
            padding: 6px;
            font-size: 9px;
            border: 1px solid black;
        }

        .table td {
            padding: 6px;
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
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -100px;
            left: -25px;
            right: -25px;
            height: 80px;
            font-size: 9px;
        }

        .box2 {
            display: block;
            position: relative;
            border: 3px solid black;
            padding: 6px;
            margin: 20px;
            width: 200px;
            border-radius: 12px;
        }

        .border {
            border-bottom: 1px solid black;
            border-top: 1px solid black;
            font-size: 12px;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .center {
            text-align: center;
        }

        footer .pagenum:before {
            content: counter(page);
        }

        .tablex {
            width: 100%;
            border-collapse: collapse;
            margin: auto;
            font-size: 11px;
            border-radius: 10px;
            margin-top: 50px
        }

        .tablex th,
        .tablex td {
            border: 1px solid black;
            padding: 8px;
        }

        .tablex td:first-child,
        .tablex th:first-child {
            border-left: 1px solid black;
        }

        .tablex td:last-child,
        .tablex th:last-child {
            border-right: 1px solid black;
        }

        .tablex tr:last-child td {
            border-bottom: 1px solid black;
        }

        .tablex td[rowspan],
        .tablex th[rowspan] {
            border-bottom: none;
        }

        .tablex .firma-row td {
            border-bottom: 1px solid black;
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
                    <h3 style="line-height: 25px;">SERVICIOS GENERALES AMARU DEL SUR S.A.C.</h3>
                    <span style="font-size:14px">20608024167</span> <br>
                    <span style="font-size:10px">AV. CAMINO REAL NRO. 215 DPTO. 801 URB. EL ROSARIO LIMA - LIMA - SAN ISIDRO</span>
                </td>
                <td>
                    <div class="box2">
                        <h4 class="center" style="font-size:16px">REQUERIMIENTO</h4>
                        <h5 class="center" style="font-size:14px">{{ $data->code }}</h5>
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <footer>
        <table style="width:90%;margin:10px auto">
            <tr>
                <td>Generado por AMTIC.</td>
            </tr>
            <td>
            <td>
                <div class="pagenum-container">Pagina: <span class="pagenum"></span></div>
            </td>
            </td>
        </table>
    </footer>

    <table style="width:100%; margin: 100px 0px 10px 0px; font-size:11px">
        <thead>
            <tr>
                <th width="80">Solicitante: </th>
                <td>{{ $data->paterno }} {{ $data->materno }} {{ $data->nombre }}</td>
                <th width="100">Fecha de emisión: </th>
                <td style="min-width: 180px;">{{ $data->fecha }}</td>
            </tr>
            <tr>
                <th>Area Solicitante: </th>
                <td>{{ $data->areaSolicita }}</td>
            </tr>
            <tr>
                <th>Observación: </th>
                <td colspan="3">{{ $data->observacion }}</td>
            </tr>
        </thead>
    </table>
    <table style="width:100%; margin: 40px 0px 10px 0px; font-size:11px">
        <thead>
            <tr style="background-color: #ede8e8;">
                <th class="border center">CANT.</th>
                <th class="border center">UNIDAD</th>
                <th class="border">MATERIAL</th>
                <th class="border">MARCA</th>


            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $index => $row)
            <tr>
                <td class="center">{{ $row->cantidad }}</td>
                <td class="center">{{ $row->unidad_nombre }}</td>
                <td>
                    <span style="font-size: 12px;">{{ $row->material }} </span>
                    @if($row->observacion)
                    <br><strong>Observación: </strong> <span style="font-size: 9px;">{{ $row->observacion }}</span>
                    @endif


                </td>
                <td>{{ $row->marca_nombre }}</td>


            </tr>
            @endforeach
        </tbody>
    </table>
    <table style="width:70%; font-size:11px; float:right">
        <tr>
            <td style="padding: 20px;">
                <div style="border: 1px solid black; border-radius: 12px">
                    <p style="padding-left:8px;text-align:center;font-weight: 700">Revisado por:</p>
                    <hr style="padding: 0px; margin: 0px;">
                    <p style="font-weight: 600;padding-left:8px">Nombre/función</p>
                    <p style="padding-left:8px;text-align: center;">{{ $data->user }}</p>
                    <hr>
                    <p style="padding-left:8px">Firma: <br> <br><br><br>
                </div>
            </td>
            <td style="padding: 20px;">
                <div style="border: 1px solid black; border-radius: 12px">
                    <p style="padding-left:8px;text-align:center;font-weight: 700">Solicitado por:</p>
                    <hr>
                    <p style="font-weight: 600;padding-left:8px">Nombre/función</p>
                    <p style="padding-left:8px;text-align: center;font-size:9px">{{ $data->paterno }} {{ $data->materno }} <br> {{ $data->nombre }}</p>
                    <hr>
                    <p style="padding-left:8px">Firma: <br> <br><br><br>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>