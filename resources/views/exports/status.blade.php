<table>
    <tr>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">RX<</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Fecha de captura</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Lab Origen</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Lab Destino</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Cliente</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Diseño</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Material</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Características</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Antireflejante</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Total</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Status</th>
        <th style="background-color: #000000;color: #ffffff;font-weight: bold;text-align: center;text-transform: uppercase;">Cont. Días</th>
    </tr>
    @foreach ($orders as $order)
        <tr>
            <td>{{ $order['rx'] }}</td>
            <td>{{ $order['created_at'] }}</td>
            <td>{{ $order['branch']['name'] }}</td>
            <td>{{ $order['laboratory']['name'] }}</td>
            <td>{{ $order['client']['name'] }}</td>
            <td>{{ @$order['product']['name'] ?? 'NO DISPONIBLE'  }}</td>
            <td>{{ @$order['product']['subcategory_name'] ?? 'NO DISPONIBLE' }}</td>
            <td>{{ $order['product'] ? $order['product']['type_name'] : 'NO DISPONIBLE' }}</td>
            <td>
                @if (count($order['extras']) > 0)
                    {{ implode(', ', array_map(function($e) {
                        return $e['name'];
                    }, $order['extras'])) }}
                @else
                    -
                @endif
            </td>

            <td>$ {{ number_format($order['computedTotal'], 2) }}</td>
            <td>
                {{ mb_strtoupper(str_replace("_", " ", $order['status'])) }}</button>
            </td>
            <td>{{ $order['cont_dias'] }}</td>
        </tr>
    @endforeach
</table>
