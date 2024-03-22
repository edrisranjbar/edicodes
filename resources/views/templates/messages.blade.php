@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show text-right" role="alert">
    <strong class="d-block mb-2">لطفا خطاهای زیر را رفع کرده و دوباره تلاش کنید 👇</strong>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close py-3" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show text-right" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close py-3" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('info'))
<div class="alert alert-info alert-dismissible fade show text-right" role="alert">
    {{ session('info') }}
    <button type="button" class="btn-close py-3" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif