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
            border: 1px solid black;
            padding: 6px;
            margin: 20px;
            width: 200px
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
                        <h4 class="center" style="font-size:16px">PARTE DE SALIDA</h4>
                        <h5 class="center" style="font-size:14px">{{ $data->code }}</h5>
                    </div>
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

    <table style="width:100%; margin: 70px 0px 10px 0px; font-size:11px">
        <thead>
            <tr>
                <th width="100">Personal solicitante: </th>
                <td>{{ $data->nombre }} {{ $data->paterno }} {{ $data->materno }}</td>
                <th width="100">Fecha de emisión: </th>
                <td style="min-width: 180px;">{{ $data->fecha }}</td>
            </tr>
            <tr>
                <th>Centro de Costo: </th>
                <td colspan="3">{{ $data->cc }}</td>
            </tr>
            <tr>
                <th>Observaciòn: </th>
                <td>{{ $data->observacion }}</td>
            </tr>
        </thead>
    </table>
  
    <table style="width:100%; margin: 20px 0px 10px 0px; font-size:11px">
        <thead>
            <tr>
                <th class="border center">CANT.</th>
                <th class="border center">UNIDAD</th>
                <th class="border">DESCRIPCIÓN</th>
                <th class="border">MARCA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $row)
            <tr>
                <td class="center">{{ $row->cantidad }}</td>
                <td class="center">{{ $row->unidad_nombre }}</td>
                <td>
                    {{ $row->material }}
                    @if($row->observacion)
                    <br><strong>Observación: </strong> <span style="font-size: 9px;">{{ $row->observacion }}</span>
                    @endif
                </td>
                <td>{{ $row->marca_nombre }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table style="width:70%; font-size:11px;float:right">
        <tr>
            <td style="padding: 20px;width:200px">
                <div style="border: 1px solid black; border-radius: 12px">
                    <p style="padding-left:8px;text-align:center;font-weight: 700">Revisado por:</p>
                    <hr>
                    <p style="font-weight: 600;padding-left:8px">Nombre/función</p>
                    <p style="padding-left:8px;text-align: center;"> admin  <br> operador </p>
                    <hr>
                    <p style="padding-left:8px">Firma: <br> <br><br><br>
                </div>
            </td>
            <td style="padding: 20px;width:200px">
                <div style="border: 1px solid black; border-radius: 12px">
                    <p style="padding-left:8px;text-align:center;font-weight: 700">Personal Solicitante:</p>
                    <hr>
                    <p style="font-weight: 600;padding-left:8px">Nombre/función</p>
                    <p style="padding-left:8px;text-align: center;"> {{ $data->nombre }} {{ $data->paterno }} {{ $data->materno }} </p>
                    <hr>
                    <p style="padding-left:8px">Firma: <br> <br><br><br>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>