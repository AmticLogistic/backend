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
            font-size: 12px !important;
            border: 1px solid black;
        }

        .table td {
            font-size: 11px;
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

        .borderb {
            border-bottom: 1px solid black;
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
                    <h3>Ordenes de Compra de Bienes</h3>
                    <p style="font-size:11px">Detalle de atencion por Documento</p>
                    <p style="font-size:9px">Del 2022-01-01 Al 2022-12-31</p>
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
    @php
    $currentNumDoc = null;
    $totalCantidadPedida = 0;
    @endphp
    @foreach ($data as $row)
    @if ($row->numDoc != $currentNumDoc)
    @if (!is_null($currentNumDoc))
    <tr>
        <td colspan="8" style="text-align:right">Total Documento:</td>
        <td>{{ $totalCantidadPedida }}</td>
    </tr>
    </table> <!-- Cerrar la tabla anterior -->
    @endif
    <table style="width:100%;margin:40px 0px; font-size: 9px !important;">
        <thead>
            <tr>
                <th class="borderb">Tipo Docum.</th>
                <th class="borderb">Numero Docum.</th>
                <th class="borderb">Fecha Emisión</th>
                <th class="borderb">Código</th>
                <th class="borderb">Articulos o Servicio</th>
                <th class="borderb">Cantidad Pedida</th>
                <th class="borderb">Cantidad Atendida</th>
                <th class="borderb">Cantidad Pendiente</th>
                <th class="borderb" width="40">Valor Cantidad Atendida</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalCantidadPedida = 0; // Restablecer la suma para el nuevo grupo
            @endphp
            @endif
            <tr>
                <td width="60">{{ $row->tipoDoc }}</td>
                <td>{{ $row->numDoc }}</td>
                <td>{{ $row->fecha }}</td>
                <td>{{ $row->codigo }}</td>
                <td>{{ $row->material }}</td>
                <td>{{ $row->cantidadPe }}</td>
                <td>{{ $row->cantidadAt }}</td>
                <td>{{ $row->cantidadTo }}</td>
                <td>{{ $row->total }}</td>
            </tr>
            @php
            $totalCantidadPedida += $row->cantidadPe; // Sumar la cantidad pedida para el grupo
            $currentNumDoc = $row->numDoc;
            @endphp
            @endforeach
            <tr>
                <td colspan="8" style="text-align:right">Total Documento:</td>
                <td>{{ $totalCantidadPedida }}</td>
            </tr>
    </table> <!-- Cerrar la última tabla -->
</body>

</html>