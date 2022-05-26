<table style="font-family: 'Helvetica';">
    {{-- SHEET HEADER --}}
    <tr>
        <td rowspan="5" colspan="6"></td>
        <td rowspan="5" colspan="6" style="font-weight: bold;"> ESTADO DE CUENTA</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    {{-- END SHEET HEADER --}}

    {{-- SHEET CUSTOMER HEADER --}}
    <tr>
        <td rowspan="3" colspan="6"></td>
        <td rowspan="3" colspan="3"><b>CLIENTE:</b>{{ $client->name }}</td>
        <td rowspan="3" colspan="3"><b>FECHA DE CONSULTA:</b> {{ date('d/m/Y') }}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    {{-- END SHEET CUSTOMER HEADER --}}

    {{-- SHEET BODY --}}
    <tr>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">RX</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Fecha</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Semanas Restantes</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Diseño</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Material</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Característica</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Antireflejante</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Plazo</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Subtotal</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Descuento Sistema</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Descuento</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Total</th>
    </tr>
    @foreach ($data as $i => $row)
        <tr>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['rx'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['fecha'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['semanas_restantes'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['diseno'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['material'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['caracteristica'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['antireflejante'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['plazo'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['subtotal'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['descuentos_sistema'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['descuento'] }}</td>
            <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $row['total'] }}</td>
        </tr>
    @endforeach
    <tr></tr>
    {{-- END SHEET BODY --}}

    {{-- SHEET FOOTER --}}
    <tr>
        <td rowspan="4" colspan="10"></td>
        <td>TOTAL</td>
        {{-- <td></td> --}}
    </tr>
    {{-- <tr> --}}
        {{-- <td>% Desc.</td> --}}
        {{-- <td>-</td> --}}
    {{-- </tr> --}}
    {{-- <tr> --}}
        {{-- <td>IVA</td> --}}
        {{-- <td></td> --}}
    {{-- </tr> --}}
    {{-- <tr> --}}
        {{-- <td>TOTAL</td> --}}
        {{-- <td></td> --}}
    {{-- </tr> --}}
    {{-- END SHEET FOOTER --}}
</table>
