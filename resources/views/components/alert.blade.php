{{-- resources/views/components/alert.blade.php --}}

@if(session('success'))
    <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i data-lucide="alert-circle"></i>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        <i data-lucide="alert-triangle"></i>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif