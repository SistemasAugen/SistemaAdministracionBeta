<style media="screen">
* {
    font-family: 'Helvetica';
}
.table {
    width: 100%;
    max-width: 100%;
    margin-bottom: 22px;
}

.table-client {
    border: 3px solid #000;
}

.table-client tr td:not(:last-child) {
    border-right: 2px solid #ddd;
}

.table > thead > tr > th, .table > thead > tr > td, .table > tbody > tr > th, .table > tbody > tr > td, .table > tfoot > tr > th, .table > tfoot > tr > td {
    padding: 8px;
    line-height: 1.6;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.table-rx {
    border: 3px solid #000;
}

.table-rx * {
    font-size: 11px;
}

</style>
<div id="modal_rx">
    <div class="row" v-if="selectedClient != null">
        <table class="table table-client">
            <tbody>
                <tr>
                    <td>{{ $selectedClient->name }}</td>
                    <td>{{ $selectedClient->phone }}</td>
                    <td>MON: {{ number_format($selectedClient->monofocalDiscount, 1) }} %</td>
                    <td>PAQ: {{ number_format($selectedClient->packagesDiscount, 1) }} %</td>
                </tr>
                <tr>
                    <td>{{ $selectedClient->comertial_name }}</td>
                    <td>{{ $selectedClient->email }}</td>
                    <td>BI: {{ number_format($selectedClient->bifocalDiscount, 1) }} %</td>
                    <td>TER: {{ number_format($selectedClient->finishedDiscount, 1) }} %</td>
                </tr>
                <tr>
                    <td>{{ $selectedClient->branch->name }}</td>
                    <td>PLAZO: {{ $selectedClient->payment_plan }} SEMANAS</td>
                    <td>PRO: {{ number_format($selectedClient->progresiveDiscount, 1) }} %</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table responsive table-rx">
                <thead>
                    <tr>
                        <th>RX</th>
                        <th>FC</th>
                        <th>FE</th>
                        {{-- <th>Semanas Restantes</th> --}}
                        <th>Diseño</th>
                        <th>Material</th>
                        <th>Caracteristica</th>
                        <th>Antireflejante</th>
                        <th>Plazo</th>
                        <th>Subtotal</th>
                        {{-- <th>Descuentos sistema</th> --}}
                        {{-- <th>Descuento</th> --}}
                        {{-- <th>Descuento Promoción</th> --}}
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $cart)
                        <tr>
                            <td>{{ $cart->rx }}</td>
                            <td style="font-size: 10px; width: 50px;">{{ date('Y-m-d', strtotime($cart->created_at)) }}</td>
                            <td style="font-size: 10px; width: 50px;">{{ $cart->delivered_date }}</td>
                            {{-- <td>
                                @if ($cart->status == 'pagado')
                                    0
                                @elseif ($cart->passed < 0)
                                    {{ $cart->passed }}
                                @else
                                    {{ $cart->passed }}
                                @endif
                            </td> --}}
                            <td>{{ isset($cart->product) ? $cart->product['name'] : 'NO DISPONIBLE'  }}</td>
                            <td>{{ isset($cart->product) ? $cart->product['subcategory_name'] : 'NO DISPONIBLE' }}</td>
                            <td>{{ isset($cart->product) ? $cart->product['type_name'] : 'NO DISPONIBLE' }}</td>
                            @if (count($cart->extas) > 0)
                                <td>{{ implode(', ', array_map(function($e) {
                                    return $e->name;
                                }, $cart->extras)) }}</td>
                            @else
                                <td> - </td>
                            @endif
                            <td>{{ $cart->client->payment_plan }}</td>
                            <td>{{ '$ ' . number_format($cart->total, 2) }}</td>
                            {{-- <td>{{ '$ ' . number_format($cart->discount, 2) }}</td> --}}
                            {{-- <td>{{ '$ ' . number_format($cart->discount_admin, 2) }}</td> --}}
                            {{-- <td>
                                @if ($cart->promo_discount != null)
                                    <span>$ {{ '$ ' . number_format($cart->promo_discount, 2) }}</span>
                                @else
                                    <span>N/A</span>
                                @endif
                            </td> --}}
                            @if (!$cart->cancelled)
                                <td>{{ '$ ' . number_format($cart->computedTotal, 2) }}</td>
                            @else
                                <td>{{ '$ ' . number_format(0, 2) }} </td>
                            @endif
                            <td>
                                {{ mb_strtoupper(str_replace("_", " ", $cart->status)) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
