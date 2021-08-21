<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="nav-icon la la-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>




<!-- Users, Roles Permissions -->
<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Authentication</a>
  <ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-group"></i> <span>Roles</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
  </ul>
</li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('orders') }}"><i class="nav-icon la la-key"></i> <span>Orders</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('comissions') }}"><i class="nav-icon la la-key"></i> <span>Comissions</span></a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cogs"></i> Configurations</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon la la-files-o"></i> <span>File manager</span></a></li>
{{--      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('log') }}"><i class="nav-icon la la-terminal"></i> <span>Logs</span></a></li>--}}
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('setting') }}"><i class="nav-icon la la-cog"></i> <span>Settings</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('areas') }}"><i class="nav-icon la la-cog"></i> <span>Areas</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('requeststatus') }}"><i class="nav-icon la la-cog"></i> <span>Order Status</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('cartypes') }}"><i class="nav-icon la la-cog"></i> <span>Car Types</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('cars') }}"><i class="nav-icon la la-cog"></i> <span>Cars</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('customers') }}"><i class="nav-icon la la-cog"></i> <span>Customers</span></a></li>
    </ul>
</li>

{{--<li class="nav-title">Demo Entities</li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('monster') }}"><i class="nav-icon la la-optin-monster"></i> <span>Monsters</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('icon') }}"><i class="nav-icon la la-info-circle"></i> <span>Icons</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('product') }}"><i class="nav-icon la la-shopping-cart"></i> <span>Products</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('fluent-monster') }}"><i class="nav-icon la la-pastafarianism"></i> <span>Fluent Monsters</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dummy') }}"><i class="nav-icon la la-poo"></i> <span>Dummies</span></a></li>--}}
