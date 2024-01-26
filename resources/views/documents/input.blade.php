<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif !important;
            /* text-align: justify;
            text-justify: inter-word; */
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

        .right {
            text-align: right;
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

        .left {
            text-align: left;
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
                        <h4 class="center" style="font-size:16px">REGISTRO DE COMPRA</h4>
                        <h5 class="center" style="font-size:14px">{{ $data->code }}</h5>
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <footer>
        <table style="width:90%;margin:12px auto">
            <tr>
                <td style="font-size: 11px;">
                    Procedimiento de Recepción de Facturas para el pago
                    las facturas se recibirán únicamente en la dirección fiscal
                    los dias Martes Horario: 08:00 - 17:00
                    Se debe presentar: copia de la Guia de Remisión y/o copia
                    de Factura sellada por el proyecto
                </td>
                <td>
                    <div class="pagenum-container"><span class="pagenum"></span></div>
                </td>
            </tr>

        </table>
    </footer>

    <table style="width:100%; margin: 70px 0px 10px 0px; font-size:11px">
        <thead>
            <tr>
                <th width="70" class="left">Proveedor: </th>
                <td class="left">{{ $data->proveedor }}</td>
                <th width="100" class="left">Fecha de emisión: </th>
                <td style="min-width: 180px;" class="left">{{ $data->fecha }}</td>
            </tr>
            <tr>
                <th width="70" class="left">RUC:: </th>
                <td class="left">{{ $data->proveedordoc }}</td>
            </tr>
            <tr>
                <th width="70" class="left">Dirección: </th>
                <td class="left">{{ $data->dir }}</td>
            </tr>
            <tr>
                <td colspan="4">
                     <hr>
                </td>
            </tr>
           
            <tr>
                <th class="left">Transportista: </th>
                <td colspan="3">{{ $data->transportista }}</td>
            </tr>
            <tr>
                <th class="left">RUC: </th>
                <td colspan="3">{{ $data->transportistadoc }}</td>
            </tr>
            <tr>
                <td colspan="4">
                     <hr>
                </td>
            </tr>
            <tr>
                <th class="left">Nro Guia Remitente: </th>
                <td colspan="3">{{ $data->guiaRemision }}</td>
            </tr>
            <tr>
                <th class="left">°N Factura: </th>
                <td colspan="3">{{ $data->serieComprobante }} - {{ $data->correlativoComprobante }}</td>
            </tr>
            <tr>
                <th class="left">Orden de compra: </th>
                <td colspan="3">{{ $data->code2 }}</td>
            </tr>
            <tr>
                <th class="left">Observación: </th>
                <td colspan="3">{{ $data->observacion }}</td>
            </tr>
        </thead>
    </table>
    @php
    $total = 0;
    @endphp

    @foreach ($detalles as $row)
    @php
    $total += $row->subtotal;
    @endphp
    @endforeach

    @php
    $igvPorcentaje = 18;
    $igv = round(($total * $igvPorcentaje / 100), 4);
    $gravado = round(($total - $igv), 4);
    @endphp
    <table style="width:100%; margin: 20px 0px 10px 0px; font-size:11px">
        <thead>
            <tr>
                <th class="border">CANT.</th>
                <th class="border">UNIDAD</th>
                <th class="border">DESCRIPCIÓN</th>
                <th class="border">MARCA</th>
                <th class="border">P.UNIT</th>
                <th class="border">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detalles as $row)
            <tr>
                <td class="center">{{ $row->cantidad }}</td>
                <td class="center">{{ $row->unidad_nombre }}</td>
                <td>{{ $row->material }}</td>
                <td class="center">{{ $row->marca_nombre }}</td>
                <td>{{ $row->precioUnitario }}</td>
                <td>{{ number_format($row->subtotal, 4)  }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="5" class="right" style="border-top: 1px solid black;">Gravado: </th>
                <td style="border-top: 1px solid black;">&nbsp;{{ number_format($gravado, 4) }}</td>
            </tr>
            <tr>
                <th colspan="5" class="right">IGV ({{ $igvPorcentaje }}%): </th>
                <td>&nbsp;&nbsp;{{ number_format($igv, 4) }}</td>
            </tr>
            <tr>
                <th colspan="5" class="right">Total: </th>
                <td>&nbsp;{{ number_format($total, 4) }}</td>
            </tr>
        </tbody>
    </table>
    <table style="width:35%; font-size:11px;float:right">
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
        </tr>
    </table>
</body>

</html>