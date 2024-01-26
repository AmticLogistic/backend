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

        .borderb {
            border-bottom: 1px solid black;
        }
        .borderb2 {
            border-bottom: 1px dashed black;
        }
        .bordert {
            border-top: 1px solid black;
        }
        .center{
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
                    <h3>KARDEX FISICO</h3>
                    <span style="font-size:11px">PROYECTO EL VALLE</span>
                    <span style="font-size:9px">Del {{ $init }} Al {{ $end }}</span>
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
    <?php
    $currentMaterial = null; // Material actual
    ?>
    @foreach ($data as $row)
    @if ($currentMaterial !== $row->material)
    @if ($currentMaterial !== null)
    </table> <!-- Cerrar tabla anterior si no es la primera -->
    @endif
    <table style="width:100%;margin:40px 0px 0px 0px;font-size:9px">
        <thead>
            <tr style="background-color:#94CEF2">
                <th>Articulo :</th>
                <td colspan="3">{{ $row->material }}</td>
                <th>Codigo :</th>
                <td colspan="3">{{ $row->codigo }}</td>
            </tr>
            <tr>
                <th>Marca :</th>
                <td colspan="8">{{ $row->marca }}</td>
            </tr>
            <tr>
                <th>Unidad :</th>
                <td colspan="8">{{ $row->unidad }}</td>
            </tr>
            <tr>
                <th rowspan="2" class="borderb bordert" style="width: 90px;">Fecha</th>
                <th rowspan="2" class="borderb bordert">Tipo de Doc.</th>
                <th rowspan="2" class="borderb bordert"># Doc.</th>
                <th rowspan="2" class="borderb bordert">Concepto</th>
                <th rowspan="2" class="borderb bordert">RUC/DNI</th>
                <th colspan="3" class="borderb bordert center">Cantidades</th>
            </tr>
            <tr>
                <th class="borderb center">Entrada</th>
                <th class="borderb center">Salida</th>
                <th class="borderb center">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @endif
            <tr>
                <td class="borderb2">{{ $row->fecha }}</td>
                <td class="borderb2">{{ $row->tipoDocumento }}</td>
                <td class="borderb2">{{ $row->nDocumento }}</td>
                <td class="borderb2">{{ $row->concepto }}</td>
                <td class="borderb2">{{ $row->doc }}</td>
                <td class="borderb2" style="width: 80px;">{{ $row->entrada }}</td>
                <td class="borderb2" style="width: 80px;">{{ $row->salida }}</td>
                <td class="borderb2" style="width: 80px;">{{ $row->saldo }}</td>
            </tr>
            <?php $currentMaterial = $row->material; ?>
            @endforeach
    </table> <!-- Cerrar la última tabla -->
</body>

</html>