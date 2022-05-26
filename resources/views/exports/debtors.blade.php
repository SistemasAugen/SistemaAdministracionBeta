<table style="font-family: 'Helvetica';">
    {{-- SHEET HEADER --}}
    <tr>
        <td rowspan="5" colspan="2"></td>
        <td rowspan="5" colspan="10" style="font-weight: bold;"> {{ $title }} {{ $branch }} {{ $range }} </td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    {{-- END SHEET HEADER --}}

    {{-- SHEET BODY --}}
    @if (count($data) > 1)
        <tr>
            <th colspan="2" style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">- 1</th>
            <th></th>
            <th colspan="2" style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">- 2</th>
            <th></th>
            <th colspan="2" style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">- 3</th>
        </tr>
    @endif
    <tr>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CLIENTE</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">MONTO</th>
        @if (count($data) > 1)
            <th></th>
            <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CLIENTE</th>
            <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">MONTO</th>
            <th></th>
            <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">CLIENTE</th>
            <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">MONTO</th>
        @endif
    </tr>
    @for($i = 0; $i < max(count($data[0]), count(@$data[1] ?: []), count(@$data[2] ?: [])); $i++)
        <tr>
            @if (isset($data[0][$i]))
                @if (is_array($data[0][$i]))
                    <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $data[0][$i]['client'] }}</td>
                    <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ str_replace(',', '', number_format($data[0][$i]['amount'], 2)) }}</td>
                @else
                    <td colspan="2" style="background: #333333; border: 1px solid #333333; text-align: center; color: #ffffff;">{{ $data[0][$i] }}</td>
                @endif
            @endif
            @if (isset($data[1][$i]))
                <td></td>
                @if (is_array($data[1][$i]))
                    <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $data[1][$i]['client'] }}</td>
                    <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ str_replace(',', '', number_format($data[1][$i]['amount'], 2)) }}</td>
                @else
                    <td colspan="2" style="background: #333333; border: 1px solid #333333; text-align: center; color: #ffffff;">{{ $data[1][$i] }}</td>
                @endif
            @endif
            @if (isset($data[2][$i]))
                <td></td>
                @if (is_array($data[2][$i]))
                    <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ $data[2][$i]['client'] }}</td>
                    <td style="text-align: center; border: 1px solid black; background-color: {{ $i % 2 == 0 ? '#d8d8d8' : '#ffffff'}};">{{ str_replace(',', '', number_format($data[2][$i]['amount'], 2)) }}</td>
                @else
                    <td colspan="2" style="background: #333333; border: 1px solid #333333; text-align: center; color: #ffffff;">{{ $data[2][$i] }}</td>
                @endif
            @endif
        </tr>
    @endfor
    {{-- END SHEET BODY --}}

    {{-- SHEET FOOTER --}}
    <tr>
        <td style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: right;text-transform: uppercase;">TOTAL</td>
        <td style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">-</td>
        @if (isset($data[1]))
            <td></td>
            <td style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: right;text-transform: uppercase;">TOTAL</td>
            <td style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">-</td>
        @endif
        @if (isset($data[2]))
            <td></td>
            <td style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: right;text-transform: uppercase;">TOTAL</td>
            <td style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">-</td>
        @endif
    </tr>
    {{-- <tr>
        <td>% Desc.</td>
        <td>-</td>
    </tr>
    <tr>
        <td>IVA</td>
        <td></td>
    </tr>
    <tr>
        <td>TOTAL</td>
        <td></td>
    </tr> --}}
    {{-- END SHEET FOOTER --}}
</table>
