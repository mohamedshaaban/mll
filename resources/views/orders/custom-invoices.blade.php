<div class="col-sm-12">
    <table id="crudTable" class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed has-hidden-columns" aria-describedby="crudTable_info" role="grid" cellspacing="0">
        <thead>
            <tr>
                <th>

                </th>
                <th>
                    Order ID
                </th>
                <th>
                    From
                </th>
                <th>
                    To
                </th>
                <th>
                    Payment Type
                </th>
                <th>
                    Date
                </th>
            </tr>
        </thead>

        <tbody>
        @foreach(session('invoice') as $order)
            <tr>
                <th>
                    <input type="checkbox" name="orderId[]" checked value="{{$order->id}}">
                </th>
                <th>
                    {{ $order->invoice_unique_id }}
                </th>
                <th>
                    {{ @$order->areafrom->name }}
                </th>
                <th>
                    {{ @$order->areato->name }}
                </th>
                <th>
                    {{ $order->paymenttext  }}
                </th>
                <th>
                    {{ $order->date  }}
                </th>
            </tr>
        @endforeach
            @foreach(session('orders') as $order)
                <tr>
                    <th>
                        <input type="checkbox" name="orderId[]" value="{{$order->id}}">
                    </th>
                    <th>
                        {{ $order->invoice_unique_id }}
                    </th>
                    <th>
                        {{ @$order->areafrom->name }}
                    </th>
                    <th>
                        {{ @$order->areato->name }}
                    </th>
                    <th>
                        {{ $order->paymenttext  }}
                     </th>
                    <th>
                        {{ $order->date  }}
                    </th>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>