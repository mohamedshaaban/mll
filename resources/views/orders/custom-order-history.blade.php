@if(session('orderChanges'))
Order Logs :
<br />
@foreach(session('orderChanges') as $change )
@if($change->text)
    <span>{{ $change->text }}</span><br />
@endif
@endforeach
@endif