@php
    $segment = request()->segment(1); // Get the first URL segment
    $formattedSegment = strtoupper(str_replace('-', ' ', $segment));
@endphp

<div class="page-header">
    <div class="row">
        <div class="col">
            <h3 class="page-title">{{ $formattedSegment }}</h3>
            <!-- Optional Breadcrumb -->
            {{--
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $formattedSegment }}</li>
            </ul>
            --}}
        </div>
    </div>
</div>
