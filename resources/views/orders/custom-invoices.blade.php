<div class="col-sm-12">
    <div class="form-group col-sm-12" element="div">
        <label>User Payment Link & Invoice</label>
        <input type="text" class="form-control" disabled value="{{ route('payInvoice',session('payLink')) }}">
         <br />
        <a href="{{ session('shareLink') }}" target="_blank">
        <button class="btn btn-success"  type="button" value="share">
            <i class="lab la-whatsapp"></i> share</button>
        </a>
    </div>
</div>
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
                    <input type="checkbox" @if(session('canEdit')==false) onclick="return false;" @endif  name="orderId[]" checked value="{{$order->id}}">
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
                        <input @if(session('canEdit')==false) onclick="return false;" @endif type="checkbox" name="orderId[]" value="{{$order->id}}">
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
<div class="col-sm-12">
     <table id="crudTable" class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline collapsed has-hidden-columns" aria-describedby="crudTable_info" role="grid" cellspacing="0">
        <thead>
            <tr>

                <th>
                    transaction id
                </th>
                <th>
                    refernece number
                </th>
                <th>
                    amount
                </th>

                <th>
                    Date
                </th>
            </tr>
        </thead>

        <tbody>
        @foreach(session('knetPayment') as $pay)
            <tr>

                <th>
                    {{ $pay->transaction_id }}
                </th>
                <th>
                    {{ @$pay->refernece_number  }}
                </th>
                <th>
                    {{ @$pay->amount  }}
                </th>

                <th>
                    {{ $pay->date  }}
                </th>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>

@if(session('canEditFields')==false)
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            $( document ).ready(function() {

                $('.delete-element').hide();
                $('.add-repeatable-element-button').hide();
            });
        </script>

@endif